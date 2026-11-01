<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Capaian;
use App\Models\Indikator;
use App\Models\Target;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class IndikatorDetailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = Admin::create(['name' => 'Admin', 'username' => 'admin', 'password' => Hash::make('admin12345')]);
        $this->actingAs($admin, 'admin');
    }

    public function test_admin_can_view_indicator_detail_with_history_chart_and_targets(): void
    {
        $indicator = Indikator::create(['nama_indikator' => 'Indeks Pembangunan Manusia', 'satuan' => 'Point', 'jenis' => 'utama', 'deskripsi' => 'Indikator pembangunan.']);
        Capaian::create(['indikator_id' => $indicator->id, 'tahun' => 2005, 'nilai' => 68.2]);
        Capaian::create(['indikator_id' => $indicator->id, 'tahun' => 2025, 'nilai' => 78.5]);
        Target::create(['indikator_id' => $indicator->id, 'jenis_target' => 'RPJMD', 'periode' => '2017-2022', 'tahun' => 2022, 'nilai_target' => 77, 'sumber' => 'Bapperida']);
        Target::create(['indikator_id' => $indicator->id, 'jenis_target' => 'RPJPD', 'periode' => 'Tahap I', 'tahun' => null, 'nilai_target' => 80]);

        $this->get(route('indikator.show', $indicator))->assertOk()
            ->assertSee('Indeks Pembangunan Manusia')
            ->assertSee('indikatorDetailChart', false)
            ->assertSee('2005')
            ->assertSee('2017-2022')
            ->assertSee('Tahap I')
            ->assertSee('Data capaian')
            ->assertSee('Data target')
            ->assertSee('>2<', false)
            ->assertSee('Riwayat capaian dan target tahunan 2005 - 2025.');
    }

    public function test_indicator_chart_only_spans_available_data_years(): void
    {
        $indicator = Indikator::create(['nama_indikator' => 'Harapan Lama Sekolah', 'satuan' => 'Tahun', 'jenis' => 'subindikator']);
        Capaian::create(['indikator_id' => $indicator->id, 'tahun' => 2016, 'nilai' => 13.40]);
        Capaian::create(['indikator_id' => $indicator->id, 'tahun' => 2025, 'nilai' => 13.51]);
        Target::create(['indikator_id' => $indicator->id, 'jenis_target' => 'RPJMD', 'tahun' => 2024, 'nilai_target' => 13.52]);

        $response = $this->get(route('indikator.show', $indicator))->assertOk();
        $response->assertSee('Riwayat capaian dan target tahunan 2016 - 2025.');
        $response->assertSee('[2016,2017,2018,2019,2020,2021,2022,2023,2024,2025]', false);
        $response->assertDontSee('[2005,', false);
    }

    public function test_detail_shows_empty_messages_when_data_is_missing(): void
    {
        $indicator = Indikator::create(['nama_indikator' => 'Indikator Kosong', 'jenis' => 'utama']);

        $this->get(route('indikator.show', $indicator))->assertOk()
            ->assertSee('Belum tersedia data capaian untuk indikator ini.')
            ->assertSee('Belum tersedia data target untuk indikator ini.');
    }

    public function test_missing_indicator_returns_404_and_guest_is_redirected(): void
    {
        $this->get('/indikator/999999')->assertNotFound();
        auth('admin')->logout();
        $this->get('/indikator/999999')->assertRedirect(route('login'));
    }

    public function test_detail_route_resolves_ipm_slug_and_id_and_returns_404_for_invalid(): void
    {
        $indicator = Indikator::create([
            'nama_indikator' => 'Indeks Pembangunan Manusia',
            'satuan' => 'Point',
            'jenis' => 'utama',
        ]);

        // Resolving by slug 'ipm'
        $this->get('/detail-indikator/ipm')
            ->assertOk()
            ->assertSee('Indeks Pembangunan Manusia');

        // Resolving by ID
        $this->get('/detail-indikator/' . $indicator->id)
            ->assertOk()
            ->assertSee('Indeks Pembangunan Manusia');

        // Resolving invalid slug
        $this->get('/detail-indikator/non-existent-slug-xyz')
            ->assertNotFound();
    }
}
