<?php

namespace App\Filament\Resources\JsonTemplateResource\Pages;

use App\Filament\Resources\JsonTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditJsonTemplate extends EditRecord
{
    protected static string $resource = JsonTemplateResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('duplicate')
                ->label('Duplicate Template')
                ->icon('heroicon-o-document-duplicate')
                ->action(function () {
                    $newTemplate = $this->record->replicate();
                    $newTemplate->name = $this->record->name . ' (Copy)';
                    $newTemplate->created_by = Auth::id();
                    $newTemplate->updated_by = Auth::id();
                    $newTemplate->save();
                    
                    $this->redirect(JsonTemplateResource::getUrl('edit', ['record' => $newTemplate]));
                })
                ->requiresConfirmation()
                ->modalHeading('Duplicate Template')
                ->modalDescription('Are you sure you want to duplicate this template?'),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Convert JSON array back to string for editing
        if (isset($data['template_data']) && is_array($data['template_data'])) {
            $data['template_data'] = json_encode($data['template_data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = Auth::id();
        
        // Validate and format JSON
        if (isset($data['template_data'])) {
            $data['template_data'] = json_decode($data['template_data'], true);
        }
        
        return $data;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'JSON Template updated successfully!';
    }
}
