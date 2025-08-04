<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class VendorApi extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'api_name',
        'base_url',
        'version',
        'auth_type',
        'auth_credentials',
        'email',
        'password',
        'headers',
        'timeout',
        'rate_limit',
        'status',
        'last_tested_at',
        'is_healthy',
    ];

    protected $casts = [
        'headers' => 'array',
        'timeout' => 'integer',
        'rate_limit' => 'integer',
        'is_healthy' => 'boolean',
        'last_tested_at' => 'datetime',
        'email' => 'string',
        'password' => 'string',
    ];

    // Relationships
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function apiEndpoints()
    {
        return $this->hasMany(ApiEndpoint::class);
    }

    public function apiRequests()
    {
        return $this->hasMany(ApiRequest::class);
    }

    public function apiConfigurations()
    {
        return $this->hasMany(ApiConfiguration::class);
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

    public function scopeMaintenance($query)
    {
        return $query->where('status', 'maintenance');
    }

    public function scopeHealthy($query)
    {
        return $query->where('is_healthy', true);
    }

    public function scopeUnhealthy($query)
    {
        return $query->where('is_healthy', false);
    }

    // Accessors & Mutators
    public function getAuthCredentialsAttribute($value)
    {
        return $value ? Crypt::decrypt($value) : null;
    }

    public function setAuthCredentialsAttribute($value)
    {
        $this->attributes['auth_credentials'] = $value ? Crypt::encrypt($value) : null;
    }

    public function getFullUrlAttribute()
    {
        return rtrim($this->base_url, '/') . '/' . $this->version;
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

    public function isMaintenance()
    {
        return $this->status === 'maintenance';
    }

    public function isHealthy()
    {
        return $this->is_healthy;
    }

    public function markAsHealthy()
    {
        $this->update([
            'is_healthy' => true,
            'last_tested_at' => now(),
        ]);
    }

    public function markAsUnhealthy()
    {
        $this->update([
            'is_healthy' => false,
            'last_tested_at' => now(),
        ]);
    }
}