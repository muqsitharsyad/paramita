<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\CheckboxList;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Str;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'System Management';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Permissions';

    protected static ?string $pluralModelLabel = 'Permissions';

    protected static ?string $modelLabel = 'Permission';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Permission Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $context, $state, callable $set) => $context === 'create' ? $set('guard_name', 'web') : null)
                            ->placeholder('e.g., create-posts, edit-users')
                            ->helperText('Permission name should be descriptive and follow kebab-case format')
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('generateName')
                                    ->icon('heroicon-m-sparkles')
                                    ->action(function ($state, callable $set) {
                                        $set('name', Str::slug($state));
                                    })
                            ),

                        TextInput::make('guard_name')
                            ->required()
                            ->default('web')
                            ->maxLength(255)
                            ->placeholder('web')
                            ->helperText('Guard name for the permission (usually "web")'),
                    ])->columns(2),

                Forms\Components\Section::make('Role Assignment')
                    ->schema([
                        CheckboxList::make('roles')
                            ->relationship('roles', 'name')
                            ->options(fn () => Role::query()->orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->bulkToggleable()
                            ->columns(3)
                            ->gridDirection('row')
                            ->helperText('Assign this permission to roles'),
                    ])->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Permission Name')
                    ->sortable()
                    ->searchable()
                    ->weight('medium')
                    ->copyable()
                    ->copyMessage('Permission name copied!')
                    ->copyMessageDuration(1500)
                    ->description(fn (Permission $record): string => ucfirst(str_replace('-', ' ', $record->name))),

                TextColumn::make('guard_name')
                    ->label('Guard')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('roles_count')
                    ->label('Roles')
                    ->counts('roles')
                    ->badge()
                    ->color('success'),

                TextColumn::make('users_count')
                    ->label('Direct Users')
                    ->counts('users')
                    ->badge()
                    ->color('warning')
                    ->tooltip('Users who have this permission directly assigned'),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('guard_name')
                    ->options([
                        'web' => 'Web',
                        'api' => 'API',
                    ]),
                
                SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Delete Permission')
                    ->modalDescription('Are you sure you want to delete this permission? This action cannot be undone and will remove it from all roles.')
                    ->modalSubmitActionLabel('Yes, delete it'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Delete Selected Permissions')
                        ->modalDescription('Are you sure you want to delete these permissions? This action cannot be undone.')
                        ->modalSubmitActionLabel('Yes, delete them'),
                ]),
            ])
            ->defaultSort('name')
            ->striped()
            ->paginated([10, 25, 50, 100]);
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
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            // 'view' => Pages\ViewPermission::route('/{record}'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount(['roles', 'users']);
    }
}
