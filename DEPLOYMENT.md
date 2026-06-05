# 🚀 DEPLOYMENT GUIDE - PARAMITA SYSTEM

## 📋 Table of Contents

1. [Server Requirements](#server-requirements)
2. [Fresh Installation](#fresh-installation)
3. [Server Migration](#server-migration)
4. [Performance Optimization](#performance-optimization)
5. [Troubleshooting](#troubleshooting)

---

## 🖥️ Server Requirements

| Component | Minimum | Recommended |
|-----------|---------|-------------|
| PHP | 8.2+ | 8.3 |
| MySQL | 8.0 | 8.0+ |
| RAM | 2GB | 4GB+ |
| Disk | 10GB | 20GB+ |

### Required PHP Extensions

```bash
php -m | grep -E "curl|mbstring|openssl|pdo|tokenizer|xml|ctype|json|bcmath|gd|zip|fileinfo|intl"
```

Pastikan semua extension di atas terinstall. Di Laragon, bisa diaktifkan dari **Laragon Menu → PHP → Extensions**.

---

## 📦 Fresh Installation

### Step 1: Clone & Setup

```bash
# Clone ke direktori web server (misal: C:\laragon\www\)
cd C:\laragon\www
git clone <your-repo-url> paramita
cd paramita

# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node dependencies & build assets
npm ci
npm run build
```

### Step 2: Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

Edit `.env` dengan konfigurasi yang sesuai:

```env
APP_NAME=Paramita
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost/paramita

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=paramita
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=database
```

### Step 3: Database Setup

```bash
# Buat database (via MySQL CLI atau phpMyAdmin)
mysql -u root -p -e "CREATE DATABASE paramita CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
php artisan migrate --force

# Seed initial data
php artisan db:seed --force
```

### Step 4: Optimize

```bash
# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### Step 5: Set File Permissions (Linux)

```bash
# Hanya untuk Linux/Mac, skip untuk Windows
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

Di **Laragon/Windows**, pastikan folder `storage` dan `bootstrap/cache` writable.

---

## 🔄 Server Migration

### Pre-Migration Checklist

- [ ] Backup database di server lama
- [ ] Backup `.env` file (berisi APP_KEY!)
- [ ] Backup folder `storage/` (avatars, logs)
- [ ] Catat versi Laravel yang digunakan

### Step 1: Backup Server Lama

```bash
# Backup database
mysqldump -u root -p paramita > paramita_backup_$(date +%Y%m%d).sql

# Backup application files (kecuali vendor/node_modules/.git)
# Di Windows: gunakan 7-Zip atau tar
# Di Linux:
tar -czf paramita_files.tar.gz \
    --exclude='vendor' \
    --exclude='node_modules' \
    --exclude='.git' \
    /path/to/paramita

# PENTING: Backup .env (berisi APP_KEY!)
cp .env /tmp/paramita_env_backup
```

### Step 2: Setup Server Baru

```bash
# Install prerequisites
# - PHP 8.2+ dengan extensions
# - MySQL 8.0+
# - Composer
# - Node.js & npm

# Install dependencies
composer install --optimize-autoloader --no-dev
npm ci && npm run build
```

### Step 3: Restore di Server Baru

```bash
# Copy .env dari backup (PENTING: APP_KEY harus sama!)
cp /path/to/paramita_env_backup .env

# Verifikasi APP_KEY ada
grep APP_KEY .env

# Restore database
mysql -u root -p paramita < paramita_backup.sql

# Jalankan migration jika ada yang tertinggal
php artisan migrate --force

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### Step 4: Verifikasi

```bash
# Test aplikasi
php artisan about

# Cek error logs
tail -f storage/logs/laravel.log

# Test akses web
curl -I http://localhost/paramita
```

---

## ⚡ Performance Optimization

### OPcache

Pastikan OPcache aktif di PHP. Di Laragon:

1. Buka **Laragon Menu → PHP → php.ini**
2. Cari dan pastikan baris berikut ada:

```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=10000
opcache.validate_timestamps=0
opcache.enable_cli=1
```

3. Restart Apache/Nginx dari Laragon

### Redis (Opsional, Recommended)

Untuk performa lebih baik, gunakan Redis untuk cache dan sessions:

1. Install Redis (di Laragon: **Menu → Preferences → WSL** atau install manual)
2. Install php-redis extension
3. Update `.env`:

```env
CACHE_STORE=redis
SESSION_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

4. Rebuild cache:
```bash
php artisan config:cache
php artisan route:cache
```

### Database Indexes

Project sudah include indexes di migrations. Untuk tabel besar, verifikasi:

```sql
SHOW INDEX FROM api_requests;
SHOW INDEX FROM vendor_apis;
SHOW INDEX FROM vendors;
```

---

## 🔧 Troubleshooting

### 1. "APP_KEY not set" Error

```bash
grep APP_KEY .env
# Jika kosong:
php artisan key:generate
```

### 2. "No application encryption key has been specified"

```bash
php artisan config:cache
```

### 3. Database Connection Failed

```bash
php artisan tinker
>>> DB::connection()->getPdo();
```

### 4. Permission Denied on Storage (Linux)

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 5. Filament Panel Shows 403

```bash
php artisan tinker
>>> App\Models\User::where('email', 'admin@paramita.com')->first()->hasRole('admin');
```

### 6. Filament Loading Lambat

```bash
# Clear semua caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Rebuild
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 7. Migration Error Setelah Pindah Server

```bash
php artisan migrate:status
php artisan migrate --force
```

### 8. Laravel Mix/Vite Build Error

```bash
rm -rf node_modules
npm ci
npm run build
```

---

## 📝 Post-Deployment Checklist

- [ ] APP_KEY ada dan benar di `.env`
- [ ] APP_ENV=production
- [ ] APP_DEBUG=false
- [ ] Database connection berhasil
- [ ] Filament admin panel bisa diakses di /admin
- [ ] Login dengan admin account berhasil
- [ ] OPcache aktif
- [ ] File permissions benar (Linux)
- [ ] Error logs tidak ada error

---

**Last Updated:** June 2026
**Laravel Version:** 12.x
**Filament Version:** 3.3
