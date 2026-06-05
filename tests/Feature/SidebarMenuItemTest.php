<?php

namespace Tests\Feature;

use App\Models\SidebarMenuItem;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SidebarMenuItemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_tree_for_role_only_returns_visible_roots_and_children(): void
    {
        $pusatRole = Role::where('name', 'pimpinan-pusat')->firstOrFail();
        $viewerRole = Role::where('name', 'viewer')->firstOrFail();

        $root = SidebarMenuItem::create([
            'label' => 'Dashboard',
            'url' => '/page/dashboard',
            'order' => 1,
            'is_active' => true,
        ]);
        $root->roles()->attach($pusatRole);

        $allowedChild = SidebarMenuItem::create([
            'label' => 'Allowed Child',
            'url' => '/page/allowed-child',
            'parent_id' => $root->id,
            'order' => 1,
            'is_active' => true,
        ]);
        $allowedChild->roles()->attach($pusatRole);

        $blockedChild = SidebarMenuItem::create([
            'label' => 'Blocked Child',
            'url' => '/page/blocked-child',
            'parent_id' => $root->id,
            'order' => 2,
            'is_active' => true,
        ]);
        $blockedChild->roles()->attach($viewerRole);

        $inactiveChild = SidebarMenuItem::create([
            'label' => 'Inactive Child',
            'url' => '/page/inactive-child',
            'parent_id' => $root->id,
            'order' => 3,
            'is_active' => false,
        ]);
        $inactiveChild->roles()->attach($pusatRole);

        $hiddenRoot = SidebarMenuItem::create([
            'label' => 'Hidden Root',
            'url' => '/page/hidden-root',
            'order' => 2,
            'is_active' => true,
        ]);
        $hiddenRoot->roles()->attach($viewerRole);

        $menuTree = SidebarMenuItem::treeForRole('pimpinan-pusat');

        $this->assertCount(1, $menuTree);
        $this->assertSame('Dashboard', $menuTree->first()->label);
        $this->assertCount(1, $menuTree->first()->children);
        $this->assertSame('Allowed Child', $menuTree->first()->children->first()->label);
    }
}
