<?php

namespace Tests\Feature;

use App\Filament\Resources\ApiEndpointResource;
use ReflectionMethod;
use Tests\TestCase;

class ApiEndpointResourceTest extends TestCase
{
    public function test_template_validation_summary_includes_error_details(): void
    {
        $method = new ReflectionMethod(ApiEndpointResource::class, 'formatTemplateValidation');

        $summary = $method->invoke(null, [
            'valid' => false,
            'errors' => [
                'Missing key: data[0].stok',
                'Type mismatch at data[0].total_berat: expected number, got string',
            ],
        ]);

        $this->assertSame(
            "Template tidak cocok.\nDetail masalah:\n1. Field `data[0].stok` tidak ada di response.\n2. Field `data[0].total_berat` tipe data salah. Template minta number, response memberi string.",
            $summary
        );
    }
}
