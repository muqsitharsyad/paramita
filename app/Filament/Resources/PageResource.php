<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\JsonTemplate;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'System Management';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Dynamic Pages';

    protected static ?string $pluralModelLabel = 'Dynamic Pages';

    protected static ?string $modelLabel = 'Page';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Page Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (?string $state, callable $set) {
                                if ($state) {
                                    $set('slug', Str::slug($state));
                                }
                            })
                            ->placeholder('e.g., Monitoring Buku')
                            ->helperText('Page title displayed in the header'),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('e.g., monitoring-buku')
                            ->helperText('URL path untuk /page/{slug}')
                            ->rules(['alpha_dash']),

                        Forms\Components\TextInput::make('icon')
                            ->maxLength(50)
                            ->placeholder('icon or emoji')
                            ->helperText('Optional icon for navigation'),

                        Forms\Components\Textarea::make('description')
                            ->maxLength(500)
                            ->rows(2)
                            ->placeholder('Deskripsi singkat halaman ini'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Only active pages are accessible'),
                    ])->columns(2),

                Forms\Components\Section::make('JSON Templates')
                    ->description('Select templates to display on this page. Data will be fetched live from their associated endpoints.')
                    ->schema([
                        Forms\Components\CheckboxList::make('jsonTemplates')
                            ->label('Templates')
                            ->relationship('jsonTemplates', 'name')
                            ->options(fn () => JsonTemplate::query()
                                ->active()
                                ->orderBy('name')
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->bulkToggleable()
                            ->columns(2)
                            ->gridDirection('row')
                            ->helperText('Select one or more templates. Templates without endpoints will show their structure as reference.'),
                    ]),

                Forms\Components\Section::make('Role Access')
                    ->description('Which roles can access this page')
                    ->schema([
                        Forms\Components\CheckboxList::make('roles')
                            ->label('Roles')
                            ->relationship('roles', 'name')
                            ->options(fn () => Role::query()
                                ->orderBy('name')
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->bulkToggleable()
                            ->columns(2)
                            ->gridDirection('row')
                            ->helperText('Only users with selected roles can view this page.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('icon')
                    ->label('')
                    ->formatStateUsing(fn (?string $state) => $state ?? 'page')
                    ->size(Tables\Columns\TextColumn\TextColumnSize::Large),

                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->sortable()
                    ->searchable()
                    ->weight('medium')
                    ->description(fn (Page $record) => '/page/' . $record->slug),

                Tables\Columns\TextColumn::make('jsonTemplates')
                    ->label('Templates')
                    ->formatStateUsing(function (Page $record) {
                        $names = $record->jsonTemplates->pluck('name')->toArray();

                        return $names !== [] ? implode(', ', $names) : 'None';
                    })
                    ->badge()
                    ->color('info')
                    ->wrap(),

                Tables\Columns\TextColumn::make('roles')
                    ->label('Role Access')
                    ->formatStateUsing(function (Page $record) {
                        $names = $record->roles->pluck('name')->toArray();

                        return $names !== [] ? implode(', ', $names) : 'None';
                    })
                    ->badge()
                    ->color('success')
                    ->wrap(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jsonTemplates')
                    ->relationship('jsonTemplates', 'name')
                    ->multiple()
                    ->label('Filter by Template'),

                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->label('Filter by Role'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Delete Page')
                    ->modalDescription('Are you sure? This action cannot be undone.')
                    ->modalSubmitActionLabel('Yes, delete it'),
                Tables\Actions\Action::make('duplicate')
                    ->icon('heroicon-o-document-duplicate')
                    ->action(function (Page $record) {
                        $new = $record->replicate();
                        $new->title = $record->title . ' (Copy)';
                        $new->slug = static::makeUniqueSlug($new->title);
                        $new->created_by = Auth::id();
                        $new->save();

                        $new->jsonTemplates()->sync($record->jsonTemplates->pluck('id'));
                        $new->roles()->sync($record->roles->pluck('id'));

                        return redirect()->route('filament.admin.resources.pages.edit', $new);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Duplicate Page')
                    ->modalDescription('Duplicate this page including template and role assignments?'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('title')
            ->striped();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'jsonTemplates:id,name',
                'roles:id,name',
            ]);
    }

    private static function makeUniqueSlug(string $title): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 2;

        while (Page::query()->where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
