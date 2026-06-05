<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RolePermissionResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\CheckboxList;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Collection;
use Filament\Notifications\Notification;

class RolePermissionResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'System Management';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Role Permissions';

    protected static ?string $pluralModelLabel = 'Role Permission Management';

    protected static ?string $modelLabel = 'Role Permission';

    protected static ?string $slug = 'role-permissions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Role Permission Assignment')
                    ->schema([
                        Select::make('role_id')
                            ->label('Role')
                            ->options(fn () => static::roleOptions())
                            ->required()
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $role = Role::find($state);
                                    $permissions = $role ? $role->permissions->pluck('id')->toArray() : [];
                                    $set('permissions', $permissions);
                                }
                            })
                            ->placeholder('Select a role'),

                        CheckboxList::make('permissions')
                            ->label('Permissions')
                            ->options(fn () => static::permissionOptions())
                            ->searchable()
                            ->bulkToggleable()
                            ->columns(3)
                            ->gridDirection('row')
                            ->helperText('Select permissions for the chosen role'),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Role Name')
                    ->sortable()
                    ->searchable()
                    ->weight('medium')
                    ->copyable(),

                TextColumn::make('guard_name')
                    ->label('Guard')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('permissions')
                    ->label('Permissions')
                    ->formatStateUsing(function (Role $record) {
                        $permissions = $record->permissions->take(3)->pluck('name')->toArray();
                        $remainingCount = $record->permissions->count() - 3;
                        
                        $display = implode(', ', $permissions);
                        if ($remainingCount > 0) {
                            $display .= " (+{$remainingCount} more)";
                        }
                        
                        return $display ?: 'No permissions';
                    })
                    ->wrap()
                    ->tooltip(function (Role $record) {
                        return $record->permissions->pluck('name')->implode(', ') ?: 'No permissions assigned';
                    }),

                TextColumn::make('permissions_count')
                    ->label('Total Permissions')
                    ->counts('permissions')
                    ->badge()
                    ->color('success'),

                TextColumn::make('users_count')
                    ->label('Users')
                    ->counts('users')
                    ->badge()
                    ->color('warning'),

                TextColumn::make('created_at')
                    ->label('Created')
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
            ])
            ->actions([
                Action::make('managePermissions')
                    ->label('Manage Permissions')
                    ->icon('heroicon-o-key')
                    ->color('primary')
                    ->form([
                        Forms\Components\Section::make('Manage Permissions for Role')
                            ->schema([
                                Forms\Components\Placeholder::make('role_info')
                                    ->label('Role Information')
                                    ->content(function (Role $record) {
                                        return "Managing permissions for: **{$record->name}**";
                                    }),

                                CheckboxList::make('permissions')
                                    ->label('Permissions')
                                    ->options(fn () => static::permissionOptions())
                                    ->default(function (Role $record) {
                                        return $record->permissions->pluck('id')->toArray();
                                    })
                                    ->searchable()
                                    ->bulkToggleable()
                                    ->columns(3)
                                    ->gridDirection('row'),
                            ])
                    ])
                    ->action(function (Role $record, array $data) {
                        $record->syncPermissions($data['permissions']);
                        
                        Notification::make()
                            ->title('Permissions Updated')
                            ->body("Permissions for role '{$record->name}' have been updated successfully.")
                            ->success()
                            ->send();
                    }),

                Action::make('clearPermissions')
                    ->label('Clear All')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Clear All Permissions')
                    ->modalDescription('Are you sure you want to remove all permissions from this role?')
                    ->modalSubmitActionLabel('Yes, clear all')
                    ->action(function (Role $record) {
                        $record->syncPermissions([]);
                        
                        Notification::make()
                            ->title('Permissions Cleared')
                            ->body("All permissions have been removed from role '{$record->name}'.")
                            ->success()
                            ->send();
                    }),

                EditAction::make()
                    ->form([
                        Forms\Components\Section::make('Role Information')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),

                                Forms\Components\TextInput::make('guard_name')
                                    ->required()
                                    ->default('web')
                                    ->maxLength(255),
                            ])->columns(2),

                        Forms\Components\Section::make('Permissions')
                            ->schema([
                                CheckboxList::make('permissions')
                                    ->relationship('permissions', 'name')
                                    ->options(fn () => static::permissionOptions())
                                    ->searchable()
                                    ->bulkToggleable()
                                    ->columns(3)
                                    ->gridDirection('row'),
                            ]),
                    ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('assignPermissions')
                        ->label('Assign Permissions')
                        ->icon('heroicon-o-plus')
                        ->color('success')
                        ->form([
                            CheckboxList::make('permissions')
                                ->label('Permissions to Assign')
                                ->options(fn () => static::permissionOptions())
                                ->searchable()
                                ->bulkToggleable()
                                ->columns(3)
                                ->gridDirection('row'),
                        ])
                        ->action(function (Collection $records, array $data) {
                            foreach ($records as $record) {
                                $record->givePermissionTo($data['permissions']);
                            }
                            
                            Notification::make()
                                ->title('Permissions Assigned')
                                ->body('Selected permissions have been assigned to the selected roles.')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\BulkAction::make('removePermissions')
                        ->label('Remove Permissions')
                        ->icon('heroicon-o-minus')
                        ->color('warning')
                        ->form([
                            CheckboxList::make('permissions')
                                ->label('Permissions to Remove')
                                ->options(fn () => static::permissionOptions())
                                ->searchable()
                                ->bulkToggleable()
                                ->columns(3)
                                ->gridDirection('row'),
                        ])
                        ->action(function (Collection $records, array $data) {
                            foreach ($records as $record) {
                                $record->revokePermissionTo($data['permissions']);
                            }
                            
                            Notification::make()
                                ->title('Permissions Removed')
                                ->body('Selected permissions have been removed from the selected roles.')
                                ->success()
                                ->send();
                        }),
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
            'index' => Pages\ListRolePermissions::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['permissions'])
            ->withCount(['permissions', 'users']);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    private static function permissionOptions()
    {
        return Permission::query()
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    private static function roleOptions()
    {
        return Role::query()
            ->orderBy('name')
            ->pluck('name', 'id');
    }
}
