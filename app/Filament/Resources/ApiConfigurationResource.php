<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApiConfigurationResource\Pages;
use App\Models\ApiConfiguration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ApiConfigurationResource extends Resource
{
    protected static ?string $model = ApiConfiguration::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'API Management';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Configuration Information')
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
                        Forms\Components\TextInput::make('config_key')
                            ->required()
                            ->maxLength(255)
                            ->label('Configuration Key')
                            ->helperText('e.g., timeout, retry_count, base_path'),
                        Forms\Components\Select::make('data_type')
                            ->options([
                                'string' => 'String',
                                'integer' => 'Integer',
                                'boolean' => 'Boolean',
                                'json' => 'JSON',
                                'encrypted' => 'Encrypted',
                            ])
                            ->default('string')
                            ->required()
                            ->native(false)
                            ->label('Data Type')
                            ->live(),
                        Forms\Components\Toggle::make('is_sensitive')
                            ->default(false)
                            ->label('Is Sensitive Data')
                            ->helperText('Mark as sensitive for security purposes'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Configuration Value')
                    ->schema([
                        Forms\Components\TextInput::make('config_value')
                            ->required()
                            ->label('Configuration Value')
                            ->visible(fn (Forms\Get $get) => in_array($get('data_type'), ['string', 'encrypted']))
                            ->password(fn (Forms\Get $get) => $get('data_type') === 'encrypted'),
                        Forms\Components\TextInput::make('config_value')
                            ->required()
                            ->numeric()
                            ->label('Configuration Value')
                            ->visible(fn (Forms\Get $get) => $get('data_type') === 'integer'),
                        Forms\Components\Toggle::make('config_value_boolean')
                            ->label('Configuration Value')
                            ->visible(fn (Forms\Get $get) => $get('data_type') === 'boolean')
                            ->afterStateUpdated(function (Forms\Set $set, $state) {
                                $set('config_value', $state ? 'true' : 'false');
                            }),
                        Forms\Components\Textarea::make('config_value')
                            ->required()
                            ->label('Configuration Value (JSON)')
                            ->visible(fn (Forms\Get $get) => $get('data_type') === 'json')
                            ->helperText('Enter valid JSON format')
                            ->rows(5),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->label('Description')
                            ->helperText('Describe what this configuration does')
                            ->columnSpanFull(),
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
                    ->label('Vendor API'),
                Tables\Columns\TextColumn::make('config_key')
                    ->searchable()
                    ->sortable()
                    ->label('Configuration Key'),
                Tables\Columns\TextColumn::make('config_value')
                    ->searchable()
                    ->label('Value')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        $record = $column->getRecord();
                        
                        if ($record->is_sensitive) {
                            return 'Sensitive data - hidden for security';
                        }
                        
                        return strlen($state) > 50 ? $state : null;
                    })
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->is_sensitive) {
                            return '••••••••';
                        }
                        return $state;
                    }),
                Tables\Columns\BadgeColumn::make('data_type')
                    ->colors([
                        'primary' => 'string',
                        'success' => 'integer',
                        'warning' => 'boolean',
                        'info' => 'json',
                        'danger' => 'encrypted',
                    ])
                    ->label('Data Type'),
                Tables\Columns\IconColumn::make('is_sensitive')
                    ->boolean()
                    ->label('Sensitive'),
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
                    ->relationship(
                        'vendorApi',
                        'api_name',
                        fn (Builder $query) => $query->orderBy('api_name')
                    )
                    ->searchable()
                    ->label('Vendor API'),
                Tables\Filters\SelectFilter::make('data_type')
                    ->options([
                        'string' => 'String',
                        'integer' => 'Integer',
                        'boolean' => 'Boolean',
                        'json' => 'JSON',
                        'encrypted' => 'Encrypted',
                    ])
                    ->label('Data Type'),
                Tables\Filters\TernaryFilter::make('is_sensitive')
                    ->label('Is Sensitive'),
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
            ])
            ->defaultSort('config_key', 'asc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Configuration Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('vendorApi.api_name')
                            ->label('Vendor API'),
                        Infolists\Components\TextEntry::make('config_key')
                            ->label('Configuration Key')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('data_type')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'string' => 'primary',
                                'integer' => 'success',
                                'boolean' => 'warning',
                                'json' => 'info',
                                'encrypted' => 'danger',
                            })
                            ->label('Data Type'),
                        Infolists\Components\IconEntry::make('is_sensitive')
                            ->boolean()
                            ->label('Is Sensitive'),
                        Infolists\Components\TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Configuration Value')
                    ->schema([
                        Infolists\Components\TextEntry::make('config_value')
                            ->label('Value')
                            ->copyable(fn ($record) => !$record->is_sensitive)
                            ->formatStateUsing(function ($state, $record) {
                                if ($record->is_sensitive) {
                                    return '••••••••';
                                }
                                
                                return match ($record->data_type) {
                                    'json' => json_encode(json_decode($state), JSON_PRETTY_PRINT),
                                    'boolean' => $state === 'true' ? 'Yes' : 'No',
                                    default => $state,
                                };
                            })
                            ->columnSpanFull()
                            ->markdown(fn ($record) => $record->data_type === 'json'),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['vendorApi:id,api_name']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApiConfigurations::route('/'),
            'create' => Pages\CreateApiConfiguration::route('/create'),
            // 'view' => Pages\ViewApiConfiguration::route('/{record}'),
            'edit' => Pages\EditApiConfiguration::route('/{record}/edit'),
        ];
    }
}   
