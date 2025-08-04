<?php

namespace App\Filament\Resources\VendorApiResource\Pages;

use App\Filament\Resources\VendorApiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVendorApis extends ListRecords
{
    protected static string $resource = VendorApiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
