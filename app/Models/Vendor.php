<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'company_name',
        'description',
        'contact_person',
        'email',
        'phone',
        'address',
        'city',
        'province',
        'postal_code',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // Relationships
    public function vendorApis()
    {
        return $this->hasMany(VendorApi::class);
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

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    // Accessors & Mutators
    public function getFullAddressAttribute()
    {
        return $this->address . ', ' . $this->city . ', ' . $this->province . ' ' . $this->postal_code;
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

    public function isSuspended()
    {
        return $this->status === 'suspended';
    }
}