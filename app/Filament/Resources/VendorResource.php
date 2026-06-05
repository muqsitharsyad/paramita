<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VendorResource\Pages;
use App\Models\Vendor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;

class VendorResource extends Resource
{
    protected static ?string $model = Vendor::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Vendor Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Vendor Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Vendor Name'),
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->label('Vendor Code')
                            ->alphaNum(),
                        Forms\Components\TextInput::make('company_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Company Name'),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->label('Description')
                            ->columnSpanFull(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'suspended' => 'Suspended',
                            ])
                            ->default('active')
                            ->required()
                            ->native(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Contact Information')
                    ->schema([
                        Forms\Components\TextInput::make('contact_person')
                            ->required()
                            ->maxLength(255)
                            ->label('Contact Person'),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->label('Email Address'),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->required()
                            ->maxLength(255)
                            ->label('Phone Number'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Address Information')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->required()
                            ->maxLength(65535)
                            ->label('Address')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('city')
                            ->required()
                            ->maxLength(255)
                            ->label('City'),
                        Forms\Components\TextInput::make('province')
                            ->required()
                            ->maxLength(255)
                            ->label('Province'),
                        Forms\Components\TextInput::make('postal_code')
                            ->required()
                            ->maxLength(255)
                            ->label('Postal Code'),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->label('Code'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Vendor Name'),
                Tables\Columns\TextColumn::make('company_name')
                    ->searchable()
                    ->sortable()
                    ->label('Company'),
                Tables\Columns\TextColumn::make('contact_person')
                    ->searchable()
                    ->label('Contact Person'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable()
                    ->label('Email'),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->copyable()
                    ->label('Phone'),
                Tables\Columns\TextColumn::make('city')
                    ->searchable()
                    ->label('City'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'inactive',
                        'danger' => 'suspended',
                    ])
                    ->label('Status'),
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
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'suspended' => 'Suspended',
                    ])
                    ->label('Status'),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Created From'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Created Until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn ($query, $date) => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn ($query, $date) => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->label('Created Date Range'),
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
                Infolists\Components\Section::make('Vendor Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Vendor Name'),
                        Infolists\Components\TextEntry::make('code')
                            ->label('Vendor Code'),
                        Infolists\Components\TextEntry::make('company_name')
                            ->label('Company Name'),
                        Infolists\Components\TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'inactive' => 'warning',
                                'suspended' => 'danger',
                            })
                            ->label('Status'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Contact Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('contact_person')
                            ->label('Contact Person'),
                        Infolists\Components\TextEntry::make('email')
                            ->label('Email Address')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('phone')
                            ->label('Phone Number')
                            ->copyable(),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Address Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('address')
                            ->label('Address')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('city')
                            ->label('City'),
                        Infolists\Components\TextEntry::make('province')
                            ->label('Province'),
                        Infolists\Components\TextEntry::make('postal_code')
                            ->label('Postal Code'),
                    ])
                    ->columns(3),

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
        return parent::getEloquentQuery();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVendors::route('/'),
            'create' => Pages\CreateVendor::route('/create'),
            // 'view' => Pages\ViewVendor::route('/{record}'),
            'edit' => Pages\EditVendor::route('/{record}/edit'),
        ];
    }
}
