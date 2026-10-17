<?php

namespace Tests\Feature;

use App\Models\Capaian;
use App\Models\Indikator;
use App\Models\Target;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalisisGrowthProjectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_analysis_page_displays_growth_and_projection_sections_for_selected_indicator(): void
    {
        $admin = \App\Models\Admin::create([
            'name' => 'Admin',
            'username' => 'admin',
            'password' => \Illuminate\Support\Facades\Hash::make('admin12345'),
        ]);
        $this->actingAs($admin, 'admin');

        $indicator = Indikator::create([
            'nama_indikator' => 'Indeks Pembangunan Manusia',
            'satuan' => 'Index',
            'jenis' => 'utama',
        ]);

        foreach ([2020 => 70.0, 2021 => 71.5, 2022 => 73.0, 2023 => 74.8, 2024 => 76.2, 2025 => 77.0] as $year => $value) {
            Capaian::create([
                'indikator_id' => $indicator->id,
                'tahun' => $year,
                'nilai' => $value,
                'sumber' => 'BPS',
            ]);
        }

        Target::create([
            'indikator_id' => $indicator->id,
            'jenis_target' => 'RKPD',
            'periode' => '2025',
            'tahun' => 2025,
            'nilai_target' => 78.0,
            'sumber' => 'BPS',
        ]);

        $response = $this->get(route('analisis.index', [
            'indikator' => $indicator->id,
            'tahun_mulai' => 2020,
            'tahun_akhir' => 2025,
        ]));

        $response->assertOk();
        $response->assertSee('Tren Pertumbuhan');
        $response->assertSee('Proyeksi');
        $response->assertSee('Rata-rata Pertumbuhan');
        $response->assertSee('2026');
    }
}
