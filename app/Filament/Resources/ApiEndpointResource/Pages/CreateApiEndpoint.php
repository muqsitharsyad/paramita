<?php

namespace App\Filament\Resources\ApiEndpointResource\Pages;

use App\Filament\Resources\ApiEndpointResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateApiEndpoint extends CreateRecord
{
    protected static string $resource = ApiEndpointResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
