<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SidebarIcon extends Model
{
    protected $fillable = [
        'key',
        'label',
        'svg',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
