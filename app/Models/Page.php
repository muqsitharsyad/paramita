<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class Page extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'icon',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function jsonTemplates()
    {
        return $this->belongsToMany(JsonTemplate::class, 'page_template')
            ->withPivot('display_order')
            ->orderBy('page_template.display_order')
            ->withTimestamps();
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'page_role')
            ->withTimestamps();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForRole($query, string $roleName)
    {
        return $query->whereHas('roles', fn($q) => $q->where('name', $roleName));
    }

    public function scopeForUser($query, $user)
    {
        if (!$user) return $query->whereRaw('1=0');

        $roleNames = $user->roles->pluck('name');
        return $query->whereHas('roles', fn($q) => $q->whereIn('name', $roleNames));
    }
}
