# 🔌 PANDUAN INTEGRASI API - PARAMITA SYSTEM

## 📘 Daftar Isi

1. [Cara Kerja Sistem](#cara-kerja-sistem)
2. [Setup Vendor API](#setup-vendor-api)
3. [Contoh Implementasi](#contoh-implementasi)
4. [Authentication Methods](#authentication-methods)
5. [Response Format](#response-format)
6. [Error Handling](#error-handling)
7. [Best Practices](#best-practices)

---

## 🎯 Cara Kerja Sistem

Paramita bertindak sebagai **API Gateway** yang berada di antara aplikasi Anda dan vendor APIs eksternal:

```
┌────────────────┐         ┌──────────────┐         ┌──────────────┐
│  Your App      │  ────▶  │  PARAMITA    │  ────▶  │ Vendor API 1 │
│  (Frontend)    │         │  (Gateway)   │         └──────────────┘
└────────────────┘         │              │         ┌──────────────┐
                           │              │  ────▶  │ Vendor API 2 │
                           │              │         └──────────────┘
                           │              │         ┌──────────────┐
                           │              │  ────▶  │ Vendor API N │
                           └──────────────┘         └──────────────┘
```

**Keuntungan:**

-   ✅ Single point of authentication
-   ✅ Centralized logging & monitoring
-   ✅ Standardized response format
-   ✅ Easy vendor switching
-   ✅ Security & credential management

---

## 🚀 Setup Vendor API

### Step 1: Register Vendor

1. Login ke **Filament Admin Panel** (`/admin`)
2. Navigate ke **Vendors** menu
3. Click **Create**

**Field yang perlu diisi:**

```php
[
    'name' => 'Vendor ABC',                    // Nama vendor
    'code' => 'VENDOR_ABC',                    // Kode unik (uppercase)
    'company_name' => 'PT Vendor ABC Indonesia',
    'description' => 'Deskripsi vendor...',
    'contact_person' => 'John Doe',
    'email' => 'contact@vendorabc.com',
    'phone' => '+62812345678',
    'address' => 'Jl. Example No. 123',
    'city' => 'Jakarta',
    'province' => 'DKI Jakarta',
    'postal_code' => '12345',
    'status' => 'active'                       // active | inactive | suspended
]
```

---

### Step 2: Configure Vendor API

1. Navigate ke **Vendor APIs** menu
2. Click **Create**

**Field konfigurasi:**

#### A. API Information

```php
[
    'vendor_id' => 1,                          // Pilih vendor yang sudah dibuat
    'api_name' => 'Vendor ABC API',            // Nama API
    'base_url' => 'https://api.vendorabc.com/api/v1',  // Base URL
    'version' => 'v1',                         // Versi API
    'status' => 'active'                       // active | inactive | maintenance
]
```

#### B. Authentication Configuration

**Contoh 1: Bearer Token**

```php
[
    'auth_type' => 'bearer_token',
    'auth_credentials' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...'  // Token akan di-encrypt
]
```

**Contoh 2: API Key**

```php
[
    'auth_type' => 'api_key',
    'auth_credentials' => 'abc123def456ghi789'
]
```

**Contoh 3: Auto-login (Email & Password)**

```php
[
    'auth_type' => 'bearer_token',
    'email' => 'api@yourcompany.com',          // Email untuk login
    'password' => 'your-password',             // Password
    'auth_credentials' => null                 // Token akan di-generate otomatis
]
```

**Contoh 4: Basic Authentication**

```php
[
    'auth_type' => 'basic_auth',
    'auth_credentials' => 'username:password'  // Format: username:password
]
```

#### C. Additional Configuration

```php
[
    'headers' => [                             // Custom headers (opsional)
        'Accept' => 'application/json',
        'X-Custom-Header' => 'value',
        'X-API-Version' => 'v1'
    ],
    'timeout' => 30,                           // Timeout dalam detik
    'rate_limit' => 1000                       // Maksimal request per menit
]
```

---

### Step 3: Define API Endpoints

1. Navigate ke **API Endpoints** menu
2. Click **Create**

**Contoh Konfigurasi Endpoint:**

#### Endpoint 1: Get Users

```php
[
    'vendor_api_id' => 1,                      // Pilih Vendor API
    'name' => 'Get Users',                     // Nama endpoint
    'path' => '/users',                        // Path endpoint
    'method' => 'GET',                         // HTTP method
    'description' => 'Retrieve list of users',
    'parameters' => [                          // Parameter yang diperlukan
        'page' => 'Page number (default: 1)',
        'limit' => 'Items per page (default: 10)',
        'search' => 'Search by name or email'
    ],
    'requires_auth' => true,                   // Perlu authentication
    'status' => 'active'                       // active | inactive | deprecated
]
```

#### Endpoint 2: Dashboard Data

```php
[
    'vendor_api_id' => 1,
    'name' => 'dashboard',                     // Nama special untuk dashboard
    'path' => '/dashboard',
    'method' => 'GET',
    'description' => 'Get dashboard statistics',
    'parameters' => null,
    'requires_auth' => true,
    'status' => 'active'
]
```

#### Endpoint 3: Create User

```php
[
    'vendor_api_id' => 1,
    'name' => 'Create User',
    'path' => '/users',
    'method' => 'POST',
    'description' => 'Create new user',
    'parameters' => [
        'name' => 'User full name (required)',
        'email' => 'User email (required)',
        'password' => 'User password (required)',
        'role' => 'User role (optional)'
    ],
    'requires_auth' => true,
    'status' => 'active'
]
```

---

## 💻 Contoh Implementasi

### 1. Simple API Call (Tanpa Authentication)

```php
<?php

use App\Models\ApiEndpoint;
use Illuminate\Support\Facades\Http;

class MyController extends Controller
{
    public function getPublicData()
    {
        // Get endpoint configuration
        $endpoint = ApiEndpoint::where('name', 'Get Public Data')
            ->where('status', 'active')
            ->with('vendorApi')
            ->first();

        if (!$endpoint) {
            return response()->json([
                'status' => 'error',
                'message' => 'Endpoint not found'
            ], 404);
        }

        // Build URL
        $url = rtrim($endpoint->vendorApi->base_url, '/') . '/' . ltrim($endpoint->path, '/');

        // Make request
        $response = Http::withOptions(['verify' => false])
            ->timeout($endpoint->vendorApi->timeout)
            ->get($url);

        if ($response->successful()) {
            return response()->json([
                'status' => 'success',
                'data' => $response->json()
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'API call failed',
            'error' => $response->body()
        ], $response->status());
    }
}
```

---

### 2. API Call dengan Authentication

```php
<?php

use App\Models\ApiEndpoint;
use App\Helpers\VendorApiAuthHelper;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MyController extends Controller
{
    public function getUsersFromVendor()
    {
        // Get endpoint configuration
        $endpoint = ApiEndpoint::where('name', 'Get Users')
            ->where('status', 'active')
            ->with('vendorApi')
            ->first();

        if (!$endpoint) {
            return $this->errorResponse('Endpoint not found', 404);
        }

        $vendorApi = $endpoint->vendorApi;
        $baseUrl = rtrim($vendorApi->base_url, '/');

        // Create HTTP client
        $client = Http::withHeaders(['Accept' => 'application/json'])
            ->withOptions(['verify' => false])
            ->timeout($vendorApi->timeout);

        // Authenticate if required
        if ($endpoint->requires_auth) {
            $authClient = VendorApiAuthHelper::authenticate($vendorApi, $baseUrl);

            if (!$authClient) {
                Log::error('Authentication failed for vendor: ' . $vendorApi->api_name);
                return $this->errorResponse('Authentication failed', 401);
            }

            $client = $authClient;
        }

        // Build URL with parameters
        $url = $baseUrl . $endpoint->path;
        $params = [
            'page' => request('page', 1),
            'limit' => request('limit', 10),
        ];

        // Make API call
        try {
            $response = $client->get($url, $params);

            if ($response->successful()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $response->json('data'),
                    'meta' => [
                        'vendor' => $vendorApi->api_name,
                        'endpoint' => $endpoint->name
                    ]
                ]);
            }

            return $this->errorResponse(
                'Vendor API returned error',
                $response->status(),
                $response->json()
            );

        } catch (\Exception $e) {
            Log::error('API call exception: ' . $e->getMessage());
            return $this->errorResponse('API call failed', 500);
        }
    }

    private function errorResponse($message, $code, $data = null)
    {
        $response = [
            'status' => 'error',
            'code' => $code,
            'message' => $message
        ];

        if ($data) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }
}
```

---

### 3. API Call dengan Logging

```php
<?php

use App\Models\ApiEndpoint;
use App\Models\ApiRequest;
use App\Helpers\VendorApiAuthHelper;
use Illuminate\Support\Facades\Http;

class MyController extends Controller
{
    public function callApiWithLogging()
    {
        $endpoint = ApiEndpoint::where('name', 'Get Users')
            ->with('vendorApi')
            ->first();

        $vendorApi = $endpoint->vendorApi;
        $baseUrl = rtrim($vendorApi->base_url, '/');
        $url = $baseUrl . $endpoint->path;

        // Create log entry (PENDING)
        $log = ApiRequest::create([
            'vendor_api_id' => $vendorApi->id,
            'api_endpoint_id' => $endpoint->id,
            'method' => $endpoint->method,
            'url' => $url,
            'headers' => ['Accept' => 'application/json'],
            'parameters' => request()->all(),
            'status' => 'pending',
            'requested_at' => now()
        ]);

        // Authenticate
        $client = Http::withHeaders(['Accept' => 'application/json'])
            ->timeout($vendorApi->timeout);

        if ($endpoint->requires_auth) {
            $authClient = VendorApiAuthHelper::authenticate($vendorApi, $baseUrl);
            if ($authClient) {
                $client = $authClient;
            }
        }

        // Make API call
        $startTime = microtime(true);

        try {
            $response = $client->get($url, request()->all());
            $endTime = microtime(true);
            $responseTime = ($endTime - $startTime) * 1000; // Convert to ms

            // Update log entry (SUCCESS)
            $log->update([
                'response_code' => $response->status(),
                'response_headers' => $response->headers(),
                'response_body' => $response->body(),
                'response_time' => round($responseTime),
                'status' => $response->successful() ? 'success' : 'failed',
                'error_message' => $response->successful() ? null : $response->body(),
                'responded_at' => now()
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $response->json(),
                'meta' => [
                    'request_id' => $log->request_id,
                    'response_time' => round($responseTime) . 'ms'
                ]
            ]);

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $responseTime = ($endTime - $startTime) * 1000;

            // Update log entry (FAILED)
            $log->update([
                'response_time' => round($responseTime),
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'responded_at' => now()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'API call failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
```

---

### 4. Dashboard Multi-Vendor Integration

```php
<?php

use App\Models\ApiEndpoint;
use App\Helpers\VendorApiAuthHelper;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get all dashboard endpoints from active vendors
        $dashboardEndpoints = ApiEndpoint::with(['vendorApi', 'vendorApi.vendor'])
            ->where('name', 'dashboard')
            ->where('status', 'active')
            ->whereHas('vendorApi', function($query) {
                $query->where('status', 'active');
            })
            ->get();

        $vendorDashboardData = [];

        foreach ($dashboardEndpoints as $endpoint) {
            try {
                $vendorApi = $endpoint->vendorApi;
                $baseUrl = rtrim($vendorApi->base_url, '/');

                // Create HTTP client
                $client = Http::withHeaders(['Accept' => 'application/json'])
                    ->withOptions(['verify' => false])
                    ->timeout($vendorApi->timeout);

                // Authenticate if required
                if ($endpoint->requires_auth) {
                    $authClient = VendorApiAuthHelper::authenticate($vendorApi, $baseUrl);

                    if (!$authClient) {
                        Log::warning('Failed to authenticate with vendor: ' . $vendorApi->api_name);
                        $vendorDashboardData[$vendorApi->api_name] = [
                            'status' => 'error',
                            'message' => 'Authentication failed'
                        ];
                        continue;
                    }

                    $client = $authClient;
                }

                // Make API call
                $url = $baseUrl . $endpoint->path;
                $response = $client->get($url);

                if ($response->successful()) {
                    $vendorDashboardData[$vendorApi->api_name] = [
                        'status' => 'success',
                        'data' => $response->json('data'),
                        'vendor' => $vendorApi->vendor->name
                    ];
                } else {
                    $vendorDashboardData[$vendorApi->api_name] = [
                        'status' => 'error',
                        'message' => 'API returned error: ' . $response->status()
                    ];
                    Log::error('Dashboard API error from ' . $vendorApi->api_name, [
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);
                }

            } catch (\Exception $e) {
                Log::error('Exception calling vendor API: ' . $e->getMessage(), [
                    'vendor' => $endpoint->vendorApi->api_name,
                    'exception' => $e->getMessage()
                ]);

                $vendorDashboardData[$endpoint->vendorApi->api_name] = [
                    'status' => 'error',
                    'message' => 'Connection failed'
                ];
            }
        }

        return view('dashboard', [
            'dashboardData' => $vendorDashboardData,
            'role' => optional($user->roles->first())->name,
            'user' => $user
        ]);
    }
}
```

---

## 🔐 Authentication Methods

### 1. Bearer Token

**Setup:**

```php
[
    'auth_type' => 'bearer_token',
    'auth_credentials' => 'your-bearer-token-here'
]
```

**Usage:**

```php
$client = Http::withToken($vendorApi->auth_credentials)
    ->get($url);
```

---

### 2. API Key

**Setup:**

```php
[
    'auth_type' => 'api_key',
    'auth_credentials' => 'your-api-key-here',
    'headers' => [
        'X-API-Key' => '{{auth_credentials}}'  // Atau header lain yang diperlukan
    ]
]
```

**Usage:**

```php
$client = Http::withHeaders([
    'X-API-Key' => $vendorApi->auth_credentials
])->get($url);
```

---

### 3. Basic Authentication

**Setup:**

```php
[
    'auth_type' => 'basic_auth',
    'auth_credentials' => 'username:password'
]
```

**Usage:**

```php
list($username, $password) = explode(':', $vendorApi->auth_credentials);
$client = Http::withBasicAuth($username, $password)
    ->get($url);
```

---

### 4. Auto-Login (Email & Password)

**Setup:**

```php
[
    'auth_type' => 'bearer_token',
    'email' => 'api@yourcompany.com',
    'password' => 'your-password',
    'auth_credentials' => null  // Will be auto-populated
]
```

**Usage:**

```php
// Automatically handled by VendorApiAuthHelper
$client = VendorApiAuthHelper::authenticate($vendorApi, $baseUrl);
```

**How it works:**

1. POST to `/login` endpoint with email & password
2. Extract `access_token` or `token` from response
3. Save token to `auth_credentials` (encrypted)
4. Return HTTP client with Bearer token

---

### 5. OAuth 2.0

**Setup:**

```php
[
    'auth_type' => 'oauth2',
    'auth_credentials' => 'client_id:client_secret:redirect_uri'
]
```

**Manual Implementation Required:**

```php
// You need to implement OAuth2 flow manually
// 1. Get authorization code
// 2. Exchange for access token
// 3. Store access token in auth_credentials
// 4. Implement token refresh logic
```

---

## 📋 Response Format

### Standard Success Response

```json
{
    "status": "success",
    "code": 200,
    "message": "Data retrieved successfully",
    "timestamp": "2025-11-06T10:30:00.000000Z",
    "data": {
        "users": [
            {
                "id": 1,
                "name": "John Doe",
                "email": "john@example.com"
            }
        ]
    },
    "meta": {
        "vendor": "Vendor ABC API",
        "endpoint": "Get Users",
        "response_time": "245ms"
    }
}
```

### Standard Error Response

```json
{
    "status": "error",
    "code": 401,
    "message": "Authentication failed",
    "timestamp": "2025-11-06T10:30:00.000000Z",
    "errors": {
        "token": ["Invalid or expired token"]
    }
}
```

### Using JSON Templates

```php
use App\Helpers\JsonTemplateHelper;

return JsonTemplateHelper::jsonResponse(
    'api_response',           // Template name
    ['users' => $users],      // Data
    [                         // Variables
        'message' => 'Users retrieved successfully'
    ],
    'api',                    // Category
    200                       // HTTP code
);
```

---

## ⚠️ Error Handling

### 1. Connection Errors

```php
try {
    $response = $client->get($url);
} catch (\Illuminate\Http\Client\ConnectionException $e) {
    return response()->json([
        'status' => 'error',
        'code' => 503,
        'message' => 'Service temporarily unavailable',
        'error' => 'Cannot connect to vendor API'
    ], 503);
}
```

---

### 2. Timeout Errors

```php
try {
    $response = $client->timeout(30)->get($url);
} catch (\Illuminate\Http\Client\RequestException $e) {
    if ($e->getCode() === CURLE_OPERATION_TIMEDOUT) {
        return response()->json([
            'status' => 'error',
            'code' => 408,
            'message' => 'Request timeout',
            'error' => 'Vendor API did not respond in time'
        ], 408);
    }
}
```

---

### 3. Authentication Errors

```php
$authClient = VendorApiAuthHelper::authenticate($vendorApi, $baseUrl);

if (!$authClient) {
    return response()->json([
        'status' => 'error',
        'code' => 401,
        'message' => 'Authentication failed',
        'error' => 'Could not authenticate with vendor API'
    ], 401);
}
```

---

### 4. HTTP Error Responses

```php
$response = $client->get($url);

if ($response->failed()) {
    $statusCode = $response->status();

    return response()->json([
        'status' => 'error',
        'code' => $statusCode,
        'message' => match($statusCode) {
            400 => 'Bad request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not found',
            429 => 'Too many requests',
            500 => 'Internal server error',
            502 => 'Bad gateway',
            503 => 'Service unavailable',
            default => 'API error'
        },
        'error' => $response->json()
    ], $statusCode);
}
```

---

## 🎯 Best Practices

### 1. Always Use Timeout

```php
$client = Http::timeout($vendorApi->timeout ?? 30)
    ->get($url);
```

---

### 2. Handle SSL Verification

**Development:**

```php
$client = Http::withOptions(['verify' => false])
    ->get($url);
```

**Production:**

```php
$client = Http::withOptions(['verify' => true])
    ->get($url);
```

---

### 3. Use Logging

```php
Log::info('API call started', [
    'vendor' => $vendorApi->api_name,
    'endpoint' => $endpoint->name,
    'url' => $url
]);

// ... make API call ...

Log::info('API call completed', [
    'status' => $response->status(),
    'response_time' => $responseTime
]);
```

---

### 4. Cache Frequent Data

```php
use Illuminate\Support\Facades\Cache;

$cacheKey = "vendor_{$vendorApi->id}_dashboard";

$data = Cache::remember($cacheKey, 300, function() use ($client, $url) {
    $response = $client->get($url);
    return $response->json();
});
```

---

### 5. Implement Retry Logic

```php
$maxRetries = 3;
$retryCount = 0;

while ($retryCount < $maxRetries) {
    try {
        $response = $client->get($url);

        if ($response->successful()) {
            break;
        }

        $retryCount++;
        sleep(1); // Wait 1 second before retry

    } catch (\Exception $e) {
        $retryCount++;
        if ($retryCount >= $maxRetries) {
            throw $e;
        }
        sleep(1);
    }
}
```

---

### 6. Validate Response Format

```php
$response = $client->get($url);

if ($response->successful()) {
    $data = $response->json();

    // Validate required fields
    if (!isset($data['data']) || !isset($data['status'])) {
        Log::error('Invalid response format from vendor API', [
            'response' => $data
        ]);

        return response()->json([
            'status' => 'error',
            'message' => 'Invalid response format'
        ], 500);
    }

    return response()->json($data);
}
```

---

### 7. Rate Limiting

```php
use Illuminate\Support\Facades\RateLimiter;

$key = 'api_call_vendor_' . $vendorApi->id;
$maxAttempts = $vendorApi->rate_limit ?? 1000;
$decayMinutes = 1;

if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
    $seconds = RateLimiter::availableIn($key);

    return response()->json([
        'status' => 'error',
        'code' => 429,
        'message' => 'Too many requests',
        'retry_after' => $seconds
    ], 429);
}

RateLimiter::hit($key, $decayMinutes * 60);

// Make API call...
```

---

## 🧪 Testing API Endpoints

### Manual Test via Filament

1. Navigate ke **API Endpoints**
2. Click pada endpoint yang ingin ditest
3. Click **Test Endpoint** button
4. System akan:
    - Authenticate (jika required)
    - Call endpoint
    - Show response
    - Update health status
    - Log request/response

---

### Test via Code

```php
use App\Models\ApiEndpoint;
use App\Helpers\VendorApiAuthHelper;
use Illuminate\Support\Facades\Http;

class TestController extends Controller
{
    public function testEndpoint($endpointId)
    {
        $endpoint = ApiEndpoint::with('vendorApi')->findOrFail($endpointId);
        $vendorApi = $endpoint->vendorApi;
        $baseUrl = rtrim($vendorApi->base_url, '/');
        $url = $baseUrl . $endpoint->path;

        $client = Http::timeout($vendorApi->timeout);

        if ($endpoint->requires_auth) {
            $authClient = VendorApiAuthHelper::authenticate($vendorApi, $baseUrl);
            if ($authClient) {
                $client = $authClient;
            }
        }

        $startTime = microtime(true);

        try {
            $response = $client->get($url);
            $responseTime = (microtime(true) - $startTime) * 1000;

            // Update health status
            $endpoint->update([
                'health_status' => $response->successful() ? 'healthy' : 'unhealthy',
                'last_tested_at' => now(),
                'health_message' => $response->successful()
                    ? 'Response: ' . $response->status() . ' in ' . round($responseTime) . 'ms'
                    : 'Error: ' . $response->status()
            ]);

            return response()->json([
                'status' => 'success',
                'endpoint' => $endpoint->name,
                'url' => $url,
                'response_code' => $response->status(),
                'response_time' => round($responseTime) . 'ms',
                'health_status' => $endpoint->health_status,
                'response_body' => $response->json()
            ]);

        } catch (\Exception $e) {
            $endpoint->update([
                'health_status' => 'unhealthy',
                'last_tested_at' => now(),
                'health_message' => 'Exception: ' . $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Test failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
```

---

## 📞 Support

Jika Anda mengalami masalah dalam integrasi:

1. **Check Logs:** `storage/logs/laravel.log`
2. **Check API Request Logs:** Admin Panel → API Requests
3. **Test Endpoint:** Admin Panel → API Endpoints → Test
4. **Check Health Status:** Admin Panel → API Endpoints → Health Status
5. **Review Configuration:** Pastikan base_url, auth_type, dan credentials sudah benar

---

## 📝 Checklist Integrasi

-   [ ] Vendor sudah terdaftar dan status **active**
-   [ ] Vendor API sudah dikonfigurasi dengan benar
-   [ ] Base URL sudah benar (dengan versi API jika ada)
-   [ ] Authentication type dan credentials sudah sesuai
-   [ ] Endpoint sudah didefinisikan dengan method yang benar
-   [ ] Test endpoint berhasil dan status **healthy**
-   [ ] Authentication berhasil (jika required)
-   [ ] Response format sesuai ekspektasi
-   [ ] Logging berjalan dengan baik
-   [ ] Error handling sudah diimplementasikan
-   [ ] Timeout sudah diset dengan wajar
-   [ ] Rate limiting sudah dikonfigurasi (jika perlu)

---

**Dokumentasi dibuat:** November 6, 2025  
**Versi:** 1.0  
**Status:** Production Ready
