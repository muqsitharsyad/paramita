# Paramita

Paramita is a Laravel 12 + Filament 3.3 API Gateway Management System for managing vendor APIs, validating JSON responses, monitoring endpoint health, and serving role-based Dynamic Pages.

## Core Features

- Filament admin panel for vendor, API, endpoint, template, RBAC, and sidebar management
- Dynamic Pages at `/page/{slug}` driven from database configuration
- role-based access control with Spatie Laravel Permission
- endpoint testing with template-based response validation
- API request logging and health status tracking
- database-driven sidebar navigation

## Current Architecture

### Admin Surface

- admin users access Filament at `/admin`

### User Surface

- non-admin users are redirected to Dynamic Pages
- default user landing page is `/page/dashboard`
- user navigation is read from `sidebar_menu_items`

### Runtime Direction

The project no longer uses legacy role-specific dashboards such as:

- `manager/*`
- `operator/*`
- `viewer/*`

Those flows have been replaced by:

- `config/roles.php`
- `DynamicPageController`
- `pages`, `page_role`, and `page_template`
- `sidebar_menu_items`

## Tech Stack

- PHP 8.2+
- Laravel 12
- Filament 3.3
- Spatie Laravel Permission 6
- MySQL or MariaDB
- Blade + Livewire

## Installation

```bash
git clone <repo-url>
cd paramita
composer install
npm install
copy .env.example .env
php artisan key:generate
```

Update `.env` with your database credentials, then run:

```bash
php artisan migrate:fresh --seed
php artisan storage:link
npm run build
```

For local development:

```bash
composer run dev
```

## Seeded Accounts

After `migrate:fresh --seed`, these users are available:

| Email | Role | Password |
|-------|------|----------|
| `admin@paramita.com` | `admin` | `password` |
| `pusat@paramita.com` | `pimpinan-pusat` | `password` |
| `daerah@paramita.com` | `pimpinan-daerah` | `password` |
| `viewer@paramita.com` | `viewer` | `password` |

## Important Routes

| Route | Purpose |
|------|---------|
| `/login` | application login |
| `/admin` | Filament admin dashboard |
| `/page/{slug}` | Dynamic Page runtime |
| `/home` | role-aware redirect |

## Seeder Output

Fresh seed creates:

- roles and permissions
- sample users
- JSON templates including `monitoring-stock`
- sample vendor and vendor API
- sample endpoint linked to a template
- Dynamic Pages:
  - `/page/dashboard`
  - `/page/monitoring-stock`
- default sidebar menu items for those pages

## Performance Notes

Applied optimization direction:

- strict model mode in non-production to catch lazy loading
- explicit eager loading in Filament resources
- narrower relation columns where possible
- deferred loading for large searchable relation selects
- cached and pooled remote fetches in `DynamicPageController`

Recommended environment defaults:

```env
SESSION_DRIVER=file
CACHE_STORE=file
```

## Testing

Common verification commands:

```bash
php artisan route:list
php artisan test
```

Note:

- `php artisan test` requires a working test database
- if your PHP CLI does not have `pdo_sqlite`, feature tests that rely on SQLite will fail until that extension is enabled or the test database is changed

## Project Reference

Detailed architecture and maintenance notes live in:

- `AGENTS.md`

Use that file as the current internal reference before changing routes, RBAC behavior, Dynamic Pages, or Filament resources.
