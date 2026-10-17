<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \App\Services\AdminLoginPathService::resetToDefault();
        $this->seed(\Database\Seeders\AdminSeeder::class);
    }

    protected function tearDown(): void
    {
        \App\Services\AdminLoginPathService::resetToDefault();
        parent::tearDown();
    }

    public function test_admin_can_login_and_be_redirected_to_dashboard(): void
    {
        $response = $this->post(route('login.submit'), [
            'username' => 'admin',
            'password' => 'admin12345',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated('admin');
    }

    public function test_admin_cannot_login_with_invalid_credentials(): void
    {
        $response = $this->from(route('login'))->post(route('login.submit'), [
            'username' => 'admin',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['username']);
        $this->assertGuest('admin');
    }

    public function test_dashboard_requires_admin_authentication(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_login_again_after_logout(): void
    {
        $this->post(route('login.submit'), [
            'username' => 'admin',
            'password' => 'admin12345',
        ])->assertRedirect('/dashboard');

        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest('admin');

        $this->post(route('login.submit'), [
            'username' => 'admin',
            'password' => 'admin12345',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticated('admin');
    }

    public function test_admin_dashboard_secret_url_displays_admin_login(): void
    {
        $this->get('/admin-dashboard')->assertOk()->assertSee('Login Administrator');
    }

    public function test_common_login_and_admin_urls_return_404(): void
    {
        $this->get('/login')->assertNotFound();
        $this->get('/admin')->assertNotFound();
        $this->get('/admin/login')->assertNotFound();
    }
}
