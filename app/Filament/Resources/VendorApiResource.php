<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VendorApiResource\Pages;
use App\Models\VendorApi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;

class VendorApiResource extends Resource
{
    protected static ?string $model = VendorApi::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationGroup = 'Vendor Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('API Information')
                    ->schema([
                        Forms\Components\Select::make('vendor_id')
                            ->relationship(
                                name: 'vendor',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query->orderBy('name')
                            )
                            ->searchable()
                            ->required()
                            ->label('Vendor'),
                        Forms\Components\TextInput::make('api_name')
                            ->required()
                            ->maxLength(255)
                            ->label('API Name'),
                        Forms\Components\TextInput::make('base_url')
                            ->required()
                            ->url()
                            ->maxLength(255)
                            ->label('Base URL'),
                        Forms\Components\TextInput::make('version')
                            ->default('v1')
                            ->required()
                            ->maxLength(255)
                            ->label('Version'),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255)
                            ->label('Email'),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->maxLength(255)
                            ->label('Password'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'maintenance' => 'Maintenance',
                            ])
                            ->default('active')
                            ->required()
                            ->native(false)
                            ->label('Status'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Authentication')
                    ->schema([
                        Forms\Components\Select::make('auth_type')
                            ->options([
                                'none' => 'None',
                                'api_key' => 'API Key',
                                'bearer_token' => 'Bearer Token',
                                'basic_auth' => 'Basic Auth',
                                'oauth2' => 'OAuth2',
                            ])
                            ->default('api_key')
                            ->required()
                            ->native(false)
                            ->label('Authentication Type')
                            ->live(),
                        Forms\Components\Textarea::make('auth_credentials')
                            ->label('Authentication Credentials')
                            ->helperText('This will be encrypted when stored')
                            ->visible(fn (Forms\Get $get) => $get('auth_type') !== 'none')
                            ->maxLength(65535),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Configuration')
                    ->schema([
                        Forms\Components\KeyValue::make('headers')
                            ->label('Additional Headers')
                            ->keyLabel('Header Name')
                            ->valueLabel('Header Value')
                            ->addActionLabel('Add Header'),
                        Forms\Components\TextInput::make('timeout')
                            ->numeric()
                            ->default(30)
                            ->required()
                            ->label('Timeout (seconds)')
                            ->minValue(1)
                            ->maxValue(300),
                        Forms\Components\TextInput::make('rate_limit')
                            ->numeric()
                            ->label('Rate Limit (requests per minute)')
                            ->minValue(1)
                            ->maxValue(10000),
                    ])
                    ->columns(2),

                // Forms\Components\Section::make('Health Status')
                //     ->schema([
                //         Forms\Components\Toggle::make('is_healthy')
                //             ->default(true)
                //             ->label('Is Healthy'),
                //         Forms\Components\DateTimePicker::make('last_tested_at')
                //             ->label('Last Tested At')
                //             ->displayFormat('Y-m-d H:i:s'),
                //     ])
                //     ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vendor.name')
                    ->searchable()
                    ->sortable()
                    ->label('Vendor'),
                Tables\Columns\TextColumn::make('api_name')
                    ->searchable()
                    ->sortable()
                    ->label('API Name'),
                Tables\Columns\TextColumn::make('base_url')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    })
                    ->label('Base URL'),
                Tables\Columns\TextColumn::make('version')
                    ->searchable()
                    ->label('Version'),
                // Tables\Columns\TextColumn::make('email')
                //     ->searchable()
                //     ->label('Email'),
                Tables\Columns\BadgeColumn::make('auth_type')
                    ->colors([
                        'secondary' => 'none',
                        'primary' => 'api_key',
                        'success' => 'bearer_token',
                        'warning' => 'basic_auth',
                        'danger' => 'oauth2',
                    ])
                    ->label('Auth Type'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'inactive',
                        'danger' => 'maintenance',
                    ])
                    ->label('Status'),
                // Tables\Columns\IconColumn::make('is_healthy')
                //     ->boolean()
                //     ->label('Healthy'),
                // Tables\Columns\TextColumn::make('last_tested_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true)
                //     ->label('Last Tested'),
                // Tables\Columns\TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true)
                //     ->label('Created At'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('vendor_id')
                    ->relationship(
                        'vendor',
                        'name',
                        fn (Builder $query) => $query->orderBy('name')
                    )
                    ->searchable()
                    ->label('Vendor'),
                Tables\Filters\SelectFilter::make('auth_type')
                    ->options([
                        'none' => 'None',
                        'api_key' => 'API Key',
                        'bearer_token' => 'Bearer Token',
                        'basic_auth' => 'Basic Auth',
                        'oauth2' => 'OAuth2',
                    ])
                    ->label('Auth Type'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'maintenance' => 'Maintenance',
                    ])
                    ->label('Status'),
                // Tables\Filters\TernaryFilter::make('is_healthy')
                //     ->label('Health Status'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
                Infolists\Components\Section::make('API Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('vendor.name')
                            ->label('Vendor'),
                        Infolists\Components\TextEntry::make('api_name')
                            ->label('API Name'),
                        Infolists\Components\TextEntry::make('base_url')
                            ->label('Base URL')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('version')
                            ->label('Version'),
                        Infolists\Components\TextEntry::make('email')
                            ->label('Email'),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'inactive' => 'warning',
                                'maintenance' => 'danger',
                            })
                            ->label('Status'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Authentication')
                    ->schema([
                        Infolists\Components\TextEntry::make('auth_type')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'none' => 'secondary',
                                'api_key' => 'primary',
                                'bearer_token' => 'success',
                                'basic_auth' => 'warning',
                                'oauth2' => 'danger',
                            })
                            ->label('Authentication Type'),
                        Infolists\Components\TextEntry::make('auth_credentials')
                            ->label('Authentication Credentials')
                            ->placeholder('Encrypted credentials')
                            ->visible(fn ($record) => $record->auth_type !== 'none'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Configuration')
                    ->schema([
                        Infolists\Components\KeyValueEntry::make('headers')
                            ->label('Additional Headers')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('timeout')
                            ->label('Timeout (seconds)')
                            ->suffix(' seconds'),
                        Infolists\Components\TextEntry::make('rate_limit')
                            ->label('Rate Limit')
                            ->suffix(' requests/minute')
                            ->placeholder('No limit'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Health Status')
                    ->schema([
                        Infolists\Components\IconEntry::make('is_healthy')
                            ->boolean()
                            ->label('Is Healthy'),
                        Infolists\Components\TextEntry::make('last_tested_at')
                            ->dateTime()
                            ->label('Last Tested At')
                            ->placeholder('Never tested'),
                    ])
                    ->columns(2),

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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['vendor:id,name']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVendorApis::route('/'),
            'create' => Pages\CreateVendorApi::route('/create'),
            // 'view' => Pages\ViewVendorApi::route('/{record}'),
            'edit' => Pages\EditVendorApi::route('/{record}/edit'),
        ];
    }
}
