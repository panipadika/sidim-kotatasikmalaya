<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use App\Services\AdminLoginPathService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        AdminLoginPathService::resetToDefault();
        parent::tearDown();
    }

    private function createAdmin(string $username = 'admin', string $password = 'admin12345'): Admin
    {
        return Admin::create([
            'name' => 'Admin Test',
            'username' => $username,
            'password' => Hash::make($password),
        ]);
    }

    public function test_admin_can_view_settings_page(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin, 'admin')->get(route('settings.index'));

        $response->assertOk();
        $response->assertSee('Pengaturan Akun Admin');
        $response->assertSee('Username');
        $response->assertSee('Ubah Password');
        $response->assertSee('URL Khusus Login Admin');
        $response->assertSee($admin->username);
        $response->assertSee('admin-dashboard');
    }

    public function test_admin_can_update_username_and_name(): void
    {
        $admin = $this->createAdmin('admin', 'admin12345');

        $response = $this->actingAs($admin, 'admin')->put(route('settings.profile.update'), [
            'name' => 'Administrator Baru',
            'username' => 'admin_baru',
        ]);

        $response->assertRedirect(route('settings.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('admin', [
            'id' => $admin->id,
            'name' => 'Administrator Baru',
            'username' => 'admin_baru',
        ]);
    }

    public function test_admin_cannot_use_taken_username(): void
    {
        $admin1 = $this->createAdmin('admin1', 'admin12345');
        $admin2 = $this->createAdmin('admin2', 'admin12345');

        $response = $this->actingAs($admin1, 'admin')->put(route('settings.profile.update'), [
            'name' => 'Admin 1',
            'username' => 'admin2',
        ]);

        $response->assertSessionHasErrors(['username']);
        $this->assertDatabaseHas('admin', [
            'id' => $admin1->id,
            'username' => 'admin1',
        ]);
    }

    public function test_admin_can_update_password_with_valid_current_password(): void
    {
        $admin = $this->createAdmin('admin', 'passwordlama123');

        $response = $this->actingAs($admin, 'admin')->put(route('settings.password.update'), [
            'current_password' => 'passwordlama123',
            'password' => 'passwordbaru123',
            'password_confirmation' => 'passwordbaru123',
        ]);

        $response->assertRedirect(route('settings.index'));
        $response->assertSessionHas('success');

        $admin->refresh();
        $this->assertTrue(Hash::check('passwordbaru123', $admin->password));
    }

    public function test_admin_cannot_update_password_with_wrong_current_password(): void
    {
        $admin = $this->createAdmin('admin', 'passwordlama123');

        $response = $this->actingAs($admin, 'admin')->put(route('settings.password.update'), [
            'current_password' => 'passwordsalah',
            'password' => 'passwordbaru123',
            'password_confirmation' => 'passwordbaru123',
        ]);

        $response->assertSessionHasErrors(['current_password']);

        $admin->refresh();
        $this->assertTrue(Hash::check('passwordlama123', $admin->password));
    }

    public function test_admin_password_requires_confirmation_and_minimum_8_chars(): void
    {
        $admin = $this->createAdmin('admin', 'passwordlama123');

        // Short password
        $response1 = $this->actingAs($admin, 'admin')->put(route('settings.password.update'), [
            'current_password' => 'passwordlama123',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);
        $response1->assertSessionHasErrors(['password']);

        // Mismatched confirmation
        $response2 = $this->actingAs($admin, 'admin')->put(route('settings.password.update'), [
            'current_password' => 'passwordlama123',
            'password' => 'passwordbaru123',
            'password_confirmation' => 'passwordbedalagi',
        ]);
        $response2->assertSessionHasErrors(['password']);
    }

    public function test_admin_can_update_login_url(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin, 'admin')->put(route('settings.login-url.update'), [
            'login_path' => 'portal-rahasia-bappeda',
        ]);

        $response->assertRedirect(route('settings.index'));
        $response->assertSessionHas('success');

        $this->assertEquals('portal-rahasia-bappeda', AdminLoginPathService::getPath());
        $this->assertDatabaseHas('system_setting', [
            'key' => 'admin_login_path',
            'value' => 'portal-rahasia-bappeda',
        ]);
    }

    public function test_admin_cannot_set_reserved_login_url(): void
    {
        $admin = $this->createAdmin();

        foreach (['dashboard', 'login', 'admin', 'users', 'settings'] as $reserved) {
            $response = $this->actingAs($admin, 'admin')->put(route('settings.login-url.update'), [
                'login_path' => $reserved,
            ]);

            $response->assertSessionHasErrors(['login_path']);
        }
    }

    public function test_admin_cannot_set_invalid_login_url_format(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin, 'admin')->put(route('settings.login-url.update'), [
            'login_path' => 'url dengan spasi!',
        ]);

        $response->assertSessionHasErrors(['login_path']);
    }

    public function test_admin_can_reset_login_url_to_default(): void
    {
        $admin = $this->createAdmin();
        AdminLoginPathService::setPath('custom-login-path');

        $response = $this->actingAs($admin, 'admin')->post(route('settings.login-url.reset'));

        $response->assertRedirect(route('settings.index'));
        $response->assertSessionHas('success');

        $this->assertEquals(AdminLoginPathService::DEFAULT_PATH, AdminLoginPathService::getPath());
    }

    public function test_guest_cannot_access_settings(): void
    {
        $this->get(route('settings.index'))->assertRedirect(route('login'));
        $this->put(route('settings.profile.update'))->assertRedirect(route('login'));
        $this->put(route('settings.password.update'))->assertRedirect(route('login'));
        $this->put(route('settings.login-url.update'))->assertRedirect(route('login'));
        $this->post(route('settings.login-url.reset'))->assertRedirect(route('login'));
    }

    public function test_public_user_cannot_access_settings(): void
    {
        $user = User::create([
            'name' => 'Public User',
            'email' => 'public@example.com',
            'password' => Hash::make('password123'),
            'role' => 'public',
        ]);

        $this->actingAs($user, 'web')->get(route('settings.index'))->assertRedirect(route('login'));
        $this->actingAs($user, 'web')->put(route('settings.profile.update'))->assertRedirect(route('login'));
        $this->actingAs($user, 'web')->put(route('settings.password.update'))->assertRedirect(route('login'));
        $this->actingAs($user, 'web')->put(route('settings.login-url.update'))->assertRedirect(route('login'));
        $this->actingAs($user, 'web')->post(route('settings.login-url.reset'))->assertRedirect(route('login'));
    }
}
