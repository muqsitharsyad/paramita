<?php

namespace App\Filament\Resources\VendorApiResource\Pages;

use App\Filament\Resources\VendorApiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVendorApi extends EditRecord
{
    protected static string $resource = VendorApiResource::class;

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
