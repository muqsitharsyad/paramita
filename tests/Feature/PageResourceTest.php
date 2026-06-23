<?php

namespace Tests\Feature;

use App\Filament\Resources\PageResource;
use Illuminate\Support\Collection;
use ReflectionMethod;
use Tests\TestCase;

class PageResourceTest extends TestCase
{
    public function test_relation_names_are_rendered_once(): void
    {
        $method = new ReflectionMethod(PageResource::class, 'formatRelationNames');

        $summary = $method->invoke(null, new Collection([
            (object) ['name' => 'monitoring-stock'],
            (object) ['name' => 'detail-buku'],
        ]));

        $this->assertSame('monitoring-stock, detail-buku', $summary);
    }
}
