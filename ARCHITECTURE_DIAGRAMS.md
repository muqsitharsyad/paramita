# 📐 ARCHITECTURE DIAGRAMS - PARAMITA SYSTEM

## 🎨 System Architecture Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                    PARAMITA API GATEWAY SYSTEM                   │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                        PRESENTATION LAYER                        │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌──────────────────┐         ┌──────────────────┐             │
│  │  Admin Panel     │         │  Web Dashboard   │             │
│  │  (Filament 3.3)  │         │  (Blade Views)   │             │
│  │                  │         │                  │             │
│  │  • Vendor CRUD   │         │  • Analytics     │             │
│  │  • API Config    │         │  • Monitoring    │             │
│  │  • Logging       │         │  • Reports       │             │
│  │  • User Mgmt     │         │  • Health Status │             │
│  └──────────────────┘         └──────────────────┘             │
│                                                                  │
└────────────────────────┬─────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│                       APPLICATION LAYER                          │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │                    Controllers                             │  │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐   │  │
│  │  │ Auth         │  │ Dashboard    │  │ API Proxy    │   │  │
│  │  │ Controller   │  │ Controller   │  │ Controller   │   │  │
│  │  └──────────────┘  └──────────────┘  └──────────────┘   │  │
│  └──────────────────────────────────────────────────────────┘  │
│                                                                  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │                    Middleware                              │  │
│  │  • Authentication    • Authorization    • CSRF            │  │
│  │  • Rate Limiting     • Logging          • CORS            │  │
│  └──────────────────────────────────────────────────────────┘  │
│                                                                  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │                    Helpers                                 │  │
│  │  ┌────────────────────┐    ┌────────────────────┐        │  │
│  │  │ VendorApiAuth      │    │ JsonTemplate       │        │  │
│  │  │ Helper             │    │ Helper             │        │  │
│  │  │                    │    │                    │        │  │
│  │  │ • authenticate()   │    │ • generateResponse │        │  │
│  │  │ • refreshToken()   │    │ • formatTemplate   │        │  │
│  │  └────────────────────┘    └────────────────────┘        │  │
│  └──────────────────────────────────────────────────────────┘  │
│                                                                  │
└────────────────────────┬─────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│                       BUSINESS LOGIC LAYER                       │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │                    Models                                  │  │
│  │                                                            │  │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐ │  │
│  │  │ Vendor   │  │VendorApi │  │Endpoint  │  │ Request  │ │  │
│  │  └──────────┘  └──────────┘  └──────────┘  └──────────┘ │  │
│  │                                                            │  │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐ │  │
│  │  │  User    │  │   Role   │  │Permission│  │UnitKerja │ │  │
│  │  └──────────┘  └──────────┘  └──────────┘  └──────────┘ │  │
│  │                                                            │  │
│  │  ┌──────────┐  ┌──────────┐                              │  │
│  │  │ Template │  │  Config  │                              │  │
│  │  └──────────┘  └──────────┘                              │  │
│  └──────────────────────────────────────────────────────────┘  │
│                                                                  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │                    Services                                │  │
│  │  • Vendor API Service    • Logging Service                │  │
│  │  • Auth Service          • Health Check Service           │  │
│  │  • Template Service      • Report Service                 │  │
│  └──────────────────────────────────────────────────────────┘  │
│                                                                  │
└────────────────────────┬─────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│                        DATA ACCESS LAYER                         │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │                    Database (MySQL)                        │  │
│  │                                                            │  │
│  │  • vendors           • vendor_apis      • api_endpoints   │  │
│  │  • api_requests      • api_configs      • json_templates  │  │
│  │  • users             • roles            • permissions     │  │
│  │  • unit_kerjas       • model_has_roles  • ...             │  │
│  └──────────────────────────────────────────────────────────┘  │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                        EXTERNAL LAYER                            │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐         │
│  │ Vendor API 1 │  │ Vendor API 2 │  │ Vendor API N │         │
│  │              │  │              │  │              │         │
│  │ (External)   │  │ (External)   │  │ (External)   │         │
│  └──────────────┘  └──────────────┘  └──────────────┘         │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🔄 Detailed Request Flow Diagram

```
┌────────────┐
│   Client   │
│  Browser   │
└──────┬─────┘
       │ 1. HTTP GET /home
       │
       ▼
┌─────────────────────────────────────────────────────┐
│              Laravel Application                     │
│                                                      │
│  ┌───────────────────────────────────────────────┐  │
│  │        Routing (routes/web.php)               │  │
│  │  Route::middleware(['auth'])->group(...)      │  │
│  └─────────────────┬─────────────────────────────┘  │
│                    │ 2. Check authentication         │
│                    ▼                                 │
│  ┌───────────────────────────────────────────────┐  │
│  │     Authentication Middleware                  │  │
│  │  • Check session                               │  │
│  │  • Verify user logged in                       │  │
│  └─────────────────┬─────────────────────────────┘  │
│                    │ 3. Authenticated ✓              │
│                    ▼                                 │
│  ┌───────────────────────────────────────────────┐  │
│  │     Authorization Middleware                   │  │
│  │  • Check user role                             │  │
│  │  • Verify permissions                          │  │
│  └─────────────────┬─────────────────────────────┘  │
│                    │ 4. Authorized ✓                 │
│                    ▼                                 │
│  ┌───────────────────────────────────────────────┐  │
│  │     DashboardController@index                  │  │
│  │                                                │  │
│  │  $user = Auth::user();                         │  │
│  │  $role = $user->roles->first()->name;          │  │
│  └─────────────────┬─────────────────────────────┘  │
│                    │ 5. Get user info                │
│                    ▼                                 │
│  ┌───────────────────────────────────────────────┐  │
│  │  Query: ApiEndpoint::with(['vendorApi'])      │  │
│  │         ->where('name', 'dashboard')           │  │
│  │         ->where('status', 'active')            │  │
│  │         ->get()                                │  │
│  └─────────────────┬─────────────────────────────┘  │
│                    │ 6. Get active dashboard endpoints│
│                    ▼                                 │
│  ┌───────────────────────────────────────────────┐  │
│  │  Loop through each ApiEndpoint                │  │
│  │                                                │  │
│  │  foreach ($vendorApis as $api) {               │  │
│  └─────────────────┬─────────────────────────────┘  │
│                    │                                 │
│                    ▼                                 │
│  ┌───────────────────────────────────────────────┐  │
│  │  Check: $api->requires_auth                    │  │
│  └─────────────────┬─────────────────────────────┘  │
│                    │                                 │
│         ┌──────────┴──────────┐                     │
│         │                     │                     │
│    YES  ▼                     ▼  NO                 │
│  ┌──────────────┐      ┌──────────────┐            │
│  │ Authenticate │      │  Skip Auth   │            │
│  └──────┬───────┘      └──────┬───────┘            │
│         │ 7a. VendorApiAuthHelper    │             │
│         │     ::authenticate()       │             │
│         │                            │             │
│         ▼                            │             │
│  ┌──────────────────────────────┐   │             │
│  │  POST /login to Vendor API   │   │             │
│  │  • email + password          │   │             │
│  │  • Get access_token          │   │             │
│  │  • Save to auth_credentials  │   │             │
│  └──────┬───────────────────────┘   │             │
│         │ 8. Token received          │             │
│         └────────────┬───────────────┘             │
│                      │                             │
│                      ▼ 9. Make API Request          │
│  ┌───────────────────────────────────────────────┐  │
│  │  HTTP GET: $baseUrl . $api->path              │  │
│  │                                                │  │
│  │  $response = $client->get($url)                │  │
│  └─────────────────┬─────────────────────────────┘  │
│                    │ 10. Get Response                │
│                    ▼                                 │
│  ┌───────────────────────────────────────────────┐  │
│  │  if ($response->successful()) {                │  │
│  │      $data = $response->json('data');          │  │
│  │      $vendorDashboardData[$api->api_name]      │  │
│  │          = $data;                              │  │
│  │  } else {                                      │  │
│  │      Log::error(...);                          │  │
│  │      continue;                                 │  │
│  │  }                                             │  │
│  └─────────────────┬─────────────────────────────┘  │
│                    │ 11. Store data                  │
│                    ▼                                 │
│  ┌───────────────────────────────────────────────┐  │
│  │  Optional: Log to api_requests table          │  │
│  │                                                │  │
│  │  ApiRequest::create([                          │  │
│  │      'vendor_api_id' => ...,                   │  │
│  │      'api_endpoint_id' => ...,                 │  │
│  │      'method' => 'GET',                        │  │
│  │      'url' => $url,                            │  │
│  │      'response_code' => $response->status(),   │  │
│  │      'response_body' => $response->body(),     │  │
│  │      'status' => 'success',                    │  │
│  │  ]);                                           │  │
│  └─────────────────┬─────────────────────────────┘  │
│                    │                                 │
│                    │ 12. End of loop                 │
│                    ▼                                 │
│  ┌───────────────────────────────────────────────┐  │
│  │  return view('dashboard', [                    │  │
│  │      'dashboardData' => $vendorDashboardData,  │  │
│  │      'role' => $role                           │  │
│  │  ]);                                           │  │
│  └─────────────────┬─────────────────────────────┘  │
│                    │ 13. Render view                 │
└────────────────────┼─────────────────────────────────┘
                     │
                     ▼ 14. HTML Response
┌────────────┐
│   Client   │
│  Browser   │
│            │
│  Display   │
│  Dashboard │
└────────────┘
```

---

## 🔐 Authentication & Authorization Flow

```
┌──────────────────────────────────────────────────────────────────┐
│                    AUTHENTICATION FLOW                            │
└──────────────────────────────────────────────────────────────────┘

1. Login Page
┌────────────┐
│   User     │  Navigate to /login
│            ├────────────────────────┐
└────────────┘                        │
                                      ▼
                          ┌────────────────────┐
                          │  AuthController    │
                          │  @showLoginForm    │
                          │                    │
                          │  return view(      │
                          │   'auth.login'     │
                          │  );                │
                          └────────┬───────────┘
                                   │
                                   ▼ Display login form
                          ┌────────────────────┐
                          │   Login Form       │
                          │                    │
                          │  • Email input     │
                          │  • Password input  │
                          │  • CSRF token      │
                          └────────┬───────────┘
                                   │

2. Submit Login
┌────────────┐
│   User     │  Submit email + password
│            ├────────────────────────┐
└────────────┘                        │
                                      ▼
                          ┌────────────────────────────┐
                          │  AuthController@login      │
                          │                            │
                          │  1. Validate input         │
                          │     • email (required)     │
                          │     • password (required)  │
                          └────────┬───────────────────┘
                                   │
                                   ▼
                          ┌────────────────────────────┐
                          │  Auth::attempt()           │
                          │  ['email' => $email,       │
                          │   'password' => $password] │
                          └────────┬───────────────────┘
                                   │
                        ┌──────────┴──────────┐
                        │                     │
                   SUCCESS                 FAILED
                        │                     │
                        ▼                     ▼
        ┌───────────────────────┐  ┌──────────────────────┐
        │ Session Created       │  │ Return Error         │
        │                       │  │                      │
        │ • Store user data     │  │ back()->withErrors() │
        │ • Generate session ID │  │                      │
        │ • Set cookies         │  └──────────────────────┘
        └───────┬───────────────┘
                │
                ▼
        ┌───────────────────────┐
        │ Check User Role       │
        │                       │
        │ $user->roles->first() │
        └───────┬───────────────┘
                │
                ▼
        ┌───────────────────────┐
        │ Redirect to /home     │
        │                       │
        │ redirect()->intended()│
        └───────────────────────┘


┌──────────────────────────────────────────────────────────────────┐
│                    AUTHORIZATION FLOW                             │
└──────────────────────────────────────────────────────────────────┘

┌────────────┐
│   User     │  Request protected resource
│            ├────────────────────────┐
└────────────┘                        │
                                      ▼
                          ┌────────────────────────────┐
                          │  Middleware: auth          │
                          │                            │
                          │  Check if authenticated    │
                          └────────┬───────────────────┘
                                   │
                        ┌──────────┴──────────┐
                        │                     │
                   AUTHENTICATED          NOT AUTHENTICATED
                        │                     │
                        ▼                     ▼
        ┌───────────────────────┐  ┌──────────────────────┐
        │ Continue              │  │ Redirect to /login   │
        └───────┬───────────────┘  └──────────────────────┘
                │
                ▼
        ┌───────────────────────────────┐
        │ Middleware: role/permission   │
        │                               │
        │ Check user has role:          │
        │   • $user->hasRole('admin')   │
        │                               │
        │ Check user has permission:    │
        │   • $user->can('view_vendors')│
        └───────┬───────────────────────┘
                │
        ┌───────┴──────────┐
        │                  │
    AUTHORIZED        NOT AUTHORIZED
        │                  │
        ▼                  ▼
┌──────────────┐  ┌────────────────┐
│ Allow Access │  │ 403 Forbidden  │
└──────────────┘  └────────────────┘


┌──────────────────────────────────────────────────────────────────┐
│              FILAMENT ADMIN PANEL ACCESS                          │
└──────────────────────────────────────────────────────────────────┘

┌────────────┐
│   User     │  Navigate to /admin
│            ├────────────────────────┐
└────────────┘                        │
                                      ▼
                          ┌────────────────────────────┐
                          │  Filament Auth Check       │
                          │                            │
                          │  User::canAccessPanel()    │
                          └────────┬───────────────────┘
                                   │
                        ┌──────────┴──────────┐
                        │                     │
                  HAS ADMIN ROLE        NO ADMIN ROLE
                        │                     │
                        ▼                     ▼
        ┌───────────────────────┐  ┌──────────────────────┐
        │ Show Admin Panel      │  │ 403 Access Denied    │
        │                       │  │                      │
        │ • Dashboard           │  └──────────────────────┘
        │ • Vendors             │
        │ • APIs                │
        │ • Logs                │
        │ • Users               │
        │ • Roles               │
        └───────────────────────┘
```

---

## 🗄️ Database Relationship Diagram

```
┌─────────────────────────────────────────────────────────────────────┐
│                    DATABASE ENTITY RELATIONSHIPS                     │
└─────────────────────────────────────────────────────────────────────┘

┌──────────────────┐
│     vendors      │
├──────────────────┤
│ • id (PK)        │
│ • name           │
│ • code (UNIQUE)  │
│ • company_name   │
│ • email          │
│ • phone          │
│ • status         │
│ • timestamps     │
└────────┬─────────┘
         │
         │ 1:N (One-to-Many)
         ▼
┌──────────────────────────┐
│     vendor_apis          │
├──────────────────────────┤
│ • id (PK)                │
│ • vendor_id (FK) ────────┘
│ • api_name
│ • base_url
│ • version
│ • auth_type
│ • auth_credentials (ENC)
│ • email
│ • password
│ • status
│ • is_healthy
│ • timestamps
└────────┬─────────────────┘
         │
         ├──────────────────────────────────┐
         │                                  │
         │ 1:N                              │ 1:N
         ▼                                  ▼
┌──────────────────────┐       ┌──────────────────────────┐
│  api_endpoints       │       │  api_configurations      │
├──────────────────────┤       ├──────────────────────────┤
│ • id (PK)            │       │ • id (PK)                │
│ • vendor_api_id (FK) │       │ • vendor_api_id (FK)     │
│ • name               │       │ • config_key             │
│ • path               │       │ • config_value           │
│ • method             │       │ • data_type              │
│ • requires_auth      │       │ • is_sensitive           │
│ • status             │       │ • timestamps             │
│ • health_status      │       └──────────────────────────┘
│ • json_template_id   │
│ • timestamps         │
└────────┬─────────────┘
         │
         │ 1:N
         ▼
┌──────────────────────┐
│   api_requests       │
├──────────────────────┤
│ • id (PK)            │
│ • vendor_api_id (FK) │
│ • api_endpoint_id    │
│ • request_id (UUID)  │
│ • method             │
│ • url                │
│ • headers (JSON)     │
│ • request_body       │
│ • response_code      │
│ • response_body      │
│ • response_time      │
│ • status             │
│ • timestamps         │
└──────────────────────┘


┌──────────────────┐
│ json_templates   │
├──────────────────┤
│ • id (PK)        │
│ • name           │
│ • category       │
│ • template_data  │
│ • version        │
│ • is_active      │
│ • created_by (FK)├─────────┐
│ • updated_by (FK)├─────┐   │
│ • timestamps     │     │   │
│ • deleted_at     │     │   │
└──────────────────┘     │   │
                         │   │
      ┌──────────────────┘   │
      │                      │
      │ N:1                  │ N:1
      ▼                      ▼
┌──────────────────┐   ┌──────────────────┐
│     users        │   │  unit_kerjas     │
├──────────────────┤   ├──────────────────┤
│ • id (PK)        │   │ • id (PK)        │
│ • name           │   │ • nama           │
│ • email          │   │ • kode           │
│ • nip            │   │ • keterangan     │
│ • unit_kerja_id  │───┘ • timestamps     │
│ • password       │   └──────────────────┘
│ • status         │
│ • timestamps     │
└────────┬─────────┘
         │
         │ N:M (Many-to-Many)
         ▼
┌──────────────────┐        ┌──────────────────┐
│ model_has_roles  │        │     roles        │
├──────────────────┤        ├──────────────────┤
│ • role_id (FK)   │◄───────┤ • id (PK)        │
│ • model_id (FK)  │        │ • name           │
│ • model_type     │        │ • guard_name     │
└──────────────────┘        │ • timestamps     │
                            └────────┬─────────┘
         │                           │
         │ N:M                       │ N:M
         ▼                           ▼
┌──────────────────────────┐  ┌──────────────────────────┐
│ model_has_permissions    │  │ role_has_permissions     │
├──────────────────────────┤  ├──────────────────────────┤
│ • permission_id (FK) ────┼──┤ • permission_id (FK)     │
│ • model_id (FK)          │  │ • role_id (FK)           │
│ • model_type             │  └──────────────────────────┘
└──────────────────────────┘
         │
         │ N:M
         ▼
┌──────────────────┐
│   permissions    │
├──────────────────┤
│ • id (PK)        │
│ • name           │
│ • guard_name     │
│ • timestamps     │
└──────────────────┘


LEGEND:
─────────
PK  = Primary Key
FK  = Foreign Key
1:N = One-to-Many Relationship
N:M = Many-to-Many Relationship
ENC = Encrypted Field
```

---

## 📊 Data Flow Diagram

```
┌──────────────────────────────────────────────────────────────────┐
│                   API REQUEST LOGGING FLOW                        │
└──────────────────────────────────────────────────────────────────┘

START
  │
  ▼
┌─────────────────────────┐
│ Get ApiEndpoint Config  │
│                         │
│ SELECT * FROM           │
│   api_endpoints         │
│ WHERE name = '...'      │
│   AND status = 'active' │
└────────┬────────────────┘
         │
         ▼
┌─────────────────────────┐
│ Get VendorApi Config    │
│                         │
│ Eager load:             │
│   • vendor              │
│   • auth_credentials    │
│   • headers             │
└────────┬────────────────┘
         │
         ▼
┌─────────────────────────────┐
│ Create Log Entry (PENDING)  │
│                             │
│ INSERT INTO api_requests:   │
│   • request_id = UUID       │
│   • method = 'GET'          │
│   • url = $fullUrl          │
│   • status = 'pending'      │
│   • requested_at = NOW()    │
└────────┬────────────────────┘
         │ (get $logId)
         ▼
┌─────────────────────────┐
│ Check requires_auth     │
└────────┬────────────────┘
         │
    ┌────┴─────┐
    │          │
   YES        NO
    │          │
    ▼          │
┌─────────────────────────┐
│ VendorApiAuthHelper     │
│ ::authenticate()        │
│                         │
│ 1. Check credentials    │
│ 2. POST /login          │
│ 3. Get token            │
│ 4. Update auth_creds    │
│ 5. Return HTTP client   │
└────────┬────────────────┘
         │
         └──────────┬──────────────┘
                    │
                    ▼
┌──────────────────────────────────┐
│ Execute API Call                 │
│                                  │
│ $startTime = microtime(true);    │
│ $response = $client->get($url);  │
│ $endTime = microtime(true);      │
│ $responseTime = ($endTime -      │
│                 $startTime) * 1000│
└────────┬─────────────────────────┘
         │
         ▼
┌─────────────────────────┐
│ Check Response Status   │
└────────┬────────────────┘
         │
    ┌────┴─────┐
    │          │
SUCCESS      FAILED
    │          │
    ▼          ▼
┌──────────┐ ┌───────────┐
│ status   │ │ status    │
│ =success │ │ =failed   │
└────┬─────┘ └─────┬─────┘
     │             │
     └──────┬──────┘
            │
            ▼
┌───────────────────────────────┐
│ Update Log Entry              │
│                               │
│ UPDATE api_requests           │
│ SET:                          │
│   • response_code = $code     │
│   • response_headers = $hdrs  │
│   • response_body = $body     │
│   • response_time = $time     │
│   • status = $status          │
│   • error_message = $error    │
│   • responded_at = NOW()      │
│ WHERE id = $logId             │
└────────┬──────────────────────┘
         │
         ▼
┌─────────────────────────┐
│ Optional: Update        │
│ Endpoint Health Status  │
│                         │
│ UPDATE api_endpoints    │
│ SET:                    │
│   • health_status       │
│   • last_tested_at      │
│   • health_message      │
└────────┬────────────────┘
         │
         ▼
       END
```

---

## 🎯 Use Case Diagram

```
┌──────────────────────────────────────────────────────────────────┐
│                      USE CASE DIAGRAM                             │
└──────────────────────────────────────────────────────────────────┘

           ┌─────────────────────────────────────┐
           │      PARAMITA SYSTEM                │
           │                                     │
┌────────┐ │  ┌───────────────────────────────┐ │
│        │ │  │  Vendor Management            │ │
│  Admin │─┼─▶│  • Register Vendor            │ │
│        │ │  │  • Update Vendor Info         │ │
│  Role  │ │  │  • View Vendor List           │ │
│        │ │  │  • Suspend/Activate Vendor    │ │
└────────┘ │  └───────────────────────────────┘ │
           │                                     │
           │  ┌───────────────────────────────┐ │
           │  │  API Configuration            │ │
           │  │  • Add Vendor API             │ │
           │  │  • Configure Auth             │ │
           │  │  • Set Headers & Timeout      │ │
           │  │  • Test Connection            │ │
           │  └───────────────────────────────┘ │
           │                                     │
           │  ┌───────────────────────────────┐ │
           │  │  Endpoint Management          │ │
           │  │  • Define Endpoints           │ │
           │  │  • Set Parameters             │ │
           │  │  • Link JSON Template         │ │
           │  │  • Test Endpoint              │ │
           │  └───────────────────────────────┘ │
           │                                     │
           │  ┌───────────────────────────────┐ │
           │  │  User Management              │ │
           │  │  • Create Users               │ │
           │  │  • Assign Roles               │ │
           │  │  • Manage Permissions         │ │
           │  └───────────────────────────────┘ │
           │                                     │
           │  ┌───────────────────────────────┐ │
           │  │  Template Management          │ │
           │  │  • Create JSON Templates      │ │
           │  │  • Define Variables           │ │
           │  │  • Version Templates          │ │
           │  └───────────────────────────────┘ │
           └─────────────────────────────────────┘

┌────────┐   ┌─────────────────────────────────┐
│        │   │  PARAMITA SYSTEM                │
│  User  │   │                                 │
│        │───┼▶ ┌───────────────────────────┐ │
│  Role  │   │  │  View Dashboard           │ │
│        │   │  │  • Aggregated Data        │ │
└────────┘   │  │  • Multi-Vendor Stats     │ │
             │  │  • Health Status          │ │
             │  └───────────────────────────┘ │
             │                                 │
             │  ┌───────────────────────────┐ │
             │  │  API Monitoring           │ │
             │  │  • View Logs              │ │
             │  │  • Check Health           │ │
             │  │  • View Metrics           │ │
             │  └───────────────────────────┘ │
             └─────────────────────────────────┘

┌────────┐   ┌─────────────────────────────────┐
│        │   │  PARAMITA SYSTEM                │
│ System │   │                                 │
│        │───┼▶ ┌───────────────────────────┐ │
│ (Auto) │   │  │  Health Check             │ │
│        │   │  │  • Periodic Testing       │ │
└────────┘   │  │  • Update Status          │ │
             │  │  • Send Alerts            │ │
             │  └───────────────────────────┘ │
             │                                 │
             │  ┌───────────────────────────┐ │
             │  │  Request Logging          │ │
             │  │  • Log All Requests       │ │
             │  │  • Track Response Times   │ │
             │  │  • Store Responses        │ │
             │  └───────────────────────────┘ │
             │                                 │
             │  ┌───────────────────────────┐ │
             │  │  Token Management         │ │
             │  │  • Auto-login             │ │
             │  │  • Refresh Tokens         │ │
             │  │  • Store Securely         │ │
             │  └───────────────────────────┘ │
             └─────────────────────────────────┘

┌────────────┐ ┌─────────────────────────────────┐
│            │ │  PARAMITA SYSTEM                │
│  External  │ │                                 │
│  Vendor    │◀┼─ ┌───────────────────────────┐ │
│  APIs      │ │  │  API Proxy                │ │
│            │ │  │  • Forward Requests       │ │
└────────────┘ │  │  • Add Authentication     │ │
               │  │  • Log Communication      │ │
               │  │  • Format Response        │ │
               │  └───────────────────────────┘ │
               └─────────────────────────────────┘
```

---

## 🔄 State Diagram - API Request Status

```
┌──────────────────────────────────────────────────────────────────┐
│              API REQUEST STATUS STATE MACHINE                     │
└──────────────────────────────────────────────────────────────────┘

                        ┌──────────┐
                        │  START   │
                        └────┬─────┘
                             │
                             │ Create request log
                             ▼
                      ┌─────────────┐
                      │   PENDING   │
                      └──────┬──────┘
                             │
                             │ Execute API call
                             │
              ┌──────────────┼──────────────┐
              │              │              │
              ▼              ▼              ▼
      ┌──────────────┐ ┌──────────┐ ┌─────────────┐
      │   TIMEOUT    │ │ SUCCESS  │ │   FAILED    │
      │              │ │          │ │             │
      │ (>timeout)   │ │ (2xx)    │ │ (4xx, 5xx)  │
      └──────────────┘ └──────────┘ └─────────────┘
              │              │              │
              │              │              │
              └──────────────┼──────────────┘
                             │
                             ▼
                        ┌──────────┐
                        │   END    │
                        │          │
                        │ (Logged) │
                        └──────────┘
```

---

## 🏥 Health Status Flow

```
┌──────────────────────────────────────────────────────────────────┐
│            ENDPOINT HEALTH STATUS STATE DIAGRAM                   │
└──────────────────────────────────────────────────────────────────┘

                      ┌──────────────┐
                      │   UNKNOWN    │
                      │ (Initial)    │
                      └──────┬───────┘
                             │
                             │ First test
                             │
              ┌──────────────┼──────────────┐
              │              │              │
              ▼              ▼              ▼
      ┌──────────────┐ ┌──────────┐ ┌─────────────┐
      │  UNHEALTHY   │ │ HEALTHY  │ │   WARNING   │
      │              │ │          │ │             │
      │ (Failed)     │ │ (200 OK) │ │ (Slow/Warn) │
      └──────┬───────┘ └────┬─────┘ └──────┬──────┘
             │              │              │
             │              │              │
             │   Retest     │   Retest     │   Retest
             │◀─────────────┼──────────────┤
             │              │              │
             └──────────────┼──────────────┘
                            │
                            │ Periodic health check
                            │
                            ▼
                  Update last_tested_at
                  Update health_message
```

---

## 📈 Sequence Diagram - Full API Call

```
┌──────────────────────────────────────────────────────────────────────────┐
│              COMPLETE API CALL SEQUENCE DIAGRAM                           │
└──────────────────────────────────────────────────────────────────────────┘

User     Controller  ApiEndpoint  VendorApi  AuthHelper  HTTP     Vendor   Database
 │            │            │          │          │        │         │         │
 │  Request   │            │          │          │        │         │         │
 ├───────────▶│            │          │          │        │         │         │
 │            │            │          │          │        │         │         │
 │            │ Get Config │          │          │        │         │         │
 │            ├───────────▶│          │          │        │         │         │
 │            │◀───────────┤          │          │        │         │         │
 │            │            │          │          │        │         │         │
 │            │ Get API    │          │          │        │         │         │
 │            ├────────────┼─────────▶│          │        │         │         │
 │            │◀───────────┼──────────┤          │        │         │         │
 │            │            │          │          │        │         │         │
 │            │ Check Auth │          │          │        │         │         │
 │            ├────────────┼──────────┼─────────▶│        │         │         │
 │            │            │          │          │        │         │         │
 │            │            │          │          │ POST   │         │         │
 │            │            │          │          │ /login │         │         │
 │            │            │          │          ├───────▶│         │         │
 │            │            │          │          │◀───────┤         │         │
 │            │            │          │          │ token  │         │         │
 │            │            │          │          │        │         │         │
 │            │            │          │ Save     │        │         │         │
 │            │            │          │ Token    │        │         │         │
 │            │            │          ├─────────────────────────────▶│         │
 │            │            │          │          │        │         │         │
 │            │◀───────────┼──────────┼──────────┤        │         │         │
 │            │ Client     │          │          │        │         │         │
 │            │ w/ Token   │          │          │        │         │         │
 │            │            │          │          │        │         │         │
 │            │ Create Log │          │          │        │         │         │
 │            ├───────────────────────────────────────────────────────────────▶│
 │            │            │          │          │        │         │ INSERT  │
 │            │◀───────────────────────────────────────────────────────────────┤
 │            │ log_id     │          │          │        │         │         │
 │            │            │          │          │        │         │         │
 │            │ GET Request│          │          │        │         │         │
 │            ├────────────┼──────────┼──────────┼───────▶│         │         │
 │            │            │          │          │        │ GET /api│         │
 │            │            │          │          │        ├────────▶│         │
 │            │            │          │          │        │◀────────┤         │
 │            │            │          │          │        │ Response│         │
 │            │◀───────────┼──────────┼──────────┼────────┤         │         │
 │            │ Response   │          │          │        │         │         │
 │            │            │          │          │        │         │         │
 │            │ Update Log │          │          │        │         │         │
 │            ├───────────────────────────────────────────────────────────────▶│
 │            │            │          │          │        │         │ UPDATE  │
 │            │            │          │          │        │         │         │
 │            │ Format     │          │          │        │         │         │
 │            │ Response   │          │          │        │         │         │
 │            │ (Template) │          │          │        │         │         │
 │            │            │          │          │        │         │         │
 │◀───────────┤            │          │          │        │         │         │
 │ Response   │            │          │          │        │         │         │
 │            │            │          │          │        │         │         │
```

---

**Document Created:** November 6, 2025  
**System Version:** Laravel 12.x + Filament 3.3  
**Purpose:** Visual Architecture Documentation for Paramita System
