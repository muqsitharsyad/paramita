<?php

namespace App\Filament\Resources\ApiEndpointResource\Pages;

use App\Filament\Resources\ApiEndpointResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApiEndpoint extends EditRecord
{
    protected static string $resource = ApiEndpointResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
