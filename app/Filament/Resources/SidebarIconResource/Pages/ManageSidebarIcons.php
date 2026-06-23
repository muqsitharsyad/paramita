<?php

namespace App\Filament\Resources\SidebarIconResource\Pages;

use App\Filament\Resources\SidebarIconResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSidebarIcons extends ManageRecords
{
    protected static string $resource = SidebarIconResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
