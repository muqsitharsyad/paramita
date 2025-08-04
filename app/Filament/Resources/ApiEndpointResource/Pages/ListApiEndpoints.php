<?php

namespace App\Filament\Resources\ApiEndpointResource\Pages;

use App\Filament\Resources\ApiEndpointResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApiEndpoints extends ListRecords
{
    protected static string $resource = ApiEndpointResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
