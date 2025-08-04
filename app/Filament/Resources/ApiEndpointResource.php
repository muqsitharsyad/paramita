<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApiEndpointResource\Pages;
use App\Models\ApiEndpoint;
use App\Models\VendorApi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ApiEndpointResource extends Resource
{
    protected static ?string $model = ApiEndpoint::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?string $navigationGroup = 'API Management';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Endpoint Information')
                    ->schema([
                        Forms\Components\Select::make('vendor_api_id')
                            ->relationship('vendorApi', 'api_name')
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
                            ->options([
                                'GET' => 'GET',
                                'POST' => 'POST',
                                'PUT' => 'PUT',
                                'PATCH' => 'PATCH',
                                'DELETE' => 'DELETE',
                            ])
                            ->default('GET')
                            ->required()
                            ->native(false)
                            ->label('HTTP Method'),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->label('Description')
                            ->columnSpanFull(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'deprecated' => 'Deprecated',
                            ])
                            ->default('active')
                            ->required()
                            ->native(false)
                            ->label('Status'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Parameters')
                    ->schema([
                        Forms\Components\KeyValue::make('parameters')
                            ->label('Request Parameters')
                            ->keyLabel('Parameter Name')
                            ->valueLabel('Parameter Description')
                            ->addActionLabel('Add Parameter')
                            ->helperText('Define the parameters required for this endpoint'),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Response Format')
                    ->schema([
                        Forms\Components\Textarea::make('response_format')
                            ->label('Response Format (JSON)')
                            ->helperText('Example response format in JSON')
                            ->rows(10)
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Security')
                    ->schema([
                        Forms\Components\Toggle::make('requires_auth')
                            ->default(true)
                            ->label('Requires Authentication')
                            ->helperText('Whether this endpoint requires authentication'),
                    ])
                    ->columns(1),
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
                Tables\Columns\TextColumn::make('path')
                    ->searchable()
                    ->label('Path')
                    ->copyable(),
                Tables\Columns\BadgeColumn::make('method')
                    ->colors([
                        'primary' => 'GET',
                        'success' => 'POST',
                        'warning' => 'PUT',
                        'info' => 'PATCH',
                        'danger' => 'DELETE',
                    ])
                    ->label('Method'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'inactive',
                        'danger' => 'deprecated',
                    ])
                    ->label('Status'),
                Tables\Columns\IconColumn::make('requires_auth')
                    ->boolean()
                    ->label('Auth Required'),
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
                    ->preload()
                    ->label('Vendor API'),
                Tables\Filters\SelectFilter::make('method')
                    ->options([
                        'GET' => 'GET',
                        'POST' => 'POST',
                        'PUT' => 'PUT',
                        'PATCH' => 'PATCH',
                        'DELETE' => 'DELETE',
                    ])
                    ->label('HTTP Method'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'deprecated' => 'Deprecated',
                    ])
                    ->label('Status'),
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
                    ->action(function (ApiEndpoint $record) {
                        // Logic untuk test endpoint
                        // Implement test logic here
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Test Endpoint')
                    ->modalDescription('Are you sure you want to test this endpoint?')
                    ->modalSubmitActionLabel('Test Endpoint'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Endpoint Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('vendorApi.api_name')
                            ->label('Vendor API'),
                        Infolists\Components\TextEntry::make('name')
                            ->label('Endpoint Name'),
                        Infolists\Components\TextEntry::make('path')
                            ->label('Endpoint Path')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('method')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'GET' => 'primary',
                                'POST' => 'success',
                                'PUT' => 'warning',
                                'PATCH' => 'info',
                                'DELETE' => 'danger',
                            })
                            ->label('HTTP Method'),
                        Infolists\Components\TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'inactive' => 'warning',
                                'deprecated' => 'danger',
                            })
                            ->label('Status'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Parameters')
                    ->schema([
                        Infolists\Components\KeyValueEntry::make('parameters')
                            ->label('Request Parameters')
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                Infolists\Components\Section::make('Response Format')
                    ->schema([
                        Infolists\Components\TextEntry::make('response_format')
                            ->label('Response Format')
                            ->columnSpanFull()
                            ->markdown(),
                    ])
                    ->columns(1),

                Infolists\Components\Section::make('Security')
                    ->schema([
                        Infolists\Components\IconEntry::make('requires_auth')
                            ->boolean()
                            ->label('Requires Authentication'),
                    ])
                    ->columns(1),

                Infolists\Components\Section::make('Timestamps')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->dateTime()
                            ->label('Created At'),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->dateTime()
                            ->label('Updated At'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApiEndpoints::route('/'),
            'create' => Pages\CreateApiEndpoint::route('/create'),
            // 'view' => Pages\ViewApiEndpoint::route('/{record}'),
            'edit' => Pages\EditApiEndpoint::route('/{record}/edit'),
        ];
    }
}