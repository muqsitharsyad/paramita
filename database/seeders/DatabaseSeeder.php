<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Urutan penting:
     * 1. RolePermissionSeeder — membuat roles, permissions, dan users
     * 2. JsonTemplateSeeder — membuat JSON templates (butuh user ID)
     * 3. DatabaseDefaultSeeder — membuat vendor, endpoint, dan dynamic page
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            JsonTemplateSeeder::class,
            DatabaseDefaultSeeder::class,
            SidebarMenuSeeder::class,
        ]);
    }
}
