# AGENTS.md - Paramita Project Reference

This file is the working reference for architecture, optimization decisions, and current runtime behavior in the Paramita project.

---

## Project Overview

Paramita is a Laravel 12 + Filament 3.3 API Gateway Management System that:

- manages vendor APIs from a centralized admin panel
- logs and monitors API communication
- validates endpoint responses against JSON templates
- implements RBAC with Spatie Laravel Permission
- serves user-facing pages from database-driven Dynamic Pages

Current direction:

- admin users work through Filament
- non-admin users use Dynamic Pages at `/page/{slug}`
- sidebar navigation is database-driven
- legacy role-specific page stacks are removed

---

## Current Runtime Architecture

### Auth and Landing Flow

- `/login` handled by `AuthController`
- `/` redirects authenticated users to their configured home route
- `/home` mirrors the same redirect logic
- non-admin home routes are URL-based, not controller-based

### Dynamic Route Surface

Current user-facing route surface is intentionally small:

- `/page/{slug}` for authenticated dynamic pages
- Filament admin routes under `/admin/...`

There are no active legacy routes for:

- `manager/*`
- `operator/*`
- `viewer/*`

---

## Role System

Single source of truth: `config/roles.php`

Current roles:

| Role | Label | Home Route | Admin Panel |
|------|-------|------------|-------------|
| `admin` | Administrator | `filament.admin.pages.dashboard` | yes |
| `pimpinan-pusat` | Pimpinan Pusat | `/page/dashboard` | no |
| `pimpinan-daerah` | Pimpinan Daerah | `/page/dashboard` | no |
| `viewer` | Viewer | `/page/dashboard` | no |

Key helper: `app/Helpers/RoleHelper.php`

Methods currently in use:

- `getHomeRoute(string $roleName)`
- `getLabel(string $roleName)`
- `getPrimaryRoleName($user)`
- `getPanel(string $roleName)`
- `canAccessPanel(string $roleName, string $panelId)`
- `getUserHomeRoute($user)`

Notes:

- `home_route` may be a route name or a literal path beginning with `/`
- admin panel access is checked through `CheckAdminPanel`
- `RoleMiddleware` is removed and should not be reintroduced without a real route-level need

---

## Dynamic Pages

Dynamic Pages are now the primary user-facing application surface.

### Route

```php
Route::middleware(['auth'])->get('/page/{slug}', [DynamicPageController::class, 'show']);
```

### Current Behavior

Each page:

- has a unique slug
- is assigned to one or more roles
- may render one or more JSON templates
- fetches live data from endpoint-linked templates
- passes `limit`, `offset`, and `search` query parameters through to endpoint URLs when present
- renders pagination controls when upstream responses include `prev_offset` or `next_offset`
- supports simple table search on rendered array data

### Controller Optimizations

`app/Http/Controllers/DynamicPageController.php` currently:

- eager loads page roles and template endpoint relations
- uses endpoint `full_url` so query-string paths work consistently with endpoint testing
- uses `Http::pool()` for multi-template requests
- caches fetched template payloads for 30 seconds per endpoint/query-parameter combination
- degrades gracefully when one upstream request fails

---

## Sidebar Menu System

Sidebar menu items are stored in the database and managed from Filament.

Tables:

- `sidebar_menu_items`
- `sidebar_menu_item_role`

Important files:

- `app/Models/SidebarMenuItem.php`
- `app/Filament/Resources/SidebarMenuItemResource.php`
- `resources/views/partials/sidebar-nav.blade.php`
- `database/seeders/SidebarMenuSeeder.php`

Current behavior:

- menu tree is filtered by role and `is_active`
- child items are filtered correctly, not only roots
- active state works for both `route_name` and URL-based items
- default seeded menu points to Dynamic Pages only

Default seeded items:

| Label | URL | Roles |
|------|-----|-------|
| Dashboard | `/page/dashboard` | `pimpinan-pusat`, `pimpinan-daerah`, `viewer` |
| Monitoring Stock | `/page/monitoring-stock` | `pimpinan-pusat`, `pimpinan-daerah` |

---

## Filament Admin Panel

### Resource Query Strategy

All major Filament resources use explicit query shaping to avoid lazy loading and oversized payloads.

Current pattern:

- eager load only relations that table/infolist actually uses
- narrow eager-loaded columns where possible
- preload searchable relation selects where users expect initial options to be visible
- sort relation option queries explicitly

Examples:

| Resource | Current Query Strategy |
|----------|------------------------|
| `VendorResource` | no unused `withCount()` |
| `VendorApiResource` | eager loads `vendor:id,name` only |
| `ApiEndpointResource` | eager loads `vendorApi` and `jsonTemplate` with selected columns |
| `ApiRequestResource` | eager loads `vendorApi:id,api_name` and `apiEndpoint:id,name` |
| `ApiConfigurationResource` | eager loads `vendorApi:id,api_name` |
| `UserResource` | eager loads `unitKerja:id,nama` and `roles:id,name` |
| `PageResource` | eager loads `jsonTemplates:id,name` and `roles:id,name` |
| `SidebarMenuItemResource` | eager loads `roles:id,name` and `parent:id,label` |
| `RolePermissionResource` | eager loads `permissions` and counts `permissions`, `users` |

### Admin Cleanup Applied

Removed or simplified:

- unused widget `StatsOverview`
- misleading retry actions in `ApiRequestResource`
- no-op copy action in `ApiConfigurationResource`
- redundant `ViewAction` entries on resources with no dedicated view surface
- password display in `VendorApiResource` table/infolist

### Endpoint Test Refactor

`ApiEndpointResource` no longer owns all live test logic directly.

Testing is delegated to:

- `app/Services/ApiEndpointTestService.php`

Service responsibilities:

- build full endpoint URL
- authenticate vendor API client when needed
- execute request by method
- validate response structure against selected JSON template
- persist endpoint health result
- return structured result for Filament notifications

---

## Removed Legacy Components

These have already been removed from runtime and should be treated as retired:

### Controllers

- `DashboardController`
- `TemplateDataController`
- `ManagerController`
- `OperatorController`
- `ViewerController`

### Middleware

- `RoleMiddleware`

### Helpers

- `JsonTemplateHelper`

### Views and Layouts

- `resources/views/dashboard.blade.php`
- `resources/views/layouts/app.blade.php`
- all legacy view folders:
  - `resources/views/manager`
  - `resources/views/operator`
  - `resources/views/viewer`

### Static Reference Assets

- `public/Minimal_JavaScript_v4.1.0`

The application now assumes:

- no role-specific dashboard blade stacks
- no route-based non-admin navigation
- no JSON-template formatting helper layer outside the model/controller flow

---

## Database Seeding

Seeder order:

```text
RolePermissionSeeder -> JsonTemplateSeeder -> DatabaseDefaultSeeder -> SidebarMenuSeeder
```

What fresh seed produces:

- 4 roles
- 4 sample users
- JSON templates including `dashboard` and `monitoring-stock`
- 1 sample vendor
- 1 sample vendor API
- endpoints linked to `dashboard` and `monitoring-stock`
- dynamic pages:
  - `/page/dashboard`
  - `/page/monitoring-stock`
- dynamic sidebar items for those pages

Sample users:

| Email | Role | Password |
|-------|------|----------|
| `admin@paramita.com` | `admin` | `password` |
| `pusat@paramita.com` | `pimpinan-pusat` | `password` |
| `daerah@paramita.com` | `pimpinan-daerah` | `password` |
| `viewer@paramita.com` | `viewer` | `password` |

---

## Performance Notes

### Strict Mode

`app/Providers/AppServiceProvider.php`

```php
Model::shouldBeStrict(! $this->app->isProduction());
```

Meaning:

- development catches lazy loading aggressively
- production remains more tolerant
- new code should prefer explicit `with()` and selected columns

### Recommended Environment Defaults

From `.env.example` and current optimization direction:

```env
APP_URL=http://127.0.0.1:8000
DB_HOST=127.0.0.1
DB_PORT=3307
SESSION_DRIVER=file
CACHE_STORE=file
```

Rationale:

- `DB_PORT=3307` matches current Laragon MySQL setup; use `3306` on default MySQL installs
- file-backed session/cache avoids database overhead on single-server setups

---

## Testing and Verification

Current verification workflow used during optimization:

- `php artisan route:list`
- `php -l <file>`

Important environment note:

- full `php artisan test` is currently blocked on machines where PHP CLI lacks `pdo_sqlite`

Relevant test files:

- `tests/Feature/RoleBasedLoginTest.php`
- `tests/Feature/DynamicPageTest.php`
- `tests/Feature/SidebarMenuItemTest.php`
- `tests/Feature/ApiEndpointResourceTest.php`
- `tests/Feature/PageResourceTest.php`

---

## Maintenance Guidance

When extending the system:

1. prefer Dynamic Pages over adding new role-specific controllers/views
2. prefer database-managed sidebar items over hardcoded menu links
3. keep Filament relation queries explicit and narrowly loaded
4. keep searchable select preloading only where initial options materially help admin users
5. if adding a role, update both database roles and `config/roles.php`
6. if changing seeded page access, update both `DatabaseDefaultSeeder` and `SidebarMenuSeeder`

---

Document last updated: June 23, 2026
