<?php

namespace Database\Seeders;

use App\Models\SidebarMenuItem;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class SidebarMenuSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Dynamic Page Menu Items ───
        // Hanya seed item yang指向 Dynamic Pages — tanpa route manual.
        // Admin bisa menambah/mengatur sendiri via System Management → Sidebar Menu.

        $menus = [
            [
                'label' => 'Dashboard',
                'icon' => 'dashboard',
                'url' => '/page/dashboard',
                'order' => 0,
                'roles' => ['pimpinan-pusat', 'pimpinan-daerah', 'viewer'],
            ],
            [
                'label' => 'Monitoring Stock',
                'icon' => 'monitoring',
                'url' => '/page/monitoring-stock',
                'order' => 1,
                'roles' => ['pimpinan-pusat', 'pimpinan-daerah'],
            ],
        ];

        foreach ($menus as $data) {
            $roles = $data['roles'];
            unset($data['roles']);

            $item = SidebarMenuItem::firstOrCreate(
                ['url' => $data['url']],
                $data
            );

            foreach ($roles as $roleName) {
                $role = Role::where('name', $roleName)->first();
                if ($role && !$item->roles()->where('role_id', $role->id)->exists()) {
                    $item->roles()->attach($role->id);
                }
            }
        }

        $this->command?->info('Sidebar menu items (Dynamic Pages only) seeded successfully.');
    }
}
