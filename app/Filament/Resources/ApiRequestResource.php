<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApiRequestResource\Pages;
use App\Models\ApiRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ApiRequestResource extends Resource
{
    protected static ?string $model = ApiRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationGroup = 'API Management';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Request Information')
                    ->schema([
                        Forms\Components\Select::make('vendor_api_id')
                            ->relationship(
                                name: 'vendorApi',
                                titleAttribute: 'api_name',
                                modifyQueryUsing: fn (Builder $query) => $query->orderBy('api_name')
                            )
                            ->searchable()
                            ->required()
                            ->label('Vendor API')
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set) {
                                $set('api_endpoint_id', null);
                            }),
                        Forms\Components\Select::make('api_endpoint_id')
                            ->relationship(
                                'apiEndpoint',
                                'name',
                                function ($query, $get) {
                                    if ($get('vendor_api_id')) {
                                        $query->where('vendor_api_id', $get('vendor_api_id'));
                                    }

                                    return $query->orderBy('name');
                                }
                            )
                            ->searchable()
                            ->required()
                            ->label('API Endpoint'),
                        Forms\Components\TextInput::make('request_id')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->label('Request ID')
                            ->default(fn () => 'REQ-' . now()->format('YmdHis') . '-' . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT)),
                        Forms\Components\Select::make('method')
                            ->options([
                                'GET' => 'GET',
                                'POST' => 'POST',
                                'PUT' => 'PUT',
                                'PATCH' => 'PATCH',
                                'DELETE' => 'DELETE',
                            ])
                            ->required()
                            ->native(false)
                            ->label('HTTP Method'),
                        Forms\Components\TextInput::make('url')
                            ->required()
                            ->url()
                            ->label('Full URL')
                            ->columnSpanFull(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'success' => 'Success',
                                'failed' => 'Failed',
                                'timeout' => 'Timeout',
                            ])
                            ->default('pending')
                            ->required()
                            ->native(false)
                            ->label('Status'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Request Details')
                    ->schema([
                        Forms\Components\KeyValue::make('headers')
                            ->label('Request Headers')
                            ->keyLabel('Header Name')
                            ->valueLabel('Header Value')
                            ->addActionLabel('Add Header'),
                        Forms\Components\KeyValue::make('parameters')
                            ->label('Request Parameters')
                            ->keyLabel('Parameter Name')
                            ->valueLabel('Parameter Value')
                            ->addActionLabel('Add Parameter'),
                        Forms\Components\Textarea::make('request_body')
                            ->label('Request Body')
                            ->rows(5)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Response Details')
                    ->schema([
                        Forms\Components\TextInput::make('response_code')
                            ->numeric()
                            ->label('Response Code')
                            ->minValue(100)
                            ->maxValue(599),
                        Forms\Components\TextInput::make('response_time')
                            ->numeric()
                            ->label('Response Time (ms)')
                            ->minValue(0),
                        Forms\Components\KeyValue::make('response_headers')
                            ->label('Response Headers')
                            ->keyLabel('Header Name')
                            ->valueLabel('Header Value')
                            ->addActionLabel('Add Header'),
                        Forms\Components\Textarea::make('response_body')
                            ->label('Response Body')
                            ->rows(10)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('error_message')
                            ->label('Error Message')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Timestamps')
                    ->schema([
                        Forms\Components\DateTimePicker::make('requested_at')
                            ->required()
                            ->label('Requested At')
                            ->default(now()),
                        Forms\Components\DateTimePicker::make('responded_at')
                            ->label('Responded At'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('request_id')
                    ->searchable()
                    ->sortable()
                    ->label('Request ID')
                    ->copyable(),
                Tables\Columns\TextColumn::make('vendorApi.api_name')
                    ->searchable()
                    ->sortable()
                    ->label('API Name'),
                Tables\Columns\TextColumn::make('apiEndpoint.name')
                    ->searchable()
                    ->sortable()
                    ->label('Endpoint'),
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
                        'warning' => 'pending',
                        'success' => 'success',
                        'danger' => 'failed',
                        'secondary' => 'timeout',
                    ])
                    ->label('Status'),
                Tables\Columns\TextColumn::make('response_code')
                    ->sortable()
                    ->label('Response Code')
                    ->color(fn ($state) => match (true) {
                        $state >= 200 && $state < 300 => 'success',
                        $state >= 300 && $state < 400 => 'warning',
                        $state >= 400 => 'danger',
                        default => 'secondary',
                    }),
                Tables\Columns\TextColumn::make('response_time')
                    ->sortable()
                    ->label('Response Time')
                    ->suffix(' ms')
                    ->color(fn ($state) => match (true) {
                        $state < 500 => 'success',
                        $state < 2000 => 'warning',
                        $state >= 2000 => 'danger',
                        default => 'secondary',
                    }),
                Tables\Columns\TextColumn::make('requested_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Requested At'),
                Tables\Columns\TextColumn::make('responded_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Responded At'),
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
                        'pending' => 'Pending',
                        'success' => 'Success',
                        'failed' => 'Failed',
                        'timeout' => 'Timeout',
                    ])
                    ->label('Status'),
                Tables\Filters\Filter::make('response_code')
                    ->form([
                        Forms\Components\Select::make('response_code_range')
                            ->options([
                                '2xx' => '2xx - Success',
                                '3xx' => '3xx - Redirection',
                                '4xx' => '4xx - Client Error',
                                '5xx' => '5xx - Server Error',
                            ])
                            ->label('Response Code Range'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when(
                            $data['response_code_range'],
                            fn ($query, $range) => match ($range) {
                                '2xx' => $query->whereBetween('response_code', [200, 299]),
                                '3xx' => $query->whereBetween('response_code', [300, 399]),
                                '4xx' => $query->whereBetween('response_code', [400, 499]),
                                '5xx' => $query->whereBetween('response_code', [500, 599]),
                            }
                        );
                    }),
                Tables\Filters\Filter::make('requested_at')
                    ->form([
                        Forms\Components\DatePicker::make('requested_from')
                            ->label('Requested From'),
                        Forms\Components\DatePicker::make('requested_until')
                            ->label('Requested Until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['requested_from'],
                                fn ($query, $date) => $query->whereDate('requested_at', '>=', $date),
                            )
                            ->when(
                                $data['requested_until'],
                                fn ($query, $date) => $query->whereDate('requested_at', '<=', $date),
                            );
                    }),
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
            ->defaultSort('requested_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Request Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('request_id')
                            ->label('Request ID')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('vendorApi.api_name')
                            ->label('Vendor API'),
                        Infolists\Components\TextEntry::make('apiEndpoint.name')
                            ->label('API Endpoint'),
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
                        Infolists\Components\TextEntry::make('url')
                            ->label('Full URL')
                            ->copyable()
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'success' => 'success',
                                'failed' => 'danger',
                                'timeout' => 'secondary',
                            })
                            ->label('Status'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Request Details')
                    ->schema([
                        Infolists\Components\KeyValueEntry::make('headers')
                            ->label('Request Headers')
                            ->columnSpanFull(),
                        Infolists\Components\KeyValueEntry::make('parameters')
                            ->label('Request Parameters')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('request_body')
                            ->label('Request Body')
                            ->columnSpanFull()
                            ->markdown(),
                    ])
                    ->columns(1),

                Infolists\Components\Section::make('Response Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('response_code')
                            ->label('Response Code')
                            ->color(fn ($state) => match (true) {
                                $state >= 200 && $state < 300 => 'success',
                                $state >= 300 && $state < 400 => 'warning',
                                $state >= 400 => 'danger',
                                default => 'secondary',
                            }),
                        Infolists\Components\TextEntry::make('response_time')
                            ->label('Response Time')
                            ->suffix(' ms')
                            ->color(fn ($state) => match (true) {
                                $state < 500 => 'success',
                                $state < 2000 => 'warning',
                                $state >= 2000 => 'danger',
                                default => 'secondary',
                            }),
                        Infolists\Components\KeyValueEntry::make('response_headers')
                            ->label('Response Headers')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('response_body')
                            ->label('Response Body')
                            ->columnSpanFull()
                            ->markdown(),
                        Infolists\Components\TextEntry::make('error_message')
                            ->label('Error Message')
                            ->columnSpanFull()
                            ->visible(fn ($record) => !empty($record->error_message)),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Timestamps')
                    ->schema([
                        Infolists\Components\TextEntry::make('requested_at')
                            ->dateTime()
                            ->label('Requested At'),
                        Infolists\Components\TextEntry::make('responded_at')
                            ->dateTime()
                            ->label('Responded At')
                            ->placeholder('No response yet'),
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
            ->with([
                'vendorApi:id,api_name',
                'apiEndpoint:id,name',
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApiRequests::route('/'),
            'create' => Pages\CreateApiRequest::route('/create'),
            // 'view' => Pages\ViewApiRequest::route('/{record}'),
            'edit' => Pages\EditApiRequest::route('/{record}/edit'),
        ];
    }
}
