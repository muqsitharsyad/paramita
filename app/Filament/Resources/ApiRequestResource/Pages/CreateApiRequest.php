<?php

namespace App\Filament\Resources\ApiRequestResource\Pages;

use App\Filament\Resources\ApiRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateApiRequest extends CreateRecord
{
    protected static string $resource = ApiRequestResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
