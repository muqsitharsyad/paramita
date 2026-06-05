<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JsonTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'category',
        'description',
        'template_data',
        'version',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'template_data' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * API endpoints using this template
     */
    public function endpoints()
    {
        return $this->hasMany(ApiEndpoint::class, 'json_template_id');
    }

    /**
     * Pages that use this template
     */
    public function pages()
    {
        return $this->belongsToMany(Page::class, 'page_template')
            ->withPivot('display_order')
            ->withTimestamps();
    }

    /**
     * Get the creator of this template
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the last updater of this template
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope for active templates only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific category
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
