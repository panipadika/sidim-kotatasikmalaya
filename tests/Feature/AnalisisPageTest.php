<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalisisPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_analysis_page(): void
    {
        $admin = Admin::create([
            'name' => 'Admin Test',
            'username' => 'admin',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->actingAs($admin, 'admin')->get('/analisis');

        $response->assertOk();
        $response->assertSee('Analisis Indikator Pembangunan');
    }

    public function test_invalid_indicator_parameter_does_not_crash_page(): void
    {
        $admin = Admin::create([
            'name' => 'Admin Test',
            'username' => 'admin2',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->actingAs($admin, 'admin')->get('/analisis?indikator=999999');

        $response->assertOk();
    }

    public function test_reset_state_does_not_show_previous_analysis(): void
    {
        $admin = Admin::create([
            'name' => 'Admin Reset',
            'username' => 'admin-reset',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('analisis.index'));

        $response->assertOk()
            ->assertSee('Silakan pilih indikator dan rentang tahun untuk melihat analisis.')
            ->assertDontSee('Informasi Indikator');
    }
}
