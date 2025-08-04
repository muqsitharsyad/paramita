<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ApiRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_api_id',
        'api_endpoint_id',
        'request_id',
        'method',
        'url',
        'headers',
        'parameters',
        'request_body',
        'response_code',
        'response_headers',
        'response_body',
        'response_time',
        'status',
        'error_message',
        'requested_at',
        'responded_at',
    ];

    protected $casts = [
        'headers' => 'array',
        'parameters' => 'array',
        'response_headers' => 'array',
        'response_code' => 'integer',
        'response_time' => 'integer',
        'requested_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    // Boot method untuk auto-generate request_id
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->request_id) {
                $model->request_id = Str::uuid();
            }
            if (!$model->requested_at) {
                $model->requested_at = now();
            }
        });
    }

    // Relationships
    public function vendorApi()
    {
        return $this->belongsTo(VendorApi::class);
    }

    public function apiEndpoint()
    {
        return $this->belongsTo(ApiEndpoint::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeTimeout($query)
    {
        return $query->where('status', 'timeout');
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('method', strtoupper($method));
    }

    public function scopeByResponseCode($query, $code)
    {
        return $query->where('response_code', $code);
    }

    public function scopeSuccessfulResponse($query)
    {
        return $query->whereBetween('response_code', [200, 299]);
    }

    public function scopeClientError($query)
    {
        return $query->whereBetween('response_code', [400, 499]);
    }

    public function scopeServerError($query)
    {
        return $query->whereBetween('response_code', [500, 599]);
    }

    public function scopeSlowRequests($query, $threshold = 1000)
    {
        return $query->where('response_time', '>', $threshold);
    }

    // Accessors & Mutators
    public function getResponseTimeInSecondsAttribute()
    {
        return $this->response_time ? $this->response_time / 1000 : null;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'yellow',
            'success' => 'green',
            'failed' => 'red',
            'timeout' => 'orange',
        ];

        return $colors[$this->status] ?? 'gray';
    }

    public function getResponseCodeColorAttribute()
    {
        if ($this->response_code >= 200 && $this->response_code < 300) {
            return 'green';
        } elseif ($this->response_code >= 300 && $this->response_code < 400) {
            return 'blue';
        } elseif ($this->response_code >= 400 && $this->response_code < 500) {
            return 'yellow';
        } elseif ($this->response_code >= 500) {
            return 'red';
        }

        return 'gray';
    }

    // Methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isSuccess()
    {
        return $this->status === 'success';
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }

    public function isTimeout()
    {
        return $this->status === 'timeout';
    }

    public function isSuccessfulResponse()
    {
        return $this->response_code >= 200 && $this->response_code < 300;
    }

    public function isClientError()
    {
        return $this->response_code >= 400 && $this->response_code < 500;
    }

    public function isServerError()
    {
        return $this->response_code >= 500;
    }

    public function markAsSuccess($responseCode, $responseHeaders = null, $responseBody = null, $responseTime = null)
    {
        $this->update([
            'status' => 'success',
            'response_code' => $responseCode,
            'response_headers' => $responseHeaders,
            'response_body' => $responseBody,
            'response_time' => $responseTime,
            'responded_at' => now(),
        ]);
    }

    public function markAsFailed($errorMessage, $responseCode = null, $responseTime = null)
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'response_code' => $responseCode,
            'response_time' => $responseTime,
            'responded_at' => now(),
        ]);
    }

    public function markAsTimeout($responseTime = null)
    {
        $this->update([
            'status' => 'timeout',
            'error_message' => 'Request timeout',
            'response_time' => $responseTime,
            'responded_at' => now(),
        ]);
    }
}