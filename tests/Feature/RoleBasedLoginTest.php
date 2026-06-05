<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleBasedLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->createPage('Dashboard', 'dashboard', ['pimpinan-pusat', 'pimpinan-daerah', 'viewer']);
        $this->createPage('Pusat Only', 'pusat-only', ['pimpinan-pusat']);
    }

    private function createUserWithRole(string $email, string $role): User
    {
        $user = User::factory()->create([
            'email' => $email,
            'password' => bcrypt('password'),
        ]);
        $user->assignRole($role);

        return $user;
    }

    private function createPage(string $title, string $slug, array $roleNames): Page
    {
        $admin = User::factory()->create([
            'email' => "admin-{$slug}@test.com",
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole('admin');

        $page = Page::create([
            'title' => $title,
            'slug' => $slug,
            'is_active' => true,
            'created_by' => $admin->id,
        ]);

        $page->roles()->sync(Role::whereIn('name', $roleNames)->pluck('id'));

        return $page;
    }

    public function test_admin_is_redirected_to_filament_panel(): void
    {
        $user = $this->createUserWithRole('admin@test.com', 'admin');
        $this->actingAs($user);

        $this->get('/')->assertRedirect(route('filament.admin.pages.dashboard'));
    }

    public function test_non_admin_roles_are_redirected_to_dynamic_dashboard(): void
    {
        foreach (['pimpinan-pusat', 'pimpinan-daerah', 'viewer'] as $role) {
            $user = $this->createUserWithRole("{$role}@test.com", $role);
            $this->actingAs($user);

            $this->get('/')->assertRedirect('/page/dashboard');
        }
    }

    public function test_unauthenticated_user_is_redirected_to_welcome(): void
    {
        $this->get('/')->assertRedirect(route('welcome'));
    }

    public function test_non_admin_roles_can_access_dashboard_page(): void
    {
        foreach (['pimpinan-pusat', 'pimpinan-daerah', 'viewer'] as $role) {
            $user = $this->createUserWithRole("dashboard-{$role}@test.com", $role);
            $this->actingAs($user);

            $this->get('/page/dashboard')->assertOk();
        }
    }

    public function test_role_restricted_dynamic_page_returns_403_for_other_roles(): void
    {
        $viewer = $this->createUserWithRole('viewer@test.com', 'viewer');
        $this->actingAs($viewer);

        $this->get('/page/pusat-only')->assertForbidden();
    }

    public function test_allowed_role_can_access_role_restricted_dynamic_page(): void
    {
        $pusat = $this->createUserWithRole('pusat@test.com', 'pimpinan-pusat');
        $this->actingAs($pusat);

        $this->get('/page/pusat-only')->assertOk();
    }

    public function test_home_route_redirects_logged_in_user_to_dynamic_dashboard(): void
    {
        $user = $this->createUserWithRole('home@test.com', 'viewer');
        $this->actingAs($user);

        $this->get('/home')->assertRedirect('/page/dashboard');
    }
}
