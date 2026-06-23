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

Update `.env` with your database credentials. Current local defaults are:

```env
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=paramita
DB_USERNAME=root
DB_PASSWORD=
SESSION_DRIVER=file
CACHE_STORE=file
```

Use `DB_PORT=3306` instead if MySQL on the target device still uses the default port.

Create the database first, then run:

```bash
php artisan migrate:fresh --seed
php artisan storage:link
npm run build
```

For local development:

```bash
php artisan serve --host=127.0.0.1 --port=8000
npm run dev
```

If you use Laragon Apache on port `81`, Apache can serve the project, but the documented local app URL above uses Laravel's development server at `http://127.0.0.1:8000`.

After pulling updates on another device:

```bash
composer install
npm install
php artisan migrate --seed
php artisan optimize:clear
npm run build
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
- JSON templates including `dashboard` and `monitoring-stock`
- sample vendor and vendor API
- sample endpoints linked to templates:
  - `?api=1&route=Dashboard`
  - `?api=1&route=Monitoring_Stock_Bahan_ajar`
- Dynamic Pages:
  - `/page/dashboard`
  - `/page/monitoring-stock`
- default sidebar menu items for those pages

## Performance Notes

Applied optimization direction:

- strict model mode in non-production to catch lazy loading
- explicit eager loading in Filament resources
- narrower relation columns where possible
- searchable relation selects preload their first options for better admin UX
- cached and pooled remote fetches in `DynamicPageController`
- Dynamic Pages pass `limit`, `offset`, and `search` query parameters to endpoint URLs

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
