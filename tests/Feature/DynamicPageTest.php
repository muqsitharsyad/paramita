<?php

namespace Tests\Feature;

use App\Models\JsonTemplate;
use App\Models\Page;
use App\Models\User;
use Database\Seeders\JsonTemplateSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DynamicPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->seed(JsonTemplateSeeder::class);
    }

    private function createUser(string $role, string $email = null): User
    {
        $user = User::factory()->create([
            'name' => ucfirst(str_replace('-', ' ', $role)),
            'email' => $email ?? $role . '@test.com',
            'password' => bcrypt('password'),
            'nip' => '99' . str_pad((string)rand(0, 9999), 4, '0', STR_PAD_LEFT),
            'status' => 'active',
        ]);
        $user->assignRole($role);
        return $user;
    }

    private function createTemplate(): JsonTemplate
    {
        $admin = $this->createUser('admin', 'admin-tpl-' . rand(1000, 9999) . '@test.com');
        return JsonTemplate::create([
            'name' => 'test-template-' . rand(100, 999),
            'category' => 'api-response',
            'description' => 'Test template for dynamic pages',
            'version' => '1.0',
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
            'template_data' => [
                'status' => 'success',
                'data' => [
                    ['item' => 'test1', 'value' => 100],
                    ['item' => 'test2', 'value' => 200],
                ],
            ],
        ]);
    }

    private function createPage(string $title, string $slug, array $roleNames, bool $active = true, $template = null): Page
    {
        $user = $this->createUser('admin', 'admin-page@test.com');

        $page = Page::create([
            'title' => $title,
            'slug' => $slug,
            'description' => 'Test page description',
            'icon' => '📄',
            'is_active' => $active,
            'created_by' => $user->id,
        ]);

        if ($template) {
            $page->jsonTemplates()->attach($template->id, ['display_order' => 0]);
        }

        $roles = Role::whereIn('name', $roleNames)->get();
        foreach ($roles as $role) {
            $page->roles()->attach($role->id);
        }

        return $page;
    }

    public function test_authorized_user_can_access_dynamic_page(): void
    {
        $template = $this->createTemplate();
        $page = $this->createPage('Test Page', 'test-page', ['pimpinan-pusat'], true, $template);
        $user = $this->createUser('pimpinan-pusat', 'pusat-access@test.com');

        // Verify database state
        $this->assertDatabaseHas('pages', ['slug' => 'test-page', 'is_active' => true]);
        $this->assertDatabaseHas('page_role', ['page_id' => $page->id]);

        // Access the page
        $this->actingAs($user);
        $response = $this->get('/page/test-page');
        $response->assertStatus(200);
        $response->assertSee('Test Page');
        $response->assertSee($template->name);
    }

    public function test_unauthorized_role_gets_403(): void
    {
        $template = $this->createTemplate();
        $this->createPage('Restricted', 'restricted', ['pimpinan-pusat'], true, $template);
        $user = $this->createUser('viewer', 'viewer-no-access@test.com');

        $this->actingAs($user);
        $response = $this->get('/page/restricted');
        $response->assertStatus(403);
    }

    public function test_multiple_roles_can_access_same_page(): void
    {
        $template = $this->createTemplate();
        $this->createPage('Multi Role', 'multi-role', ['pimpinan-pusat', 'pimpinan-daerah'], true, $template);

        // Pusat can access
        $pusat = $this->createUser('pimpinan-pusat', 'pusat-multi@test.com');
        $this->actingAs($pusat);
        $this->get('/page/multi-role')->assertStatus(200);

        // Daerah can access
        $daerah = $this->createUser('pimpinan-daerah', 'daerah-multi@test.com');
        $this->actingAs($daerah);
        $this->get('/page/multi-role')->assertStatus(200);

        // Viewer cannot access
        $viewer = $this->createUser('viewer', 'viewer-multi@test.com');
        $this->actingAs($viewer);
        $this->get('/page/multi-role')->assertStatus(403);
    }

    public function test_inactive_page_returns_404(): void
    {
        $template = $this->createTemplate();
        $this->createPage('Inactive', 'inactive', ['pimpinan-pusat'], false, $template);
        $user = $this->createUser('pimpinan-pusat', 'pusat-inactive@test.com');

        $this->actingAs($user);
        $response = $this->get('/page/inactive');
        $response->assertStatus(404);
    }

    public function test_nonexistent_page_returns_404(): void
    {
        $user = $this->createUser('pimpinan-pusat', 'pusat-404@test.com');
        $this->actingAs($user);
        $this->get('/page/tidak-ada')->assertStatus(404);
    }

    public function test_page_shows_multiple_templates(): void
    {
        $template1 = $this->createTemplate();
        $template2 = $this->createTemplate();
        $user = $this->createUser('admin', 'admin-multi-tpl@test.com');

        $page = Page::create([
            'title' => 'Multi Template',
            'slug' => 'multi-template',
            'is_active' => true,
            'created_by' => $user->id,
        ]);
        $page->jsonTemplates()->attach($template1->id, ['display_order' => 0]);
        $page->jsonTemplates()->attach($template2->id, ['display_order' => 1]);
        $role = Role::where('name', 'pimpinan-pusat')->first();
        $page->roles()->attach($role->id);

        $pusat = $this->createUser('pimpinan-pusat', 'pusat-mtpl@test.com');
        $this->actingAs($pusat);
        $response = $this->get('/page/multi-template');
        $response->assertStatus(200);
        $response->assertSee($template1->name);
        $response->assertSee($template2->name);
    }
}
