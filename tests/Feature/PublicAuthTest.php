<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Capaian;
use App\Models\Indikator;
use App\Models\Target;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PublicAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_download_template_contains_existing_system_data(): void
    {
        $indikator = Indikator::create([
            'nama_indikator' => 'Indeks Pembangunan Manusia',
            'satuan' => '%',
            'jenis' => 'utama',
        ]);

        Capaian::create([
            'indikator_id' => $indikator->id,
            'tahun' => 2025,
            'nilai' => 76.20,
            'sumber' => 'BPS Kota Tasikmalaya',
            'keterangan' => 'Data awal',
        ]);

        Target::create([
            'indikator_id' => $indikator->id,
            'jenis_target' => 'RPJMD',
            'periode' => 'Tahunan',
            'tahun' => 2025,
            'nilai_target' => 78.50,
            'sumber' => 'BPS Kota Tasikmalaya',
            'keterangan' => 'Target awal',
        ]);

        $rows = (new \App\Exports\CapaianTemplateExport())->array();

        $this->assertNotEmpty($rows);
        $this->assertContains(['nama_indikator', 'satuan', 'tahun', 'target', 'capaian', 'sumber', 'keterangan'], $rows);
        $this->assertContains([
            'Indeks Pembangunan Manusia',
            '%',
            2025,
            78.50,
            76.20,
            'BPS Kota Tasikmalaya',
            'Data awal',
        ], $rows);
    }

    public function test_public_user_can_register_and_login(): void
    {
        $response = $this->post(route('public.register.submit'), [
            'name' => 'Publik User',
            'email' => 'publik@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('public.login'));
        $response->assertSessionHas('status', 'Akun berhasil dibuat. Silakan login untuk melanjutkan.');
        $this->assertGuest('web');
        $this->assertDatabaseHas('user', [
            'email' => 'publik@example.com',
            'role' => 'public',
        ]);

        auth('web')->logout();

        $loginResponse = $this->post(route('public.login.submit'), [
            'email' => 'publik@example.com',
            'password' => 'password123',
        ]);

        $loginResponse->assertRedirect(route('public.dashboard'));
        $this->assertAuthenticated('web');
    }

    public function test_public_user_can_only_access_public_pages(): void
    {
        $user = User::create([
            'name' => 'Publik User',
            'email' => 'publik@example.com',
            'password' => Hash::make('password123'),
            'role' => 'public',
        ]);

        $this->actingAs($user, 'web');

        $this->get(route('public.dashboard'))->assertOk();
        $this->get(route('public.analisis'))->assertOk();
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('indikator.index'))->assertRedirect(route('login'));
    }

    public function test_admin_dashboard_links_use_admin_detail_and_analysis_routes(): void
    {
        $admin = Admin::create([
            'name' => 'Admin Utama',
            'username' => 'admin',
            'password' => Hash::make('password123'),
        ]);

        $indikator = Indikator::create([
            'nama_indikator' => 'Indeks Pembangunan Manusia',
            'satuan' => 'Point',
            'jenis' => 'utama',
        ]);

        Capaian::create([
            'indikator_id' => $indikator->id,
            'tahun' => 2025,
            'nilai' => 76.2,
            'sumber' => 'BPS',
        ]);

        $this->actingAs($admin, 'admin');

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('href="' . route('indikator.show', $indikator) . '"', false)
            ->assertSee('href="' . route('analisis.index') . '"', false);
    }

    public function test_analysis_form_routes_follow_the_correct_role_context(): void
    {
        $admin = Admin::create([
            'name' => 'Admin Utama',
            'username' => 'admin',
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($admin, 'admin');

        $this->get(route('analisis.index'))
            ->assertOk()
            ->assertSee('action="' . route('analisis.index') . '"', false);

        $publicUser = User::create([
            'name' => 'Publik User',
            'email' => 'publik2@example.com',
            'password' => Hash::make('password123'),
            'role' => 'public',
        ]);

        $this->actingAs($publicUser, 'web');

        $this->get(route('public.analisis'))
            ->assertOk()
            ->assertSee('action="' . route('public.analisis') . '"', false);
    }

    public function test_dashboard_year_filter_submits_to_the_correct_role_context(): void
    {
        $admin = Admin::create([
            'name' => 'Admin Utama',
            'username' => 'admin',
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($admin, 'admin');

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('action="' . route('dashboard') . '"', false);

        $publicUser = User::create([
            'name' => 'Publik User',
            'email' => 'publik3@example.com',
            'password' => Hash::make('password123'),
            'role' => 'public',
        ]);

        $this->actingAs($publicUser, 'web');

        $this->get(route('public.dashboard'))
            ->assertOk()
            ->assertSee('action="' . route('public.dashboard') . '"', false);

        // When public user filters by year, they must remain on public dashboard without being redirected to admin login
        $this->get(route('public.dashboard', ['tahun' => 2023]))
            ->assertOk()
            ->assertSee('action="' . route('public.dashboard') . '"', false);
    }

    public function test_public_sidebar_only_shows_dashboard_and_analysis_menu(): void
    {
        $user = User::create([
            'name' => 'Publik User',
            'email' => 'publik@example.com',
            'password' => Hash::make('password123'),
            'role' => 'public',
        ]);

        $this->actingAs($user, 'web');

        $this->get(route('public.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard')
            ->assertSee('Analisis Indikator')
            ->assertDontSee('Data Indikator')
            ->assertDontSee('Data Capaian')
            ->assertDontSee('Data Target')
            ->assertDontSee('Import Excel')
            ->assertDontSee('Data User');
    }

    public function test_public_sidebar_has_detail_indicator_page(): void
    {
        $user = User::create([
            'name' => 'Publik User',
            'email' => 'detail-test@example.com',
            'password' => Hash::make('password123'),
            'role' => 'public',
        ]);
        $this->actingAs($user, 'web');

        Indikator::create([
            'nama_indikator' => 'Indikator Pembangunan Utama',
            'satuan' => '%',
            'jenis' => 'utama',
            'deskripsi' => 'Deskripsi indikator pembangunan.',
        ]);

        $this->get(route('public.detail.indikator'))
            ->assertOk()
            ->assertSee('Indikator Pembangunan Utama')
            ->assertSee('Detail Indikator');
    }

    public function test_public_indicator_detail_page_has_indicator_filter(): void
    {
        $user = User::create([
            'name' => 'Publik User',
            'email' => 'filter-test@example.com',
            'password' => Hash::make('password123'),
            'role' => 'public',
        ]);
        $this->actingAs($user, 'web');

        $first = Indikator::create([
            'nama_indikator' => 'Indikator Pertama',
            'satuan' => '%',
            'jenis' => 'utama',
        ]);

        Indikator::create([
            'nama_indikator' => 'Indikator Kedua',
            'satuan' => '%',
            'jenis' => 'utama',
        ]);

        $this->get(route('public.indikator.show', $first))
            ->assertOk()
            ->assertSee('onchange="window.location.href = this.value"', false)
            ->assertSee('Indikator Kedua');
    }

    public function test_public_detail_page_defaults_to_ipm_indicator(): void
    {
        $user = User::create([
            'name' => 'Publik User',
            'email' => 'default-ipm@example.com',
            'password' => Hash::make('password123'),
            'role' => 'public',
        ]);
        $this->actingAs($user, 'web');

        $harapan = Indikator::create([
            'nama_indikator' => 'Harapan Lama Sekolah',
            'satuan' => 'Tahun',
            'jenis' => 'utama',
        ]);

        $ipm = Indikator::create([
            'nama_indikator' => 'IPM',
            'satuan' => '%',
            'jenis' => 'utama',
        ]);

        $response = $this->get(route('public.detail.indikator'));

        $response->assertOk();
        $response->assertSee('IPM');
        $response->assertSee('Harapan Lama Sekolah');
        $response->assertSee('onchange="window.location.href = this.value"', false);
        $response->assertSee('value="' . route('public.indikator.show', $ipm) . '"', false);
        $response->assertSee('value="' . route('public.indikator.show', $harapan) . '"', false);
    }

    public function test_public_user_with_blank_role_can_still_login(): void
    {
        User::create([
            'name' => 'Publik Lama',
            'email' => 'lama@example.com',
            'password' => Hash::make('password123'),
            'role' => '',
        ]);

        $response = $this->from(route('public.login'))->post(route('public.login.submit'), [
            'email' => 'lama@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('public.dashboard'));
        $this->assertAuthenticatedAs(User::where('email', 'lama@example.com')->first(), 'web');
        $this->assertSame('', User::where('email', 'lama@example.com')->first()->role);
    }

    public function test_public_dashboard_requires_authentication(): void
    {
        $this->get(route('public.dashboard'))
            ->assertRedirect(route('public.login'));

        $user = User::create([
            'name' => 'Publik User',
            'email' => 'dash-auth@example.com',
            'password' => Hash::make('password123'),
            'role' => 'public',
        ]);

        $this->actingAs($user, 'web');
        $this->get(route('public.dashboard'))->assertOk();
    }

    public function test_public_login_rejects_invalid_credentials(): void
    {
        $response = $this->from(route('public.login'))->post(route('public.login.submit'), [
            'email' => 'unknown@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect(route('public.login'));
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest('web');
    }

    public function test_authenticated_public_user_cannot_submit_empty_login_to_dashboard(): void
    {
        $user = User::create([
            'name' => 'Publik User',
            'email' => 'publik@example.com',
            'password' => Hash::make('password123'),
            'role' => 'public',
        ]);

        $this->actingAs($user, 'web');

        $response = $this->from(route('public.login'))->post(route('public.login.submit'), [
            'email' => '',
            'password' => '',
        ]);

        $response->assertRedirect(route('public.login'));
        $response->assertSessionHasErrors(['email', 'password']);
        $this->assertAuthenticated('web');
    }

    public function test_public_registration_requires_matching_password_confirmation(): void
    {
        $response = $this->from(route('public.register'))->post(route('public.register.submit'), [
            'name' => 'Publik User',
            'email' => 'publik@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertRedirect(route('public.register'));
        $response->assertSessionHasErrors(['password']);
        $this->assertDatabaseMissing('user', ['email' => 'publik@example.com']);
    }

    public function test_public_registration_page_does_not_redirect_authenticated_user_to_dashboard(): void
    {
        $user = User::create([
            'name' => 'Publik User',
            'email' => 'publik@example.com',
            'password' => Hash::make('password123'),
            'role' => 'public',
        ]);

        $this->actingAs($user, 'web');

        $this->get(route('public.register'))
            ->assertOk()
            ->assertSee('Daftar Akun Publik');
    }

    public function test_public_logout_returns_to_welcome_page(): void
    {
        $user = User::create([
            'name' => 'Publik User',
            'email' => 'publik@example.com',
            'password' => Hash::make('password123'),
            'role' => 'public',
        ]);

        $this->actingAs($user, 'web');

        $this->post(route('public.logout'))->assertRedirect('/');
        $this->assertGuest('web');
    }
}
