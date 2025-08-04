<?php

namespace App\Filament\Resources\VendorApiResource\Pages;

use App\Filament\Resources\VendorApiResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateVendorApi extends CreateRecord
{
    protected static string $resource = VendorApiResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
