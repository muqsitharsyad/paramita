<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JsonTemplateResource\Pages;
use App\Models\JsonTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Illuminate\Support\Str;

class JsonTemplateResource extends Resource
{
    protected static ?string $model = JsonTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'API Management';

    protected static ?string $navigationLabel = 'JSON Templates';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Template Information')
                    ->description('Basic information about the JSON template')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->label('Template Name')
                                    ->helperText('Unique name for this template (e.g., dashboard, user-profile)')
                                    ->placeholder('dashboard'),
                                
                                Forms\Components\Select::make('category')
                                    ->required()
                                    ->options([
                                        'api-response' => 'API Response',
                                        'dashboard' => 'Dashboard',
                                        'report' => 'Report',
                                        'notification' => 'Notification',
                                        'configuration' => 'Configuration',
                                        'other' => 'Other',
                                    ])
                                    ->label('Category')
                                    ->helperText('Category to group similar templates')
                                    ->default('api-response'),
                            ]),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->placeholder('Describe what this template is used for...')
                            ->helperText('Brief description of the template usage'),
                        
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('version')
                                    ->required()
                                    ->default('1.0')
                                    ->label('Version')
                                    ->helperText('Template version (e.g., 1.0, 2.1)'),
                                
                                Forms\Components\Toggle::make('is_active')
                                    ->default(true)
                                    ->label('Active')
                                    ->helperText('Whether this template is available for use'),
                            ]),
                    ]),
                
                Section::make('JSON Template Data')
                    ->description('The JSON structure that will be used as template')
                    ->schema([
                        Forms\Components\Textarea::make('template_data')
                            ->required()
                            ->label('JSON Template')
                            ->rows(20)
                            ->placeholder('{"status": "success", "code": 200, "message": "{{message}}", "data": {}}')
                            ->helperText('JSON structure with variables marked as {{variable_name}}')
                            ->columnSpanFull()
                            ->extraAttributes([
                                'style' => 'font-family: monospace; font-size: 14px;'
                            ])
                            ->rules(['json'])
                            ->validationMessages([
                                'json' => 'The template data must be valid JSON format.'
                            ])
                            ->dehydrateStateUsing(function ($state) {
                                // Ensure we always store as JSON string
                                if (is_array($state)) {
                                    return json_encode($state);
                                }
                                // Validate and re-encode to ensure proper formatting
                                $decoded = json_decode($state, true);
                                return $decoded ? json_encode($decoded) : $state;
                            }),
                    ]),
                
                Section::make('Metadata')
                    ->description('Template creation and modification information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('created_by')
                                    ->relationship('creator', 'name')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->label('Created By'),
                                
                                Forms\Components\Select::make('updated_by')
                                    ->relationship('updater', 'name')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->label('Last Updated By'),
                            ]),
                    ])
                    ->visibleOn('edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Template Name')
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'api-response' => 'primary',
                        'dashboard' => 'success',
                        'report' => 'warning',
                        'notification' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),
                
                Tables\Columns\TextColumn::make('version')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Created By')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('updater.name')
                    ->label('Updated By')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options([
                        'api-response' => 'API Response',
                        'dashboard' => 'Dashboard',
                        'report' => 'Report',
                        'notification' => 'Notification',
                        'configuration' => 'Configuration',
                        'other' => 'Other',
                    ]),
                
                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        1 => 'Active',
                        0 => 'Inactive',
                    ]),
                
                TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('duplicate')
                    ->icon('heroicon-o-document-duplicate')
                    ->action(function (JsonTemplate $record) {
                        $newTemplate = $record->replicate();
                        $newTemplate->name = static::makeUniqueCopyName($record->name);
                        $newTemplate->created_by = Auth::id();
                        $newTemplate->updated_by = Auth::id();
                        $newTemplate->save();
                        
                        return redirect()->route('filament.admin.resources.json-templates.edit', $newTemplate);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Duplicate Template')
                    ->modalDescription('Are you sure you want to duplicate this template?'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->with(['creator:id,name', 'updater:id,name']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJsonTemplates::route('/'),
            'create' => Pages\CreateJsonTemplate::route('/create'),
            'edit' => Pages\EditJsonTemplate::route('/{record}/edit'),
        ];
    }

    private static function makeUniqueCopyName(string $name): string
    {
        $baseName = $name . ' (Copy)';
        $candidate = $baseName;
        $counter = 2;

        while (JsonTemplate::query()->where('name', $candidate)->exists()) {
            $candidate = $baseName . ' ' . $counter;
            $counter++;
        }

        return $candidate;
    }
}
