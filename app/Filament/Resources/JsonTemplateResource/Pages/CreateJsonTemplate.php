<?php

namespace App\Filament\Resources\JsonTemplateResource\Pages;

use App\Filament\Resources\JsonTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateJsonTemplate extends CreateRecord
{
    protected static string $resource = JsonTemplateResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();
        
        // Validate and format JSON
        if (isset($data['template_data'])) {
            $data['template_data'] = json_decode($data['template_data'], true);
        }
        
        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'JSON Template created successfully!';
    }
}
