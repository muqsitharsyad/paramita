<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class ApiConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_api_id',
        'config_key',
        'config_value',
        'data_type',
        'description',
        'is_sensitive',
    ];

    protected $casts = [
        'is_sensitive' => 'boolean',
    ];

    // Relationships
    public function vendorApi()
    {
        return $this->belongsTo(VendorApi::class);
    }

    // Scopes
    public function scopeSensitive($query)
    {
        return $query->where('is_sensitive', true);
    }

    public function scopeNonSensitive($query)
    {
        return $query->where('is_sensitive', false);
    }

    public function scopeByDataType($query, $type)
    {
        return $query->where('data_type', $type);
    }

    public function scopeByKey($query, $key)
    {
        return $query->where('config_key', $key);
    }

    // Accessors & Mutators
    public function getConfigValueAttribute($value)
    {
        if (!$value) {
            return null;
        }

        // Decrypt jika data sensitif dan tipe encrypted
        if ($this->is_sensitive && $this->data_type === 'encrypted') {
            try {
                return Crypt::decrypt($value);
            } catch (\Exception $e) {
                return null;
            }
        }

        // Cast berdasarkan data type
        switch ($this->data_type) {
            case 'integer':
                return (int) $value;
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }

    public function setConfigValueAttribute($value)
    {
        if ($value === null) {
            $this->attributes['config_value'] = null;
            return;
        }

        // Encrypt jika data sensitif dan tipe encrypted
        if ($this->is_sensitive && $this->data_type === 'encrypted') {
            $this->attributes['config_value'] = Crypt::encrypt($value);
            return;
        }

        // Cast berdasarkan data type sebelum disimpan
        switch ($this->data_type) {
            case 'integer':
                $this->attributes['config_value'] = (int) $value;
                break;
            case 'boolean':
                $this->attributes['config_value'] = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
                break;
            case 'json':
                $this->attributes['config_value'] = json_encode($value);
                break;
            default:
                $this->attributes['config_value'] = (string) $value;
        }
    }

    public function getRawValueAttribute()
    {
        return $this->attributes['config_value'] ?? null;
    }

    public function getDisplayValueAttribute()
    {
        if ($this->is_sensitive) {
            return '***HIDDEN***';
        }

        return $this->config_value;
    }

    // Methods
    public function isSensitive()
    {
        return $this->is_sensitive;
    }

    public function isString()
    {
        return $this->data_type === 'string';
    }

    public function isInteger()
    {
        return $this->data_type === 'integer';
    }

    public function isBoolean()
    {
        return $this->data_type === 'boolean';
    }

    public function isJson()
    {
        return $this->data_type === 'json';
    }

    public function isEncrypted()
    {
        return $this->data_type === 'encrypted';
    }

    public function updateValue($value)
    {
        $this->update(['config_value' => $value]);
    }

    public function markAsSensitive()
    {
        $this->update(['is_sensitive' => true]);
    }

    public function markAsNonSensitive()
    {
        $this->update(['is_sensitive' => false]);
    }

    // Static methods untuk kemudahan akses
    public static function getByKey($vendorApiId, $key, $default = null)
    {
        $config = static::where('vendor_api_id', $vendorApiId)
            ->where('config_key', $key)
            ->first();

        return $config ? $config->config_value : $default;
    }

    public static function setByKey($vendorApiId, $key, $value, $dataType = 'string', $description = null, $isSensitive = false)
    {
        return static::updateOrCreate(
            [
                'vendor_api_id' => $vendorApiId,
                'config_key' => $key,
            ],
            [
                'config_value' => $value,
                'data_type' => $dataType,
                'description' => $description,
                'is_sensitive' => $isSensitive,
            ]
        );
    }
}