<?php

namespace App\Models;

use App\Helpers\RoleHelper;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'nip',
        'avatar',
        'status',
        'password',
        'unit_kerja_id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        $role = RoleHelper::getPrimaryRoleName($this);

        return $role && RoleHelper::canAccessPanel($role, $panel->getId());
    }
}
