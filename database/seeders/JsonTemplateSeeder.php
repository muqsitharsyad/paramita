<?php

namespace Database\Seeders;

use App\Models\JsonTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;

class JsonTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first user as creator — or create a placeholder
        $user = User::first();
        $userId = $user ? $user->id : null;

        // Dashboard Template
        JsonTemplate::create([
            'name' => 'dashboard',
            'category' => 'dashboard',
            'description' => 'Standard dashboard response template with summary cards, charts, and statistics',
            'version' => '1.0',
            'is_active' => true,
            'created_by' => $userId,
            'updated_by' => $userId,
            'template_data' => [
                "status" => "{{status}}",
                "code" => "{{code}}",
                "message" => "{{message}}",
                "timestamp" => "{{timestamp}}",
                "data" => [
                    "title" => "{{title}}",
                    "semester" => "{{semester}}",
                    "summaryCards" => [
                        [
                            "label" => "{{label}}",
                            "value" => "{{value}}"
                        ]
                    ],
                    "charts" => [
                        "doByDate" => [
                            "title" => "{{title}}",
                            "type" => "{{type}}",
                            "dateRange" => [
                                "start" => "{{date_start}}",
                                "end" => "{{date_end}}"
                            ],
                            "zoomOptions" => [
                                "{{zoom_option}}"
                            ],
                            "data" => [
                                [
                                    "date" => "{{date}}",
                                    "do" => "{{do}}"
                                ]
                            ]
                        ],
                        "deliveryPercentage" => [
                            "title" => "{{title}}",
                            "type" => "{{type}}",
                            "data" => [
                                [
                                    "label" => "{{label}}",
                                    "value" => "{{value}}"
                                ]
                            ]
                        ],
                        "DoStatus" => [
                            "title" => "{{title}}",
                            "type" => "{{type}}",
                            "data" => [
                                [
                                    "label" => "{{label}}",
                                    "value" => "{{value}}"
                                ]
                            ]
                        ],
                        "DoEkspeditur" => [
                            "title" => "{{title}}",
                            "type" => "{{type}}",
                            "data" => [
                                [
                                    "label" => "{{label}}",
                                    "value" => "{{value}}"
                                ]
                            ]
                        ]
                    ],
                    "deliveryStats" => [
                        [
                            "label" => "{{label}}",
                            "value" => "{{value}}"
                        ]
                    ]
                ]
            ]
        ]);

        // API Response List Template
        JsonTemplate::create([
            'name' => 'list',
            'category' => 'api-response',
            'description' => 'Standard API response template for data listing with pagination',
            'version' => '1.0',
            'is_active' => true,
            'created_by' => $userId,
            'updated_by' => $userId,
            'template_data' => [
                "status" => "{{status}}",
                "code" => "{{code}}",
                "message" => "{{message}}",
                "timestamp" => "{{timestamp}}",
                "data" => [
                    "items" => [],
                    "meta" => [
                        "current_page" => "{{current_page}}",
                        "per_page" => "{{per_page}}",
                        "total" => "{{total}}",
                        "last_page" => "{{last_page}}",
                        "from" => "{{from}}",
                        "to" => "{{to}}"
                    ]
                ]
            ]
        ]);

        // API Response Single Item Template
        JsonTemplate::create([
            'name' => 'item',
            'category' => 'api-response',
            'description' => 'Standard API response template for single item',
            'version' => '1.0',
            'is_active' => true,
            'created_by' => $userId,
            'updated_by' => $userId,
            'template_data' => [
                "status" => "{{status}}",
                "code" => "{{code}}",
                "message" => "{{message}}",
                "timestamp" => "{{timestamp}}",
                "data" => []
            ]
        ]);

        // Error Response Template
        JsonTemplate::create([
            'name' => 'error',
            'category' => 'api-response',
            'description' => 'Standard error response template',
            'version' => '1.0',
            'is_active' => true,
            'created_by' => $userId,
            'updated_by' => $userId,
            'template_data' => [
                "status" => "error",
                "code" => "{{code}}",
                "message" => "{{message}}",
                "timestamp" => "{{timestamp}}",
                "errors" => []
            ]
        ]);

        // Validation Error Template
        JsonTemplate::create([
            'name' => 'validation-error',
            'category' => 'api-response',
            'description' => 'Template for validation error responses',
            'version' => '1.0',
            'is_active' => true,
            'created_by' => $userId,
            'updated_by' => $userId,
            'template_data' => [
                "status" => "error",
                "code" => 422,
                "message" => "Validation failed",
                "timestamp" => "{{timestamp}}",
                "errors" => []
            ]
        ]);

        // Success Response Template
        JsonTemplate::create([
            'name' => 'success',
            'category' => 'api-response',
            'description' => 'Standard success response template',
            'version' => '1.0',
            'is_active' => true,
            'created_by' => $userId,
            'updated_by' => $userId,
            'template_data' => [
                "status" => "success",
                "code" => "{{code}}",
                "message" => "{{message}}",
                "timestamp" => "{{timestamp}}",
                "data" => []
            ]
        ]);

        // Report Template
        JsonTemplate::create([
            'name' => 'report',
            'category' => 'report',
            'description' => 'Standard report response template',
            'version' => '1.0',
            'is_active' => true,
            'created_by' => $userId,
            'updated_by' => $userId,
            'template_data' => [
                "status" => "{{status}}",
                "code" => "{{code}}",
                "message" => "{{message}}",
                "timestamp" => "{{timestamp}}",
                "data" => [
                    "title" => "{{report_title}}",
                    "period" => "{{report_period}}",
                    "generated_at" => "{{timestamp}}",
                    "summary" => [],
                    "details" => [],
                    "charts" => []
                ]
            ]
        ]);

        // Notification Template
        JsonTemplate::create([
            'name' => 'notification',
            'category' => 'notification',
            'description' => 'Standard notification template',
            'version' => '1.0',
            'is_active' => true,
            'created_by' => $userId,
            'updated_by' => $userId,
            'template_data' => [
                "id" => "{{notification_id}}",
                "type" => "{{notification_type}}",
                "title" => "{{title}}",
                "message" => "{{message}}",
                "data" => [],
                "read" => false,
                "created_at" => "{{timestamp}}"
            ]
        ]);

        // Monitoring Stock Template
        JsonTemplate::create([
            'name' => 'monitoring-stock',
            'category' => 'api-response',
            'description' => 'Template for monitoring stock of teaching materials (bahan ajar)',
            'version' => '1.0',
            'is_active' => true,
            'created_by' => $userId,
            'updated_by' => $userId,
            'template_data' => [
                "status" => "{{status}}",
                "code" => "{{code}}",
                "message" => "{{message}}",
                "data" => [
                    [
                        "no" => "{{no}}",
                        "kode_buku" => "{{kode_buku}}",
                        "edisi" => "{{edisi}}",
                        "judul" => "{{judul}}",
                        "ukuran_buku" => "{{ukuran_buku}}",
                        "stok" => "{{stok}}",
                        "berat_buku" => "{{berat_buku}}",
                        "total_berat" => "{{total_berat}}",
                        "total_tinggi" => "{{total_tinggi}}",
                        "total_luas_area" => "{{total_luas_area}}",
                        "penyedia" => "{{penyedia}}"
                    ]
                ],
                "pagination" => [
                    "limit" => "{{limit}}",
                    "offset" => "{{offset}}",
                    "total" => "{{total}}",
                    "next_offset" => "{{next_offset}}",
                    "prev_offset" => "{{prev_offset}}"
                ]
            ]
        ]);
    }
}
