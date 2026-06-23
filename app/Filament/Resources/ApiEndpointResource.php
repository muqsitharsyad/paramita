<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApiEndpointResource\Pages;
use App\Models\ApiEndpoint;
use App\Services\ApiEndpointTestService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ApiEndpointResource extends Resource
{
    protected static ?string $model = ApiEndpoint::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?string $navigationGroup = 'API Management';

    protected static ?int $navigationSort = 2;

    private const METHOD_OPTIONS = [
        'GET' => 'GET',
        'POST' => 'POST',
        'PUT' => 'PUT',
        'PATCH' => 'PATCH',
        'DELETE' => 'DELETE',
    ];

    private const STATUS_OPTIONS = [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'deprecated' => 'Deprecated',
    ];

    private const HEALTH_OPTIONS = [
        'unknown' => 'Unknown',
        'healthy' => 'Healthy',
        'unhealthy' => 'Unhealthy',
        'warning' => 'Warning',
    ];

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Endpoint Information')
                    ->schema([
                        Forms\Components\Select::make('vendor_api_id')
                            ->relationship(
                                name: 'vendorApi',
                                titleAttribute: 'api_name',
                                modifyQueryUsing: fn (Builder $query) => $query->orderBy('api_name')
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Vendor API'),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Endpoint Name'),
                        Forms\Components\TextInput::make('path')
                            ->required()
                            ->maxLength(255)
                            ->label('Endpoint Path')
                            ->helperText('e.g., /users/{id} or /products'),
                        Forms\Components\Select::make('method')
                            ->options(self::METHOD_OPTIONS)
                            ->default('GET')
                            ->required()
                            ->native(false)
                            ->label('HTTP Method'),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->label('Description')
                            ->columnSpanFull(),
                        Forms\Components\Select::make('status')
                            ->options(self::STATUS_OPTIONS)
                            ->default('active')
                            ->required()
                            ->native(false)
                            ->label('Status'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Health Status')
                    ->schema([
                        Forms\Components\Select::make('health_status')
                            ->options(self::HEALTH_OPTIONS)
                            ->default('unknown')
                            ->disabled()
                            ->label('Health Status')
                            ->helperText('Health status is updated when the endpoint is tested.'),
                        Forms\Components\DateTimePicker::make('last_tested_at')
                            ->disabled()
                            ->label('Last Tested At'),
                        Forms\Components\Textarea::make('health_message')
                            ->disabled()
                            ->label('Health Message')
                            ->rows(3),
                    ])
                    ->columns(2)
                    ->visibleOn('edit'),

                Forms\Components\Section::make('JSON Template')
                    ->schema([
                        Forms\Components\Select::make('json_template_id')
                            ->relationship(
                                name: 'jsonTemplate',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query
                                    ->where('is_active', true)
                                    ->orderBy('name')
                            )
                            ->searchable()
                            ->preload()
                            ->label('JSON Response Template')
                            ->helperText('Optional. Used to validate response structure.')
                            ->live()
                            ->afterStateHydrated(function ($state, Forms\Set $set) {
                                static::syncTemplatePreview($state, $set);
                            })
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                static::syncTemplatePreview($state, $set);
                            }),
                        Forms\Components\Textarea::make('template_preview')
                            ->label('Template Preview')
                            ->rows(10)
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn (Forms\Get $get) => filled($get('template_preview')))
                            ->extraAttributes([
                                'style' => 'font-family: monospace; font-size: 12px; background: #f8f9fa;',
                            ])
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Parameters')
                    ->schema([
                        Forms\Components\KeyValue::make('parameters')
                            ->label('Request Parameters')
                            ->keyLabel('Parameter Name')
                            ->valueLabel('Parameter Description')
                            ->addActionLabel('Add Parameter')
                            ->helperText('Define the parameters required for this endpoint.'),
                    ]),

                Forms\Components\Section::make('Security')
                    ->schema([
                        Forms\Components\Toggle::make('requires_auth')
                            ->default(true)
                            ->label('Requires Authentication'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vendorApi.api_name')
                    ->searchable()
                    ->sortable()
                    ->label('API Name'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Endpoint Name'),
                Tables\Columns\BadgeColumn::make('method')
                    ->colors([
                        'primary' => 'GET',
                        'success' => 'POST',
                        'warning' => 'PUT',
                        'info' => 'PATCH',
                        'danger' => 'DELETE',
                    ])
                    ->label('Method'),
                Tables\Columns\BadgeColumn::make('health_status')
                    ->colors([
                        'success' => 'healthy',
                        'danger' => 'unhealthy',
                        'warning' => 'warning',
                        'gray' => 'unknown',
                    ])
                    ->formatStateUsing(fn (string $state): string => self::HEALTH_OPTIONS[$state] ?? 'Unknown')
                    ->label('Health'),
                Tables\Columns\TextColumn::make('jsonTemplate.name')
                    ->label('JSON Template')
                    ->badge()
                    ->color('info')
                    ->default('No Template')
                    ->formatStateUsing(fn ($state) => $state ?? 'No Template'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'inactive',
                        'danger' => 'deprecated',
                    ])
                    ->label('Status'),
                Tables\Columns\IconColumn::make('requires_auth')
                    ->boolean()
                    ->label('Auth')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('last_tested_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Last Tested')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Created At'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Updated At'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('vendor_api_id')
                    ->relationship('vendorApi', 'api_name')
                    ->searchable()
                    ->label('Vendor API'),
                Tables\Filters\SelectFilter::make('json_template_id')
                    ->relationship('jsonTemplate', 'name')
                    ->searchable()
                    ->label('JSON Template'),
                Tables\Filters\SelectFilter::make('method')
                    ->options(self::METHOD_OPTIONS)
                    ->label('HTTP Method'),
                Tables\Filters\SelectFilter::make('status')
                    ->options(self::STATUS_OPTIONS)
                    ->label('Status'),
                Tables\Filters\SelectFilter::make('health_status')
                    ->options(self::HEALTH_OPTIONS)
                    ->label('Health Status'),
                Tables\Filters\TernaryFilter::make('requires_auth')
                    ->label('Authentication Required'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('test')
                    ->icon('heroicon-o-play')
                    ->color('info')
                    ->form([
                        Forms\Components\Section::make('Test Configuration')
                            ->schema([
                                Forms\Components\Placeholder::make('endpoint_info')
                                    ->label('Endpoint')
                                    ->content(fn (ApiEndpoint $record) => "{$record->method} {$record->full_url}")
                                    ->columnSpanFull(),
                                Forms\Components\Placeholder::make('template_info')
                                    ->label('JSON Template')
                                    ->content(fn (ApiEndpoint $record) => $record->jsonTemplate
                                        ? "{$record->jsonTemplate->name} (v{$record->jsonTemplate->version})"
                                        : 'No template assigned'),
                                Forms\Components\Placeholder::make('health_info')
                                    ->label('Current Health Status')
                                    ->content(fn (ApiEndpoint $record) => $record->health_status_label
                                        . ($record->last_tested_at ? ' - ' . $record->last_tested_at->diffForHumans() : ' - never tested')),
                            ])
                            ->columns(2),
                        Forms\Components\Section::make('Test Parameters')
                            ->schema([
                                Forms\Components\KeyValue::make('test_parameters')
                                    ->label('URL Parameters')
                                    ->keyLabel('Parameter Name')
                                    ->valueLabel('Parameter Value')
                                    ->addActionLabel('Add Parameter'),
                                Forms\Components\Textarea::make('test_body')
                                    ->label('Request Body (JSON)')
                                    ->rows(5)
                                    ->visible(fn (ApiEndpoint $record) => in_array($record->method, ['POST', 'PUT', 'PATCH'], true))
                                    ->columnSpanFull(),
                                Forms\Components\KeyValue::make('test_headers')
                                    ->label('Additional Headers')
                                    ->keyLabel('Header Name')
                                    ->valueLabel('Header Value')
                                    ->addActionLabel('Add Header'),
                            ]),
                    ])
                    ->action(function (ApiEndpoint $record, array $data) {
                        static::notifyTestResult(
                            app(ApiEndpointTestService::class)->test($record, $data)
                        );
                    })
                    ->modalHeading('Test Endpoint')
                    ->modalDescription('Run a live request and validate the response against the selected template.')
                    ->modalSubmitActionLabel('Run Test')
                    ->modalWidth('3xl'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('bulk_test')
                        ->label('Test Selected Endpoints')
                        ->icon('heroicon-o-play')
                        ->color('info')
                        ->action(function ($records) {
                            static::runBulkTest($records);
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Test Multiple Endpoints')
                        ->modalDescription('This will test all selected endpoints with default parameters.')
                        ->modalSubmitActionLabel('Test All'),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                \Filament\Infolists\Components\Section::make('Endpoint Information')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('vendorApi.api_name')
                            ->label('Vendor API'),
                        \Filament\Infolists\Components\TextEntry::make('name')
                            ->label('Endpoint Name'),
                        \Filament\Infolists\Components\TextEntry::make('path')
                            ->label('Endpoint Path')
                            ->copyable(),
                        \Filament\Infolists\Components\TextEntry::make('method')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'GET' => 'primary',
                                'POST' => 'success',
                                'PUT' => 'warning',
                                'PATCH' => 'info',
                                'DELETE' => 'danger',
                                default => 'gray',
                            })
                            ->label('HTTP Method'),
                        \Filament\Infolists\Components\TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                        \Filament\Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'inactive' => 'warning',
                                'deprecated' => 'danger',
                                default => 'gray',
                            })
                            ->label('Status'),
                    ])
                    ->columns(2),

                \Filament\Infolists\Components\Section::make('Health Status')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('health_status')
                            ->label('Health Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'healthy' => 'success',
                                'unhealthy' => 'danger',
                                'warning' => 'warning',
                                'unknown' => 'gray',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => self::HEALTH_OPTIONS[$state] ?? 'Unknown'),
                        \Filament\Infolists\Components\TextEntry::make('last_tested_at')
                            ->label('Last Tested')
                            ->dateTime()
                            ->placeholder('Never tested'),
                        \Filament\Infolists\Components\TextEntry::make('health_message')
                            ->label('Health Message')
                            ->columnSpanFull()
                            ->placeholder('No health information available'),
                    ])
                    ->columns(2),

                \Filament\Infolists\Components\Section::make('JSON Template Configuration')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('jsonTemplate.name')
                            ->label('Template Name')
                            ->default('No template assigned'),
                        \Filament\Infolists\Components\TextEntry::make('jsonTemplate.version')
                            ->label('Template Version')
                            ->badge()
                            ->color('info')
                            ->visible(fn ($record) => $record->jsonTemplate),
                        \Filament\Infolists\Components\TextEntry::make('jsonTemplate.description')
                            ->label('Template Description')
                            ->visible(fn ($record) => $record->jsonTemplate && $record->jsonTemplate->description),
                        \Filament\Infolists\Components\TextEntry::make('jsonTemplate.template_data')
                            ->label('Template Structure')
                            ->columnSpanFull()
                            ->formatStateUsing(fn ($state): string => static::formatJson($state))
                            ->extraAttributes([
                                'style' => 'font-family: monospace; background: #f8f9fa; padding: 1rem; border-radius: 0.375rem; white-space: pre-wrap;',
                            ])
                            ->visible(fn ($record) => $record->jsonTemplate),
                    ])
                    ->columns(2),

                \Filament\Infolists\Components\Section::make('Parameters')
                    ->schema([
                        \Filament\Infolists\Components\KeyValueEntry::make('parameters')
                            ->label('Request Parameters')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->visible(fn ($record) => ! empty($record->parameters)),

                \Filament\Infolists\Components\Section::make('Security')
                    ->schema([
                        \Filament\Infolists\Components\IconEntry::make('requires_auth')
                            ->boolean()
                            ->label('Requires Authentication'),
                    ]),

                \Filament\Infolists\Components\Section::make('Timestamps')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('created_at')
                            ->dateTime()
                            ->label('Created At'),
                        \Filament\Infolists\Components\TextEntry::make('updated_at')
                            ->dateTime()
                            ->label('Updated At'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'vendorApi:id,api_name,base_url,version,timeout,email,password',
                'jsonTemplate:id,name,version,description,template_data',
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApiEndpoints::route('/'),
            'create' => Pages\CreateApiEndpoint::route('/create'),
            'edit' => Pages\EditApiEndpoint::route('/{record}/edit'),
        ];
    }

    private static function syncTemplatePreview($state, Forms\Set $set): void
    {
        if (! $state) {
            $set('template_preview', null);

            return;
        }

        $template = \App\Models\JsonTemplate::query()->find($state);

        $set('template_preview', $template ? static::formatJson($template->template_data) : null);
    }

    private static function formatJson($value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        if (is_array($value)) {
            return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

        $decoded = json_decode((string) $value, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

        return (string) $value;
    }

    private static function notifyTestResult(array $result): void
    {
        $status = $result['health_status'] ?? 'unknown';
        $title = match ($status) {
            'healthy' => 'Endpoint Healthy',
            'warning' => 'Endpoint Warning',
            default => 'Endpoint Test Failed',
        };

        $body = self::formatTestResultBody($result);

        Notification::make()
            ->title($title)
            ->body($body)
            ->duration(10000)
            ->{self::notificationMethod($status)}()
            ->send();
    }

    private static function formatTestResultBody(array $result): string
    {
        $statusCode = $result['status_code'] ?? null;
        $requestLine = ($result['method'] ?? 'GET') . ' ' . ($result['full_url'] ?? '');

        $lines = [
            'Endpoint:',
            $requestLine,
            '',
            'Hasil request:',
            $statusCode ? "HTTP {$statusCode}" : ($result['health_message'] ?? 'Request gagal.'),
        ];

        if (($result['health_status'] ?? null) === 'warning') {
            $lines[] = 'Request berhasil, tetapi struktur response berbeda dari template.';
        } elseif (($result['health_status'] ?? null) === 'healthy') {
            $lines[] = 'Request berhasil dan response cocok dengan template.';
        } elseif (isset($result['health_message'])) {
            $lines[] = $result['health_message'];
        }

        $lines[] = '';
        $lines[] = isset($result['template_validation']) && $result['template_validation'] !== null
            ? self::formatTemplateValidation($result['template_validation'])
            : 'Template tidak dicek.';

        return implode("\n", array_filter($lines, fn ($line) => $line !== null));
    }

    private static function formatTemplateValidation(array $validation): string
    {
        if ($validation['valid'] ?? false) {
            return 'Template: matched';
        }

        $errors = array_slice($validation['errors'] ?? [], 0, 5);

        if ($errors === []) {
            return 'Template tidak cocok.';
        }

        $formattedErrors = collect($errors)
            ->map(fn (string $error, int $index): string => ($index + 1) . '. ' . self::formatTemplateValidationError($error))
            ->implode("\n");

        return "Template tidak cocok.\nDetail masalah:\n{$formattedErrors}";
    }

    private static function formatTemplateValidationError(string $error): string
    {
        if (preg_match('/^Missing key: (.+)$/', $error, $matches)) {
            return "Field `{$matches[1]}` tidak ada di response.";
        }

        if (preg_match('/^Type mismatch at (.+): expected (.+), got (.+)$/', $error, $matches)) {
            return "Field `{$matches[1]}` tipe data salah. Template minta {$matches[2]}, response memberi {$matches[3]}.";
        }

        return $error;
    }

    private static function runBulkTest(iterable $records): void
    {
        $service = app(ApiEndpointTestService::class);
        $summary = [
            'healthy' => 0,
            'warning' => 0,
            'unhealthy' => 0,
            'unknown' => 0,
        ];

        foreach ($records as $record) {
            $result = $service->test($record, []);
            $status = $result['health_status'] ?? 'unknown';
            $summary[$status] = ($summary[$status] ?? 0) + 1;
        }

        Notification::make()
            ->title('Bulk Test Completed')
            ->body(sprintf(
                "Healthy: %d\nWarning: %d\nUnhealthy: %d",
                $summary['healthy'],
                $summary['warning'],
                $summary['unhealthy']
            ))
            ->success()
            ->send();
    }

    private static function notificationMethod(string $status): string
    {
        return match ($status) {
            'healthy' => 'success',
            'warning' => 'warning',
            default => 'danger',
        };
    }
}
