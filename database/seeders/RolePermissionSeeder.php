<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[ \Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ─── Create Permissions ───
        $permissions = [
            // User management
            'view_users', 'create_users', 'update_users', 'delete_users',
            // Vendor management
            'view_vendors', 'create_vendors', 'update_vendors', 'delete_vendors',
            // API management
            'view_api_endpoints', 'create_api_endpoints', 'update_api_endpoints', 'delete_api_endpoints', 'test_api_endpoints',
            // API requests/logs
            'view_api_requests',
            // API configurations
            'view_api_configurations', 'manage_api_configurations',
            // JSON templates
            'view_json_templates', 'create_json_templates', 'update_json_templates', 'delete_json_templates',
            // Reports & Monitoring
            'view_reports', 'view_monitoring',
            // System management
            'manage_roles', 'manage_permissions', 'manage_system',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ─── Create Roles & Assign Permissions ───

        // Admin — full access
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        // Pimpinan Pusat — strategic oversight
        $pusatRole = Role::firstOrCreate(['name' => 'pimpinan-pusat', 'guard_name' => 'web']);
        $pusatRole->syncPermissions(Permission::whereIn('name', [
            'view_vendors', 'view_api_endpoints', 'view_api_requests',
            'view_api_configurations', 'view_json_templates', 'view_reports',
        ])->get());

        // Pimpinan Daerah — operational monitoring
        $daerahRole = Role::firstOrCreate(['name' => 'pimpinan-daerah', 'guard_name' => 'web']);
        $daerahRole->syncPermissions(Permission::whereIn('name', [
            'view_vendors', 'view_api_endpoints', 'test_api_endpoints',
            'view_api_requests', 'view_api_configurations', 'view_json_templates', 'view_monitoring',
        ])->get());

        // Viewer — read-only reports & templates
        $viewerRole = Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'web']);
        $viewerRole->syncPermissions(Permission::whereIn('name', ['view_reports', 'view_json_templates'])->get());

        // ─── Sample Users ───
        $admin = User::firstOrCreate(
            ['email' => 'admin@paramita.com'],
            ['name' => 'Administrator', 'password' => bcrypt('password'), 'nip' => '000001', 'status' => 'active']
        );
        if (!$admin->hasRole('admin')) $admin->assignRole('admin');

        $pusat = User::firstOrCreate(
            ['email' => 'pusat@paramita.com'],
            ['name' => 'Pimpinan Pusat', 'password' => bcrypt('password'), 'nip' => '000002', 'status' => 'active']
        );
        if (!$pusat->hasRole('pimpinan-pusat')) $pusat->assignRole('pimpinan-pusat');

        $daerah = User::firstOrCreate(
            ['email' => 'daerah@paramita.com'],
            ['name' => 'Pimpinan Daerah', 'password' => bcrypt('password'), 'nip' => '000003', 'status' => 'active']
        );
        if (!$daerah->hasRole('pimpinan-daerah')) $daerah->assignRole('pimpinan-daerah');

        $viewer = User::firstOrCreate(
            ['email' => 'viewer@paramita.com'],
            ['name' => 'Viewer', 'password' => bcrypt('password'), 'nip' => '000004', 'status' => 'active']
        );
        if (!$viewer->hasRole('viewer')) $viewer->assignRole('viewer');
    }
}
