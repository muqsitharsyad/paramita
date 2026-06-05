<?php

namespace App\Filament\Resources\SidebarMenuItemResource\Pages;

use App\Filament\Resources\SidebarMenuItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSidebarMenuItems extends ManageRecords
{
    protected static string $resource = SidebarMenuItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
