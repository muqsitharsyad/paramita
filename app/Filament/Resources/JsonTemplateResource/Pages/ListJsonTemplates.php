<?php

namespace App\Filament\Resources\JsonTemplateResource\Pages;

use App\Filament\Resources\JsonTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJsonTemplates extends ListRecords
{
    protected static string $resource = JsonTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
