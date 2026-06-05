# 📚 ARSITEKTUR SISTEM PARAMITA

## 🎯 Overview Sistem

**Paramita** adalah platform **API Gateway dan Management System** yang berfungsi sebagai central hub untuk mengelola multiple vendor APIs dengan kemampuan logging, monitoring, authentication, dan standardisasi response format.

### Fungsi Utama:

-   ✅ **Central API Hub** - Mengelola multiple vendor APIs dalam satu platform
-   ✅ **API Request/Response Logger** - Tracking dan logging semua komunikasi API
-   ✅ **Authentication & Authorization** - Mengatur akses dan permissions berbasis role
-   ✅ **Dashboard & Monitoring** - Interface administrasi untuk monitoring real-time
-   ✅ **JSON Template Management** - Standardisasi format response API
-   ✅ **Health Check & Testing** - Monitoring kesehatan endpoint API vendor
-   ✅ **Configuration Management** - Pengelolaan konfigurasi API yang fleksibel

---

## 🏗️ Stack Teknologi

### Backend Framework

-   **Laravel 12.x** - PHP Framework
-   **PHP 8.2+** - Programming Language
-   **MySQL** - Database Management System

### Frontend & Admin Panel

-   **Filament 3.3** - Modern admin panel framework
-   **Blade Templates** - Laravel templating engine
-   **Vite** - Frontend build tool

### Authentication & Authorization

-   **Spatie Laravel Permission** - Role & Permission management
-   **Laravel Authentication** - Built-in authentication system

### API Integration

-   **Guzzle HTTP Client** - HTTP client untuk komunikasi dengan vendor APIs
-   **Laravel HTTP Client** - Wrapper untuk HTTP requests

---

## 📊 Arsitektur Database

### Entity Relationship Diagram (ERD)

```
┌─────────────┐          ┌──────────────┐          ┌─────────────────┐
│   Vendors   │──────┬───│ Vendor APIs  │──────┬───│  API Endpoints  │
└─────────────┘      │   └──────────────┘      │   └─────────────────┘
                     │                         │            │
                     │   ┌──────────────────┐  │            │
                     └───│ API Configs      │  │            │
                         └──────────────────┘  │            │
                                               │            │
                         ┌──────────────────┐  │            │
                         │  API Requests    │◄─┴────────────┘
                         │  (Logs)          │
                         └──────────────────┘

┌─────────────┐          ┌──────────────┐
│   Users     │──────────│ Unit Kerja   │
└─────────────┘          └──────────────┘
      │
      │                  ┌──────────────┐
      └──────────────────│  Roles       │
                         └──────────────┘
                                │
                         ┌──────────────┐
                         │ Permissions  │
                         └──────────────┘

┌─────────────────┐
│ JSON Templates  │
└─────────────────┘
```

### 1. **Vendors Table**

Menyimpan informasi perusahaan vendor penyedia API.

| Field          | Type            | Description                 |
| -------------- | --------------- | --------------------------- |
| id             | bigint          | Primary Key                 |
| name           | string          | Nama vendor                 |
| code           | string (unique) | Kode unik vendor            |
| company_name   | string          | Nama perusahaan             |
| description    | text            | Deskripsi vendor            |
| contact_person | string          | Nama kontak person          |
| email          | string          | Email vendor                |
| phone          | string          | Nomor telepon               |
| address        | text            | Alamat lengkap              |
| city           | string          | Kota                        |
| province       | string          | Provinsi                    |
| postal_code    | string          | Kode pos                    |
| status         | enum            | active, inactive, suspended |
| timestamps     | -               | created_at, updated_at      |

**Status Options:**

-   `active`: Vendor aktif dan dapat digunakan
-   `inactive`: Vendor tidak aktif
-   `suspended`: Vendor ditangguhkan

---

### 2. **Vendor APIs Table**

Menyimpan konfigurasi API dari setiap vendor.

| Field            | Type             | Description                                         |
| ---------------- | ---------------- | --------------------------------------------------- |
| id               | bigint           | Primary Key                                         |
| vendor_id        | bigint           | Foreign Key ke Vendors                              |
| api_name         | string           | Nama API                                            |
| base_url         | string           | URL dasar API (e.g., https://vendor-api.com/api/v1) |
| version          | string           | Versi API (default: v1)                             |
| auth_type        | enum             | none, api_key, bearer_token, basic_auth, oauth2     |
| auth_credentials | text (encrypted) | Kredensial autentikasi (encrypted)                  |
| email            | string           | Email untuk login API                               |
| password         | string           | Password untuk login API                            |
| headers          | json             | Header tambahan yang diperlukan                     |
| timeout          | integer          | Timeout dalam detik (default: 30)                   |
| rate_limit       | integer          | Limit request per menit                             |
| status           | enum             | active, inactive, maintenance                       |
| last_tested_at   | timestamp        | Waktu terakhir ditest                               |
| is_healthy       | boolean          | Status kesehatan API                                |
| timestamps       | -                | created_at, updated_at                              |

**Auth Type Options:**

-   `none`: Tanpa autentikasi
-   `api_key`: Menggunakan API Key
-   `bearer_token`: Menggunakan Bearer Token
-   `basic_auth`: HTTP Basic Authentication
-   `oauth2`: OAuth 2.0

**Security:**

-   `auth_credentials` di-encrypt menggunakan Laravel Encryption (Crypt)
-   Password di-hash secara otomatis

---

### 3. **API Endpoints Table**

Menyimpan daftar endpoint yang tersedia dari setiap Vendor API.

| Field            | Type      | Description                                    |
| ---------------- | --------- | ---------------------------------------------- |
| id               | bigint    | Primary Key                                    |
| vendor_api_id    | bigint    | Foreign Key ke Vendor APIs                     |
| name             | string    | Nama endpoint (e.g., "Get Users", "Dashboard") |
| path             | string    | Path endpoint (e.g., /users, /dashboard)       |
| method           | enum      | GET, POST, PUT, PATCH, DELETE                  |
| description      | text      | Deskripsi endpoint                             |
| parameters       | json      | Parameter yang diperlukan                      |
| requires_auth    | boolean   | Apakah butuh autentikasi                       |
| status           | enum      | active, inactive, deprecated                   |
| health_status    | enum      | unknown, healthy, unhealthy, warning           |
| last_tested_at   | timestamp | Waktu terakhir ditest                          |
| health_message   | text      | Pesan status kesehatan                         |
| json_template_id | bigint    | Foreign Key ke JSON Templates                  |
| timestamps       | -         | created_at, updated_at                         |

**HTTP Methods:**

-   `GET`: Retrieve data
-   `POST`: Create new resource
-   `PUT`: Update entire resource
-   `PATCH`: Update partial resource
-   `DELETE`: Delete resource

**Health Status:**

-   `unknown`: Belum pernah ditest
-   `healthy`: Endpoint berfungsi normal
-   `unhealthy`: Endpoint bermasalah
-   `warning`: Endpoint lambat atau ada warning

---

### 4. **API Requests Table**

Logging semua request dan response API untuk audit dan monitoring.

| Field            | Type          | Description                       |
| ---------------- | ------------- | --------------------------------- |
| id               | bigint        | Primary Key                       |
| vendor_api_id    | bigint        | Foreign Key ke Vendor APIs        |
| api_endpoint_id  | bigint        | Foreign Key ke API Endpoints      |
| request_id       | string (uuid) | ID unik request                   |
| method           | enum          | GET, POST, PUT, PATCH, DELETE     |
| url              | text          | URL lengkap request               |
| headers          | json          | Headers request                   |
| parameters       | json          | Parameter request                 |
| request_body     | longtext      | Body request (untuk POST/PUT)     |
| response_code    | integer       | HTTP response code                |
| response_headers | json          | Response headers                  |
| response_body    | longtext      | Response body                     |
| response_time    | integer       | Response time dalam ms            |
| status           | enum          | pending, success, failed, timeout |
| error_message    | text          | Pesan error jika ada              |
| requested_at     | timestamp     | Waktu request                     |
| responded_at     | timestamp     | Waktu response                    |
| timestamps       | -             | created_at, updated_at            |

**Status Options:**

-   `pending`: Request sedang diproses
-   `success`: Request berhasil (2xx)
-   `failed`: Request gagal (4xx, 5xx)
-   `timeout`: Request timeout

**Auto-generated:**

-   `request_id` otomatis generate UUID saat insert
-   `requested_at` otomatis set ke current timestamp

---

### 5. **API Configurations Table**

Menyimpan konfigurasi tambahan untuk setiap Vendor API secara fleksibel (Key-Value).

| Field         | Type    | Description                               |
| ------------- | ------- | ----------------------------------------- |
| id            | bigint  | Primary Key                               |
| vendor_api_id | bigint  | Foreign Key ke Vendor APIs                |
| config_key    | string  | Nama konfigurasi                          |
| config_value  | text    | Nilai konfigurasi                         |
| data_type     | enum    | string, integer, boolean, json, encrypted |
| description   | text    | Deskripsi konfigurasi                     |
| is_sensitive  | boolean | Apakah data sensitif                      |
| timestamps    | -       | created_at, updated_at                    |

**Data Type Options:**

-   `string`: Text biasa
-   `integer`: Angka
-   `boolean`: true/false
-   `json`: JSON data
-   `encrypted`: Data ter-enkripsi

**Security:**

-   Data dengan `is_sensitive = true` dan `data_type = encrypted` akan di-encrypt

---

### 6. **JSON Templates Table**

Menyimpan template format response JSON untuk standardisasi.

| Field         | Type      | Description                     |
| ------------- | --------- | ------------------------------- |
| id            | bigint    | Primary Key                     |
| name          | string    | Nama template                   |
| category      | string    | Kategori template               |
| description   | text      | Deskripsi template              |
| template_data | longtext  | Data template dalam format JSON |
| version       | string    | Versi template (default: 1.0)   |
| is_active     | boolean   | Status aktif                    |
| created_by    | bigint    | User yang membuat               |
| updated_by    | bigint    | User yang terakhir update       |
| timestamps    | -         | created_at, updated_at          |
| deleted_at    | timestamp | Soft delete                     |

**Features:**

-   Support variable replacement dengan format `{{variable_name}}`
-   Soft deletes untuk history tracking
-   Versioning support

**Contoh Template:**

```json
{
    "status": "{{status}}",
    "code": "{{code}}",
    "message": "{{message}}",
    "timestamp": "{{timestamp}}",
    "data": {}
}
```

---

### 7. **Users Table**

Menyimpan data pengguna sistem.

| Field             | Type            | Description               |
| ----------------- | --------------- | ------------------------- |
| id                | bigint          | Primary Key               |
| name              | string          | Nama lengkap              |
| email             | string (unique) | Email login               |
| nip               | string          | Nomor Induk Pegawai       |
| avatar            | string          | Path foto profil          |
| unit_kerja_id     | bigint          | Foreign Key ke Unit Kerja |
| status            | string          | Status user               |
| password          | string (hashed) | Password (hashed)         |
| email_verified_at | timestamp       | Waktu verifikasi email    |
| remember_token    | string          | Token remember me         |
| timestamps        | -               | created_at, updated_at    |

**Features:**

-   Terintegrasi dengan **Spatie Laravel Permission**
-   Support **Role-Based Access Control (RBAC)**
-   Password auto-hashed
-   Filament authentication

---

### 8. **Unit Kerja Table**

Menyimpan data unit kerja/departemen organisasi.

| Field      | Type   | Description            |
| ---------- | ------ | ---------------------- |
| id         | bigint | Primary Key            |
| nama       | string | Nama unit kerja        |
| kode       | string | Kode unit kerja        |
| keterangan | text   | Keterangan             |
| timestamps | -      | created_at, updated_at |

---

### 9. **Roles & Permissions Tables** (Spatie Laravel Permission)

Sistem role dan permission menggunakan package Spatie.

**Roles Table:**

-   id
-   name (e.g., admin, user, operator)
-   guard_name
-   timestamps

**Permissions Table:**

-   id
-   name (e.g., view_vendors, manage_apis)
-   guard_name
-   timestamps

**Pivot Tables:**

-   `model_has_roles` - Many-to-many: Users ↔ Roles
-   `model_has_permissions` - Many-to-many: Users ↔ Permissions
-   `role_has_permissions` - Many-to-many: Roles ↔ Permissions

---

## 🔄 Flow Arsitektur Aplikasi

### 1. **Request Flow - Dari User ke Vendor API**

```
┌─────────────┐
│   Client    │
│  (Browser)  │
└──────┬──────┘
       │ 1. HTTP Request
       ▼
┌─────────────────────┐
│  Paramita System    │
│  (Laravel App)      │
│                     │
│  ┌───────────────┐  │
│  │ Authentication│  │ 2. Check Auth
│  │ Middleware    │  │    & Permission
│  └───────┬───────┘  │
│          │          │
│          ▼          │
│  ┌───────────────┐  │
│  │  Controller   │  │ 3. Process Request
│  │  Dashboard/   │  │    & Validate
│  │  API Logic    │  │
│  └───────┬───────┘  │
│          │          │
│          ▼          │
│  ┌───────────────┐  │
│  │  VendorApi    │  │ 4. Get Vendor Config
│  │  Helper       │  │    & Auth
│  └───────┬───────┘  │
│          │          │
└──────────┼──────────┘
           │ 5. HTTP Request
           ▼
┌─────────────────────┐
│   Vendor API        │
│   (External)        │ 6. Process & Return
│                     │    Response
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│  Paramita System    │
│                     │
│  ┌───────────────┐  │
│  │ Log Request   │  │ 7. Save to
│  │ & Response    │  │    api_requests
│  └───────────────┘  │
│          │          │
│          ▼          │
│  ┌───────────────┐  │
│  │ JSON Template │  │ 8. Format Response
│  │ Helper        │  │    (Optional)
│  └───────┬───────┘  │
└──────────┼──────────┘
           │ 9. JSON Response
           ▼
┌─────────────┐
│   Client    │
│  (Browser)  │
└─────────────┘
```

### 2. **Authentication Flow**

```
┌─────────────┐
│   User      │
└──────┬──────┘
       │ 1. Login Request
       │    (email + password)
       ▼
┌─────────────────────┐
│ AuthController      │
│                     │
│ • validate()        │ 2. Validate credentials
│ • Auth::attempt()   │ 3. Check database
│                     │
└──────┬──────────────┘
       │
       ▼ 4. Success
┌─────────────────────┐
│ Session Manager     │
│                     │
│ • regenerate()      │ 5. Create session
│ • store user data   │ 6. Store in session
│                     │
└──────┬──────────────┘
       │
       ▼ 7. Redirect to /home
┌─────────────────────┐
│ Dashboard           │
│                     │
│ • Check role        │ 8. Load user role
│ • Fetch vendor APIs │ 9. Get dashboard data
│ • Aggregate data    │    from multiple vendors
│                     │
└─────────────────────┘
```

### 3. **Vendor API Communication Flow**

```
┌─────────────────────────────────────────────────────┐
│ DashboardController / Custom Controller              │
└────────────────┬────────────────────────────────────┘
                 │
                 ▼ 1. Get ApiEndpoint
┌─────────────────────────────────────────────────────┐
│ ApiEndpoint::with(['vendorApi', 'vendorApi.vendor'])│
│   ->where('name', 'dashboard')                      │
│   ->where('status', 'active')                       │
│   ->get()                                           │
└────────────────┬────────────────────────────────────┘
                 │
                 ▼ 2. Loop through endpoints
┌─────────────────────────────────────────────────────┐
│ Check if requires_auth                              │
└────────────────┬────────────────────────────────────┘
                 │
                 ├─ YES ─▶ 3a. VendorApiAuthHelper::authenticate()
                 │           │
                 │           ├─ Check email & password exists
                 │           ├─ POST to /login endpoint
                 │           ├─ Get access_token
                 │           ├─ Save token to auth_credentials
                 │           └─ Return HTTP client with token
                 │
                 └─ NO ──▶ 3b. HTTP client tanpa auth
                           │
                           ▼ 4. Make API Request
┌─────────────────────────────────────────────────────┐
│ $client->get($baseUrl . $path)                      │
│   ->withOptions(['verify' => false])                │
└────────────────┬────────────────────────────────────┘
                 │
                 ├─ SUCCESS ─▶ 5a. Extract data
                 │                 │
                 │                 └─ Return JSON response
                 │
                 └─ FAILED ──▶ 5b. Log error
                                   │
                                   └─ Continue to next vendor
```

### 4. **API Request Logging Flow**

```
┌─────────────────────┐
│ Sebelum API Call    │
└──────┬──────────────┘
       │ 1. Create log entry
       ▼
┌─────────────────────┐
│ ApiRequest::create()│
│                     │
│ • request_id (UUID) │
│ • method            │
│ • url               │
│ • headers           │
│ • parameters        │
│ • request_body      │
│ • status: pending   │
│ • requested_at      │
└──────┬──────────────┘
       │
       ▼ 2. Execute API call
┌─────────────────────┐
│ HTTP Request        │
└──────┬──────────────┘
       │
       ▼ 3. Get response
┌─────────────────────┐
│ Update log entry    │
│                     │
│ • response_code     │
│ • response_headers  │
│ • response_body     │
│ • response_time     │
│ • status: success   │
│ • responded_at      │
└─────────────────────┘
```

---

## 🔧 Core Components & Helpers

### 1. **VendorApiAuthHelper**

Helper class untuk menangani autentikasi ke vendor APIs.

**File:** `app/Helpers/VendorApiAuthHelper.php`

**Method:**

```php
VendorApiAuthHelper::authenticate(VendorApi $vendorApi, string $baseUrl)
```

**Fungsi:**

-   Login ke vendor API menggunakan email & password
-   Extract access token dari response
-   Save token ke `auth_credentials` (encrypted)
-   Return HTTP client dengan Bearer token

**Contoh Penggunaan:**

```php
$authClient = VendorApiAuthHelper::authenticate($vendorApi, $baseUrl);
if ($authClient) {
    $response = $authClient->get('/users');
}
```

---

### 2. **JsonTemplateHelper**

Helper class untuk mengelola dan memformat JSON response menggunakan template.

**File:** `app/Helpers/JsonTemplateHelper.php`

**Methods:**

#### a. `getTemplate($name, $category = null)`

Mengambil template berdasarkan nama dan kategori dengan caching.

#### b. `generateResponse($templateName, $data, $variables, $category, $httpCode, $message)`

Generate formatted response menggunakan template.

**Contoh:**

```php
return JsonTemplateHelper::jsonResponse(
    'api_response',
    ['users' => $users],
    ['message' => 'Data retrieved successfully'],
    'api',
    200
);
```

**Output:**

```json
{
  "status": "success",
  "code": 200,
  "message": "Data retrieved successfully",
  "timestamp": "2025-11-06T10:30:00.000000Z",
  "data": {
    "users": [...]
  }
}
```

#### c. `getDefaultSuccessResponse($data, $message)`

Return default success response structure.

#### d. `getDefaultErrorResponse($message, $code, $errors)`

Return default error response structure.

---

## 🎨 Admin Panel (Filament)

### Filament Resources

Paramita menggunakan **Filament 3.3** untuk admin panel dengan resources berikut:

#### 1. **VendorResource**

-   Manage data vendors
-   CRUD operations
-   Filter by status
-   Export data

#### 2. **VendorApiResource**

-   Manage vendor APIs
-   Configure authentication
-   Set headers & timeout
-   Test API connection
-   Monitor health status

#### 3. **ApiEndpointResource**

-   Manage API endpoints
-   Define parameters
-   Test endpoints
-   Link to JSON templates
-   Health check monitoring

#### 4. **ApiRequestResource**

-   View API request logs
-   Filter by status, method, response code
-   Search by URL or request_id
-   View request/response details
-   Export logs

#### 5. **JsonTemplateResource**

-   Create & manage JSON templates
-   Template versioning
-   Category management
-   Variable support
-   Preview template

#### 6. **ApiConfigurationResource**

-   Manage API configurations (Key-Value)
-   Encrypt sensitive data
-   Type casting (string, int, bool, json)

#### 7. **UserResource**

-   User management
-   Assign roles & permissions
-   Link to unit kerja

#### 8. **UnitKerjaResource**

-   Manage organizational units
-   Link to users

#### 9. **RoleResource & PermissionResource**

-   Role & permission management
-   Assign permissions to roles
-   RBAC configuration

---

## 🔐 Security Features

### 1. **Authentication**

-   Laravel built-in authentication
-   Session-based auth
-   Password hashing (bcrypt)
-   Remember me functionality
-   CSRF protection

### 2. **Authorization**

-   **Spatie Laravel Permission**
-   Role-Based Access Control (RBAC)
-   Permission-based access
-   Middleware protection
-   Filament panel access control

**Contoh:**

```php
// Check role
if ($user->hasRole('admin')) { }

// Check permission
if ($user->can('view_vendors')) { }

// Filament access
public function canAccessPanel(Panel $panel): bool
{
    return $this->hasRole('admin');
}
```

### 3. **Data Encryption**

-   `auth_credentials` di-encrypt (Laravel Crypt)
-   Sensitive configurations encrypted
-   SSL/TLS untuk external API calls

### 4. **API Security**

-   Multiple auth types support (API Key, Bearer Token, OAuth2)
-   Rate limiting
-   Timeout protection
-   SSL verification optional

---

## 🚀 Cara Menghubungkan Sistem Lain

### Langkah-langkah Setup:

#### **Step 1: Register Vendor**

1. Login ke Paramita admin panel
2. Navigate to **Vendors**
3. Click **Create New Vendor**
4. Fill vendor information:
    - Name, Code, Company Name
    - Contact Person, Email, Phone
    - Address details
    - Set status to **Active**

#### **Step 2: Configure Vendor API**

1. Navigate to **Vendor APIs**
2. Click **Create New Vendor API**
3. Configure:
    - Select Vendor
    - API Name
    - Base URL (e.g., `https://vendor-api.com/api/v1`)
    - Version (e.g., `v1`)
    - Auth Type (select dari: none, api_key, bearer_token, basic_auth, oauth2)
    - Auth Credentials (API key atau token)
    - Email & Password (untuk auto-login)
    - Additional Headers (jika diperlukan)
    - Timeout & Rate Limit
    - Set status to **Active**

**Contoh Konfigurasi:**

```json
{
    "base_url": "https://vendor-api.com/api/v1",
    "auth_type": "bearer_token",
    "auth_credentials": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "headers": {
        "Accept": "application/json",
        "X-API-Version": "v1"
    },
    "timeout": 30,
    "rate_limit": 1000
}
```

#### **Step 3: Define API Endpoints**

1. Navigate to **API Endpoints**
2. Click **Create New Endpoint**
3. Configure:
    - Select Vendor API
    - Endpoint Name (e.g., "Get Users", "Dashboard")
    - Path (e.g., `/users`, `/dashboard`)
    - HTTP Method (GET, POST, PUT, PATCH, DELETE)
    - Description
    - Parameters (define required params)
    - Requires Auth (yes/no)
    - Status (active/inactive/deprecated)
    - Link to JSON Template (optional)

**Contoh Endpoint:**

```json
{
    "name": "Get Users",
    "path": "/users",
    "method": "GET",
    "parameters": {
        "page": "Page number",
        "limit": "Items per page"
    },
    "requires_auth": true,
    "status": "active"
}
```

#### **Step 4: Test Connection**

1. Di halaman API Endpoint detail
2. Click **Test Endpoint** button
3. System akan:
    - Authenticate (jika required)
    - Call endpoint
    - Return response
    - Update health status
    - Log request/response

#### **Step 5: Use Paramita as Proxy**

Gunakan Paramita sebagai proxy untuk semua API calls:

**Contoh Implementation dalam Controller:**

```php
// Get endpoint configuration
$endpoint = ApiEndpoint::where('name', 'dashboard')
    ->where('status', 'active')
    ->first();

// Get base URL from vendor API
$baseUrl = $endpoint->vendorApi->base_url;

// Create HTTP client
$client = Http::withHeaders(['Accept' => 'application/json'])
    ->withOptions(['verify' => false]);

// Authenticate if required
if ($endpoint->requires_auth) {
    $authClient = VendorApiAuthHelper::authenticate(
        $endpoint->vendorApi,
        $baseUrl
    );
    if ($authClient) {
        $client = $authClient;
    }
}

// Make API call
$response = $client->get($baseUrl . $endpoint->path);

// Process response
if ($response->successful()) {
    $data = $response->json('data');

    // Log request (optional)
    ApiRequest::create([
        'vendor_api_id' => $endpoint->vendor_api_id,
        'api_endpoint_id' => $endpoint->id,
        'method' => 'GET',
        'url' => $baseUrl . $endpoint->path,
        'response_code' => $response->status(),
        'response_body' => $response->body(),
        'response_time' => $response->handlerStats()['total_time'] ?? 0,
        'status' => 'success',
    ]);

    return $data;
}
```

---

## 📋 Contoh Data Vendor (Berdasarkan Gambar)

### Struktur Data yang Diterima dari Vendor:

```json
{
    "base_url": "https://vendor-api.com/api/v1",
    "auth_type": "bearer|api_key|basic",
    "auth_credentials": "token or key",
    "endpoints": [
        {
            "path": "/users",
            "method": "GET",
            "requires_auth": true,
            "parameters": ["page", "limit"],
            "response_format": "json"
        }
    ]
}
```

### Response Format dari Vendor:

**Success Response:**

```json
{
    "status": "success",
    "code": 200,
    "message": "Data retrieved successfully",
    "data": {
        "users": [
            {
                "id": 1,
                "name": "John Doe",
                "email": "john@example.com"
            }
        ],
        "pagination": {
            "page": 1,
            "limit": 10,
            "total": 100
        }
    }
}
```

**Error Response:**

```json
{
    "status": "error",
    "code": 401,
    "message": "Unauthorized access",
    "errors": {
        "token": ["Invalid or expired token"]
    }
}
```

---

## 📊 Monitoring & Logging

### 1. **API Request Logging**

Semua API request/response dicatat ke database:

-   Request ID (UUID)
-   Method, URL, Headers, Body
-   Response Code, Headers, Body
-   Response Time (ms)
-   Status (pending, success, failed, timeout)
-   Error messages

### 2. **Health Check**

System melakukan health check pada endpoints:

-   Status: unknown, healthy, unhealthy, warning
-   Last tested timestamp
-   Health message
-   Auto-update setelah test

### 3. **Dashboard Monitoring**

Dashboard menampilkan:

-   Vendor API status
-   Endpoint health
-   Request statistics
-   Error rates
-   Response time metrics

---

## 🔄 API Response Standardization

### JSON Template System

Paramita menggunakan **JSON Template** untuk standardisasi response format:

**Template Variables:**

-   `{{status}}` - success/error
-   `{{code}}` - HTTP status code
-   `{{message}}` - Response message
-   `{{timestamp}}` - ISO 8601 timestamp
-   Custom variables

**Contoh Template:**

```json
{
  "status": "{{status}}",
  "code": {{code}},
  "message": "{{message}}",
  "timestamp": "{{timestamp}}",
  "data": {},
  "meta": {
    "version": "1.0",
    "api_name": "Paramita Gateway"
  }
}
```

**Penggunaan:**

```php
return JsonTemplateHelper::jsonResponse(
    'standard_response',  // template name
    ['users' => $users],  // data
    [                      // variables
        'message' => 'Users retrieved successfully'
    ],
    'api',                // category
    200                   // HTTP code
);
```

---

## 🎯 Use Cases

### 1. **Centralized API Management**

-   Manage multiple vendor APIs from one place
-   Single source of truth for API configurations
-   Easy switching between vendors

### 2. **API Monitoring & Analytics**

-   Track all API calls
-   Monitor response times
-   Identify failing endpoints
-   Generate usage reports

### 3. **Authentication Proxy**

-   Handle authentication for multiple vendors
-   Auto-refresh tokens
-   Secure credential storage

### 4. **Response Standardization**

-   Normalize different vendor response formats
-   Consistent API interface for frontend
-   Easy to maintain and update

### 5. **Multi-Vendor Dashboard**

-   Aggregate data from multiple sources
-   Single dashboard for all vendors
-   Role-based data access

---

## 🔧 Configuration & Environment

### Required Environment Variables:

```env
APP_NAME=Paramita
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://paramita.example.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=paramita
DB_USERNAME=root
DB_PASSWORD=

# Filament
FILAMENT_FILESYSTEM_DISK=public

# Cache
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

---

## 📈 Scalability & Performance

### 1. **Caching Strategy**

-   Template caching (3600s)
-   Response caching (optional)
-   Database query caching

### 2. **Queue System**

-   Asynchronous API calls (optional)
-   Background health checks
-   Report generation

### 3. **Rate Limiting**

-   Per-vendor rate limits
-   Configurable timeout
-   Automatic retry logic

### 4. **Database Optimization**

-   Indexed columns
-   Relationship eager loading
-   Pagination for large datasets

---

## 🛡️ Error Handling

### 1. **API Call Errors**

-   Connection timeout
-   Authentication failure
-   Rate limit exceeded
-   Invalid response format

**Handling:**

```php
try {
    $response = $client->get($url);
    if ($response->successful()) {
        // Process response
    } else {
        // Log error
        Log::error('API call failed', [
            'url' => $url,
            'status' => $response->status(),
            'body' => $response->body()
        ]);
    }
} catch (\Exception $e) {
    Log::error('API exception', [
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
```

### 2. **Validation Errors**

-   Invalid configuration
-   Missing credentials
-   Invalid endpoint path

### 3. **Database Errors**

-   Connection failures
-   Query errors
-   Constraint violations

---

## 📝 Best Practices

### 1. **Vendor Integration**

-   ✅ Test connection sebelum production
-   ✅ Set appropriate timeout values
-   ✅ Configure rate limits
-   ✅ Monitor health status regularly
-   ✅ Keep credentials encrypted

### 2. **Development**

-   ✅ Use environment variables for sensitive data
-   ✅ Log all API communications
-   ✅ Implement proper error handling
-   ✅ Use JSON templates for consistency
-   ✅ Follow Laravel best practices

### 3. **Security**

-   ✅ Regularly rotate API credentials
-   ✅ Use HTTPS for all external calls
-   ✅ Implement RBAC properly
-   ✅ Audit API access logs
-   ✅ Keep packages updated

### 4. **Monitoring**

-   ✅ Set up alerts for failing endpoints
-   ✅ Monitor response times
-   ✅ Track error rates
-   ✅ Review logs regularly
-   ✅ Generate periodic reports

---

## 🚀 Deployment

### Production Checklist:

-   [ ] Configure production database
-   [ ] Set `APP_ENV=production`
-   [ ] Set `APP_DEBUG=false`
-   [ ] Generate `APP_KEY`
-   [ ] Configure mail settings
-   [ ] Set up SSL certificates
-   [ ] Configure web server (Apache/Nginx)
-   [ ] Run migrations
-   [ ] Seed initial data
-   [ ] Set proper file permissions
-   [ ] Configure backups
-   [ ] Set up monitoring
-   [ ] Configure logging
-   [ ] Test all API connections

---

## 📞 Support & Maintenance

### Regular Tasks:

**Daily:**

-   Monitor API health status
-   Check error logs
-   Review failed requests

**Weekly:**

-   Generate usage reports
-   Review response times
-   Update vendor configurations

**Monthly:**

-   Clean old logs (optional)
-   Update dependencies
-   Security audit
-   Performance optimization

---

## 🎓 Kesimpulan

**Paramita** adalah sistem yang powerful dan fleksibel untuk:

-   ✅ Mengelola multiple vendor APIs
-   ✅ Monitoring dan logging API communications
-   ✅ Standardisasi response format
-   ✅ Authentication & authorization management
-   ✅ Real-time health monitoring

Dengan arsitektur yang modular dan maintainable, Paramita dapat di-scale dan di-extend sesuai kebutuhan bisnis.

---

**Dokumentasi ini dibuat pada:** November 6, 2025
**Versi Sistem:** Laravel 12.x + Filament 3.3
**Status:** Production Ready
