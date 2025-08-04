<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiEndpoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_api_id',
        'name',
        'path',
        'method',
        'description',
        'parameters',
        'response_format',
        'requires_auth',
        'status',
    ];

    protected $casts = [
        'parameters' => 'array',
        'response_format' => 'array',
        'requires_auth' => 'boolean',
    ];

    // Relationships
    public function vendorApi()
    {
        return $this->belongsTo(VendorApi::class);
    }

    public function apiRequests()
    {
        return $this->hasMany(ApiRequest::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeDeprecated($query)
    {
        return $query->where('status', 'deprecated');
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('method', strtoupper($method));
    }

    public function scopeRequiresAuth($query)
    {
        return $query->where('requires_auth', true);
    }

    public function scopeNoAuth($query)
    {
        return $query->where('requires_auth', false);
    }

    // Accessors & Mutators
    public function getFullUrlAttribute()
    {
        return rtrim($this->vendorApi->full_url, '/') . '/' . ltrim($this->path, '/');
    }

    public function getMethodColorAttribute()
    {
        $colors = [
            'GET' => 'green',
            'POST' => 'blue',
            'PUT' => 'yellow',
            'PATCH' => 'orange',
            'DELETE' => 'red',
        ];

        return $colors[$this->method] ?? 'gray';
    }

    // Methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isInactive()
    {
        return $this->status === 'inactive';
    }

    public function isDeprecated()
    {
        return $this->status === 'deprecated';
    }

    public function requiresAuth()
    {
        return $this->requires_auth;
    }

    public function getParameterNames()
    {
        if (!$this->parameters) {
            return [];
        }

        return array_keys($this->parameters);
    }

    public function getRequiredParameters()
    {
        if (!$this->parameters) {
            return [];
        }

        return array_filter($this->parameters, function ($param) {
            return isset($param['required']) && $param['required'];
        });
    }

    public function getOptionalParameters()
    {
        if (!$this->parameters) {
            return [];
        }

        return array_filter($this->parameters, function ($param) {
            return !isset($param['required']) || !$param['required'];
        });
    }
}