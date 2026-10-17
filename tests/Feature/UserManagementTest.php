<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::create([
            'name' => 'Admin Utama',
            'username' => 'admin',
            'password' => Hash::make('password123'),
        ]);
    }

    public function test_admin_can_view_user_management_page(): void
    {
        $user = User::create([
            'name' => 'Warga Tasik',
            'email' => 'warga@tasikmalaya.go.id',
            'password' => Hash::make('secret123'),
            'role' => 'public',
        ]);

        $response = $this->actingAs($this->admin, 'admin')->get(route('users.index'));

        $response->assertOk()
            ->assertSee('Data User Terdaftar')
            ->assertSee('Warga Tasik')
            ->assertSee('warga@tasikmalaya.go.id')
            ->assertSee('Data User');
    }

    public function test_admin_can_search_user_by_name_or_email(): void
    {
        User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'public',
        ]);

        User::create([
            'name' => 'Siti Rahma',
            'email' => 'siti@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'public',
        ]);

        $searchByName = $this->actingAs($this->admin, 'admin')->get(route('users.index', ['search' => 'Budi']));
        $searchByName->assertOk()
            ->assertSee('Budi Santoso')
            ->assertDontSee('Siti Rahma');

        $searchByEmail = $this->actingAs($this->admin, 'admin')->get(route('users.index', ['search' => 'siti@example.com']));
        $searchByEmail->assertOk()
            ->assertSee('Siti Rahma')
            ->assertDontSee('Budi Santoso');
    }

    public function test_admin_can_delete_user_account(): void
    {
        $user = User::create([
            'name' => 'User Hapus',
            'email' => 'hapus@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'public',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('users.destroy', $user));

        $response->assertRedirect(route('users.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('user', ['id' => $user->id]);
    }

    public function test_guest_cannot_access_user_management(): void
    {
        $response = $this->get(route('users.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_public_user_cannot_access_user_management(): void
    {
        $publicUser = User::create([
            'name' => 'Publik User',
            'email' => 'publik@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'public',
        ]);

        $response = $this->actingAs($publicUser, 'web')->get(route('users.index'));

        $response->assertRedirect(route('login'));
    }
}
