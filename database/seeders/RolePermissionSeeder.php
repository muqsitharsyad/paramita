<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'view_users',
            'create_users',
            'update_users',
            'delete_users',
            'view_reports',
            'manage_system',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Create default admin user
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@paramita.com',
            'password' => bcrypt('password'),
            'nip' => '000001',
            'status' => 'active',
        ]);
        $admin->assignRole('admin');
    }
}