<?php

namespace Database\Seeders;

use App\Models\ApiEndpoint;
use App\Models\JsonTemplate;
use App\Models\Page;
use App\Models\Vendor;
use App\Models\VendorApi;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseDefaultSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Vendor ───
        $vendor = Vendor::firstOrCreate(
            ['code' => '002'],
            [
                'name' => 'Json Viewer',
                'company_name' => 'UT Pusat',
                'description' => 'Vendor penyedia layanan JSON viewer untuk data bahan ajar',
                'contact_person' => 'Admin UT',
                'email' => 'admin@ut.ac.id',
                'phone' => '021-12345678',
                'address' => 'Jl. Cabe Raya',
                'city' => 'Tangerang Selatan',
                'province' => 'Banten',
                'postal_code' => '15418',
                'status' => 'active',
            ]
        );
        $this->command?->info("Vendor: {$vendor->name}");

        // ─── Vendor API ───
        $vendorApi = VendorApi::firstOrCreate(
            [
                'vendor_id' => $vendor->id,
                'api_name' => 'Paramita',
            ],
            [
                'base_url' => 'https://prodev.ut.ac.id/jsonviewer/',
                'version' => 'v1',
                'auth_type' => 'none',
                'headers' => ['Accept' => 'application/json'],
                'timeout' => 30,
                'status' => 'active',
                'is_healthy' => true,
                'last_tested_at' => now(),
            ]
        );
        $this->command?->info("Vendor API: {$vendorApi->api_name}");

        // ─── Json Template ───
        $template = JsonTemplate::where('name', 'monitoring-stock')->first();
        if ($template) {
            $this->command?->info("Template: {$template->name} found");

            // ─── API Endpoint ───
            $endpoint = ApiEndpoint::firstOrCreate(
                [
                    'vendor_api_id' => $vendorApi->id,
                    'name' => 'monitoring-stock',
                ],
                [
                    'path' => '?api=1&route=Stock_Bahan_Ajar',
                    'method' => 'GET',
                    'description' => 'Endpoint for monitoring stock of teaching materials',
                    'status' => 'active',
                    'health_status' => 'unknown',
                    'requires_auth' => false,
                    'json_template_id' => $template->id,
                ]
            );
            $this->command?->info("Endpoint: {$endpoint->name}");
        } else {
            $this->command?->warn('Template monitoring-stock not found — skipping endpoint creation');
        }

        // ─── Dynamic Pages ───
        $adminUser = \App\Models\User::where('email', 'admin@paramita.com')->first();

        if ($adminUser && $template) {
            // 1. Monitoring Stock
            $page1 = Page::firstOrCreate(
                ['slug' => 'monitoring-stock'],
                [
                    'title' => 'Monitoring Stock',
                    'description' => 'Live monitoring stok bahan ajar dari endpoint JSON Viewer',
                    'icon' => '📦',
                    'is_active' => true,
                    'created_by' => $adminUser->id,
                ]
            );

            if (!$page1->jsonTemplates()->where('json_template_id', $template->id)->exists()) {
                $page1->jsonTemplates()->attach($template->id, ['display_order' => 0]);
            }

            $monitoringRoles = ['pimpinan-pusat', 'pimpinan-daerah'];
            $page1->roles()->syncWithoutDetaching(Role::whereIn('name', $monitoringRoles)->pluck('id')->toArray());

            $this->command?->info("Dynamic Page: {$page1->title} (/page/{$page1->slug})");

            // 2. Dashboard
            $page2 = Page::firstOrCreate(
                ['slug' => 'dashboard'],
                [
                    'title' => 'Dashboard',
                    'description' => 'Dashboard utama dengan ringkasan data monitoring',
                    'icon' => '📊',
                    'is_active' => true,
                    'created_by' => $adminUser->id,
                ]
            );

            // Attach monitoring-stock template to Dashboard page too
            if (!$page2->jsonTemplates()->where('json_template_id', $template->id)->exists()) {
                $page2->jsonTemplates()->attach($template->id, ['display_order' => 0]);
            }

            $dashboardRoles = ['pimpinan-pusat', 'pimpinan-daerah', 'viewer'];
            $page2->roles()->syncWithoutDetaching(Role::whereIn('name', $dashboardRoles)->pluck('id')->toArray());

            $this->command?->info("Dynamic Page: {$page2->title} (/page/{$page2->slug})");
        }

        $this->command?->info('Default data seeded successfully!');
    }
}
