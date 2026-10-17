<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Capaian;
use App\Models\Indikator;
use App\Models\Target;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = Admin::create(['name' => 'Admin', 'username' => 'admin', 'password' => Hash::make('admin12345')]);
        $this->actingAs($admin, 'admin');
    }

    public function test_dashboard_shows_database_summary_and_selected_year_data(): void
    {
        $indicator = Indikator::create(['nama_indikator' => 'Inflasi', 'satuan' => '%', 'jenis' => 'utama']);
        Indikator::create(['nama_indikator' => 'Komponen Inflasi', 'jenis' => 'komponen']);
        Capaian::create(['indikator_id' => $indicator->id, 'tahun' => 2024, 'nilai' => 2.25]);
        Target::create(['indikator_id' => $indicator->id, 'jenis_target' => 'RKPD', 'periode' => '2024', 'tahun' => 2024, 'nilai_target' => 3]);

        $this->get(route('dashboard', ['tahun' => 2024]))
            ->assertOk()
            ->assertSee('Data indikator')
            ->assertSee('Data capaian')
            ->assertSee('Data target')
            ->assertSee('Inflasi')
            ->assertSee('2,25')
            ->assertSee('Target: 3,00')
            ->assertSee('Belum Tercapai')
            ->assertSee('Data tahun 2024');
    }

    public function test_dashboard_uses_latest_capaian_year_and_shows_missing_values_as_dashes(): void
    {
        Indikator::create(['nama_indikator' => 'Indikator Tanpa Data', 'jenis' => 'utama']);
        $indicator = Indikator::create(['nama_indikator' => 'Indikator Terbaru', 'jenis' => 'utama']);
        Capaian::create(['indikator_id' => $indicator->id, 'tahun' => 2025, 'nilai' => 78.5]);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Data tahun 2025')
            ->assertSee('Tahun terbaru')
            ->assertSee('Indikator Tanpa Data');
    }

    public function test_dashboard_requires_admin_authentication(): void
    {
        auth('admin')->logout();
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_dashboard_filter_by_indicator_and_year(): void
    {
        $indicator = Indikator::create(['nama_indikator' => 'Inflasi', 'satuan' => '%', 'jenis' => 'utama']);
        Capaian::create(['indikator_id' => $indicator->id, 'tahun' => 2020, 'nilai' => 2.1]);
        Target::create(['indikator_id' => $indicator->id, 'jenis_target' => 'RKPD', 'periode' => '2020', 'tahun' => 2020, 'nilai_target' => 3]);

        $this->get(route('dashboard', [
            'indikator' => $indicator->id,
            'tahun' => 2020,
        ]))->assertOk()
            ->assertSee('Inflasi')
            ->assertSee('Data tahun 2020');
    }
}
