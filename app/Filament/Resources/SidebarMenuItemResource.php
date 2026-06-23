<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SidebarMenuItemResource\Pages;
use App\Models\Page;
use App\Models\SidebarMenuItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SidebarMenuItemResource extends Resource
{
    protected static ?string $model = SidebarMenuItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-3';

    protected static ?string $navigationGroup = 'System Management';

    protected static ?string $navigationLabel = 'Sidebar Menu';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Menu Item')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('label')
                                    ->required()
                                    ->maxLength(100)
                                    ->label('Label')
                                    ->placeholder('e.g., Dashboard'),
                                Forms\Components\Select::make('icon')
                                    ->label('Icon')
                                    ->options([
                                        'dashboard' => 'Dashboard',
                                        'templates' => 'JSON Templates',
                                        'vendors' => 'Vendors',
                                        'monitoring' => 'Monitoring',
                                        'reports' => 'Reports',
                                        'settings' => 'Settings',
                                        'users' => 'Users',
                                        'link' => 'Link',
                                    ])
                                    ->helperText('Pilih icon yang tersedia'),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('dynamic_page_id')
                                    ->label('Dynamic Page')
                                    ->options(fn () => Page::query()
                                        ->active()
                                        ->orderBy('title')
                                        ->pluck('title', 'id'))
                                    ->placeholder('- Pilih Dynamic Page -')
                                    ->helperText('Pilih page untuk mengisi URL secara otomatis.')
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->dehydrated(false)
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if (! $state) {
                                            $set('label', null);
                                            $set('url', null);

                                            return;
                                        }

                                        $page = Page::query()->find($state);

                                        if (! $page) {
                                            return;
                                        }

                                        $set('label', $page->title);
                                        $set('url', '/page/' . $page->slug);
                                        $set('route_name', null);
                                    }),
                                Forms\Components\TextInput::make('url')
                                    ->label('URL')
                                    ->placeholder('e.g., /page/monitoring-stock')
                                    ->helperText('URL tujuan. Diprioritaskan untuk menu aplikasi user.'),
                            ]),
                        Forms\Components\TextInput::make('route_name')
                            ->label('Route Name (Optional)')
                            ->placeholder('e.g., filament.admin.resources.pages.index')
                            ->helperText('Opsional. Gunakan hanya jika benar-benar perlu ke named route.'),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('parent_id')
                                    ->label('Parent Menu')
                                    ->relationship('parent', 'label')
                                    ->preload()
                                    ->nullable()
                                    ->placeholder('- Root -'),
                                Forms\Components\TextInput::make('order')
                                    ->numeric()
                                    ->default(0)
                                    ->label('Urutan'),
                                Forms\Components\Toggle::make('is_active')
                                    ->default(true)
                                    ->label('Aktif'),
                            ]),
                        Forms\Components\Select::make('roles')
                            ->label('Role Access')
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->preload()
                            ->required()
                            ->helperText('Pilih role yang bisa melihat menu ini'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('order')
            ->defaultSort('order')
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->label('#')
                    ->sortable()
                    ->width(40),
                Tables\Columns\TextColumn::make('label')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->label('Menu'),
                Tables\Columns\TextColumn::make('icon')
                    ->badge()
                    ->color('gray')
                    ->label('Icon'),
                Tables\Columns\TextColumn::make('route_name')
                    ->label('Route')
                    ->limit(30)
                    ->color('gray')
                    ->icon('heroicon-o-link'),
                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->label('Roles')
                    ->separator(','),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->label('Filter by Role'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('duplicate')
                    ->icon('heroicon-o-document-duplicate')
                    ->action(function (SidebarMenuItem $record) {
                        $new = $record->replicate();
                        $new->label = $record->label . ' (Copy)';
                        $new->save();

                        $new->roles()->sync($record->roles->pluck('id'));

                        return redirect()->route('filament.admin.resources.sidebar-menu-items.index');
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Duplicate Menu Item')
                    ->modalDescription('Duplicate this menu item including role assignments?'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'roles:id,name',
                'parent:id,label',
            ])
            ->orderBy('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSidebarMenuItems::route('/'),
        ];
    }
}
