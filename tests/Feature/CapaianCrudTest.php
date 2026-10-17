<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Capaian;
use App\Models\Indikator;
use App\Models\Target;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CapaianCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = Admin::create(['name' => 'Admin', 'username' => 'admin', 'password' => Hash::make('admin12345')]);
        $this->actingAs($admin, 'admin');
    }

    public function test_admin_can_list_and_filter_capaians(): void
    {
        $indicator = Indikator::create(['nama_indikator' => 'Inflasi', 'jenis' => 'utama']);
        Capaian::create(['indikator_id' => $indicator->id, 'tahun' => 2020, 'nilai' => 11.063]);
        Capaian::create(['indikator_id' => $indicator->id, 'tahun' => 2021, 'nilai' => 11.06]);

        $this->get(route('capaian.index', ['indikator_id' => $indicator->id, 'tahun' => 2020]))
            ->assertOk()
            ->assertSee('11,063')
            ->assertDontSee('11,060')
            ->assertSee('value="11,063"', false);

        $this->get(route('capaian.index', ['indikator_id' => $indicator->id, 'tahun' => 2021]))
            ->assertOk()
            ->assertSee('11,06')
            ->assertDontSee('11,060')
            ->assertSee('value="11,06"', false);
    }

    public function test_admin_can_create_update_and_delete_capaian(): void
    {
        $indicator = Indikator::create(['nama_indikator' => 'Inflasi', 'jenis' => 'utama']);
        $payload = ['indikator_id' => $indicator->id, 'tahun' => 2020, 'nilai' => 2.5, 'sumber' => 'BPS'];

        $this->post(route('capaian.store'), $payload)->assertRedirect(route('capaian.index'));
        $capaian = Capaian::firstOrFail();
        $returnFilters = ['search' => 'Inflasi', 'indikator_id' => $indicator->id, 'tahun' => 2020, 'page' => 2];
        $this->put(route('capaian.update', $capaian), [
            ...$payload,
            'nilai' => 3.2,
            '_return_search' => $returnFilters['search'],
            '_return_indikator_id' => $returnFilters['indikator_id'],
            '_return_tahun' => $returnFilters['tahun'],
            '_return_page' => $returnFilters['page'],
        ])->assertRedirect(route('capaian.index', $returnFilters));
        $this->assertDatabaseHas('capaian', ['id' => $capaian->id, 'nilai' => '3.200000']);
        $this->delete(route('capaian.destroy', $capaian))->assertRedirect(route('capaian.index'));
        $this->assertDatabaseMissing('capaian', ['id' => $capaian->id]);
    }

    public function test_capaian_validation_rejects_invalid_year(): void
    {
        $indicator = Indikator::create(['nama_indikator' => 'Inflasi', 'jenis' => 'utama']);
        $this->from(route('capaian.create'))->post(route('capaian.store'), [
            'indikator_id' => $indicator->id, 'tahun' => 2101,
        ])->assertRedirect(route('capaian.create'))->assertSessionHasErrors('tahun');
    }

    public function test_capaian_target_accepts_decimal_comma(): void
    {
        $indicator = Indikator::create(['nama_indikator' => 'Inflasi', 'jenis' => 'utama']);

        $this->post(route('capaian.store'), [
            'indikator_id' => $indicator->id,
            'tahun' => 2020,
            'target' => '22,08',
        ])->assertRedirect(route('capaian.index'));

        $this->assertDatabaseHas('target', [
            'indikator_id' => $indicator->id,
            'tahun' => 2020,
            'jenis_target' => 'RKPD',
            'nilai_target' => '22.080000',
        ]);

        $this->get(route('capaian.index'))
            ->assertOk()
            ->assertSee('name="target" type="text" inputmode="decimal" value="22,08"', false)
            ->assertSee('id="delete-confirm-modal"', false)
            ->assertSee('data-confirm-title="Hapus data capaian?"', false);
    }

    public function test_capaian_target_accepts_range_value_format(): void
    {
        $indicator = Indikator::create(['nama_indikator' => 'Tingkat Kemiskinan', 'jenis' => 'utama']);

        $this->post(route('capaian.store'), [
            'indikator_id' => $indicator->id,
            'tahun' => 2025,
            'nilai' => 10.84,
            'target' => '9,2-10,16',
            'sumber' => 'BPS Kota Tasikmalaya',
        ])->assertRedirect(route('capaian.index'));

        $this->assertDatabaseHas('target', [
            'indikator_id' => $indicator->id,
            'tahun' => 2025,
            'jenis_target' => 'RKPD',
            'nilai_target' => '9.680000',
        ]);
    }

    public function test_clearing_target_when_updating_capaian_deletes_saved_target(): void
    {
        $indicator = Indikator::create(['nama_indikator' => 'Inflasi', 'jenis' => 'utama']);
        $capaian = Capaian::create(['indikator_id' => $indicator->id, 'tahun' => 2020, 'nilai' => 2.5]);
        $target = Target::create([
            'indikator_id' => $indicator->id,
            'tahun' => 2020,
            'jenis_target' => 'RKPD',
            'periode' => 'Tahunan',
            'nilai_target' => 22.08,
        ]);

        $this->put(route('capaian.update', $capaian), [
            'indikator_id' => $indicator->id,
            'tahun' => 2020,
            'nilai' => 2.5,
            'target' => '',
        ])->assertRedirect(route('capaian.index'));

        $this->assertDatabaseMissing('target', ['id' => $target->id]);
    }

    public function test_capaian_resolves_rkpd_target_over_rpjmd_when_both_exist(): void
    {
        $indicator = Indikator::create(['nama_indikator' => 'Kemiskinan', 'jenis' => 'utama']);
        $capaian = Capaian::create(['indikator_id' => $indicator->id, 'tahun' => 2022, 'nilai' => 12.34]);

        // Create RPJMD target first (e.g. 50.00)
        Target::create([
            'indikator_id' => $indicator->id,
            'tahun' => 2022,
            'jenis_target' => 'RPJMD',
            'periode' => '2017-2022',
            'nilai_target' => 50.00,
        ]);

        // Create RKPD target (e.g. 75.00)
        Target::create([
            'indikator_id' => $indicator->id,
            'tahun' => 2022,
            'jenis_target' => 'RKPD',
            'periode' => 'Tahunan',
            'nilai_target' => 75.00,
        ]);

        // Expect RKPD target (75,00) to be shown in table and in edit modal instead of RPJMD (50,00)
        $response = $this->get(route('capaian.index', ['indikator_id' => $indicator->id, 'tahun' => 2022]));
        $response->assertOk();
        $response->assertSee('75,00');
        $response->assertSee('value="75,00"', false);
    }

    public function test_capaian_form_renders_modal_cancel_button(): void
    {
        $this->get(route('capaian.index'))
            ->assertOk()
            ->assertSee('class="cancel-btn" data-close-modal', false)
            ->assertSee('Batal');

        $this->get(route('capaian.create'))
            ->assertOk()
            ->assertSee('class="cancel-btn" data-close-modal', false)
            ->assertSee('Batal');
    }
}
