<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Indikator;
use App\Models\Target;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TargetCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = Admin::create(['name' => 'Admin', 'username' => 'admin', 'password' => Hash::make('admin12345')]);
        $this->actingAs($admin, 'admin');
    }

    public function test_admin_can_access_target_create_page(): void
    {
        Indikator::create(['nama_indikator' => 'Indeks Pembangunan Manusia', 'satuan' => 'Point', 'jenis' => 'utama']);

        $response = $this->get(route('target.create'));

        $response->assertOk();
        $response->assertSee('Tambah Data Target');
        $response->assertSee('Indeks Pembangunan Manusia');
    }

    public function test_admin_can_list_search_and_filter_targets(): void
    {
        $indicator = Indikator::create(['nama_indikator' => 'Indeks Pembangunan Manusia', 'satuan' => 'Point', 'jenis' => 'utama']);
        Target::create(['indikator_id' => $indicator->id, 'jenis_target' => 'RPJMD', 'periode' => '2017-2022', 'tahun' => 2022, 'nilai_target' => 78.5]);
        Target::create(['indikator_id' => $indicator->id, 'jenis_target' => 'RKPD', 'periode' => '2025', 'tahun' => 2025, 'nilai_target' => 80]);

        $this->get(route('target.index', ['search' => 'pembangunan', 'jenis_target' => 'RPJMD', 'tahun' => 2022]))
            ->assertOk()->assertSee('2017-2022')->assertDontSee('80.000000');
    }

    public function test_admin_can_create_update_and_delete_target(): void
    {
        $indicator = Indikator::create(['nama_indikator' => 'Inflasi', 'satuan' => '%', 'jenis' => 'utama']);
        $payload = ['indikator_id' => $indicator->id, 'jenis_target' => 'RKPD', 'periode' => '2025', 'tahun' => 2025, 'nilai_target' => 3.12345, 'sumber' => 'Bapperida'];

        $this->post(route('target.store'), $payload)->assertRedirect(route('target.index'));
        $target = Target::firstOrFail();
        $this->assertSame('3.123450', $target->nilai_target);

        $this->put(route('target.update', $target), [...$payload, 'nilai_target' => 3.5, 'periode' => '2025-revisi'])
            ->assertRedirect(route('target.index'));
        $this->assertDatabaseHas('target', ['id' => $target->id, 'periode' => '2025-revisi']);

        $this->delete(route('target.destroy', $target))->assertRedirect(route('target.index'));
        $this->assertDatabaseMissing('target', ['id' => $target->id]);
    }

    public function test_duplicate_target_is_rejected(): void
    {
        $indicator = Indikator::create(['nama_indikator' => 'Inflasi', 'jenis' => 'utama']);
        $payload = ['indikator_id' => $indicator->id, 'jenis_target' => 'RKPD', 'periode' => '2025', 'tahun' => 2025];
        Target::create($payload);

        $this->from(route('target.create'))->post(route('target.store'), $payload)
            ->assertRedirect(route('target.create'))
            ->assertSessionHasErrors(['periode']);
        $this->assertDatabaseCount('target', 1);
    }

    public function test_target_requires_admin_authentication(): void
    {
        auth('admin')->logout();
        $this->get(route('target.index'))->assertRedirect(route('login'));
    }

    public function test_target_accepts_decimal_comma(): void
    {
        $indicator = Indikator::create(['nama_indikator' => 'Inflasi', 'jenis' => 'utama']);

        $this->post(route('target.store'), [
            'indikator_id' => $indicator->id,
            'jenis_target' => 'RKPD',
            'periode' => '2025',
            'tahun' => 2025,
            'nilai_target' => '22,08',
        ])->assertRedirect(route('target.index'));

        $this->assertDatabaseHas('target', [
            'indikator_id' => $indicator->id,
            'nilai_target' => '22.080000',
        ]);

        $this->get(route('target.index'))
            ->assertOk()
            ->assertSee('name="nilai_target" type="text" inputmode="decimal" value="22,08"', false)
            ->assertSee('id="delete-confirm-modal"', false)
            ->assertSee('data-confirm-title="Hapus data target?"', false);
    }

    public function test_target_accepts_range_value_format(): void
    {
        $indicator = Indikator::create(['nama_indikator' => 'Tingkat Kemiskinan', 'jenis' => 'utama']);

        $this->post(route('target.store'), [
            'indikator_id' => $indicator->id,
            'jenis_target' => 'RPJMD',
            'periode' => '2025-2029',
            'tahun' => 2025,
            'nilai_target' => '9,2-10,16',
            'sumber' => 'BPS Kota Tasikmalaya',
        ])->assertRedirect(route('target.index'));

        $this->assertDatabaseHas('target', [
            'indikator_id' => $indicator->id,
            'tahun' => 2025,
            'jenis_target' => 'RPJMD',
            'nilai_target' => '9.680000',
        ]);
    }

    public function test_add_target_modal_starts_with_empty_values_when_targets_exist(): void
    {
        $indicator = Indikator::create(['nama_indikator' => 'Indeks Kesehatan', 'jenis' => 'utama']);
        Target::create([
            'indikator_id' => $indicator->id,
            'jenis_target' => 'RKPD',
            'periode' => 'Tahunan',
            'tahun' => 2024,
            'nilai_target' => 81.96,
            'sumber' => 'BPS Kota Tasikmalaya',
            'keterangan' => 'Target lama',
        ]);

        $this->get(route('target.index'))
            ->assertOk()
            ->assertSee('id="nilai_target" name="nilai_target" type="text" inputmode="decimal" value=""', false)
            ->assertSee('id="sumber" name="sumber" value=""', false)
            ->assertSee('<textarea id="keterangan" name="keterangan"></textarea>', false);
    }

    public function test_target_form_renders_modal_cancel_button(): void
    {
        $this->get(route('target.index'))
            ->assertOk()
            ->assertSee('class="cancel-btn" data-close-modal', false)
            ->assertSee('Batal');

        $this->get(route('target.create'))
            ->assertOk()
            ->assertSee('class="cancel-btn" data-close-modal', false)
            ->assertSee('Batal');
    }
}
