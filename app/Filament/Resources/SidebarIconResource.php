<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SidebarIconResource\Pages;
use App\Models\SidebarIcon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class SidebarIconResource extends Resource
{
    protected static ?string $model = SidebarIcon::class;

    protected static ?string $navigationIcon = 'heroicon-o-swatch';

    protected static ?string $navigationGroup = 'System Management';

    protected static ?string $navigationLabel = 'Sidebar Icons';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Icon')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('key')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(50)
                                ->regex('/^[a-z0-9_-]+$/')
                                ->helperText('Contoh: warehouse, book, delivery-truck'),
                            Forms\Components\TextInput::make('label')
                                ->required()
                                ->maxLength(100),
                        ]),
                    Forms\Components\Textarea::make('svg')
                        ->required()
                        ->rows(10)
                        ->columnSpanFull()
                        ->placeholder('<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">...</svg>')
                        ->extraAttributes([
                            'x-on:input' => 'document.getElementById("sidebar-icon-preview").innerHTML = $event.target.value',
                        ])
                        ->helperText(new HtmlString('Format acuan:<br><code>&lt;svg width=&quot;20&quot; height=&quot;20&quot; viewBox=&quot;0 0 24 24&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; stroke-width=&quot;2&quot;&gt;<br>&nbsp;&nbsp;&lt;path d=&quot;...&quot; /&gt;<br>&lt;/svg&gt;</code>')),
                    Forms\Components\Placeholder::make('preview')
                        ->label('Preview')
                        ->content(fn ($get) => new HtmlString('<span id="sidebar-icon-preview" style="display:inline-flex;width:40px;height:40px;align-items:center;justify-content:center;color:#212B36;border:1px solid #DFE3E8;border-radius:8px;">' . ($get('svg') ?: '') . '</span>')),
                    Forms\Components\Toggle::make('is_active')
                        ->default(true)
                        ->label('Aktif'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('key')
            ->columns([
                Tables\Columns\ViewColumn::make('svg')
                    ->label('Icon')
                    ->view('filament.tables.columns.sidebar-icon'),
                Tables\Columns\TextColumn::make('key')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('label')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSidebarIcons::route('/'),
        ];
    }
}
