<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Paramita

Paramita adalah aplikasi web yang dibangun menggunakan Laravel framework. Aplikasi ini dikembangkan untuk [jelaskan tujuan/fungsi aplikasi Anda di sini].

## Tentang Paramita

Paramita menyediakan fitur-fitur berikut:

-   [Daftar fitur utama aplikasi]
-   [Fitur kedua]
-   [Fitur ketiga]

## Teknologi yang Digunakan

-   **Backend**: Laravel 11
-   **Database**: MySQL
-   **Frontend**: Blade Templates, Bootstrap/CSS
-   **Server**: Apache/Nginx

## Persyaratan Sistem

-   PHP >= 8.1
-   Composer
-   MySQL/MariaDB
-   Node.js & NPM (untuk asset compilation)
-   Apache/Nginx web server

## Cara Instalasi

Ikuti langkah-langkah berikut untuk menginstall aplikasi setelah melakukan clone repository:

### 1. Clone Repository

```bash
git clone https://github.com/username/paramita.git
cd paramita
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Environment Setup

```bash
# Copy file environment
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Configuration

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=paramita
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Database Migration & Seeding

```bash
# Buat database baru di MySQL
# Kemudian jalankan migration
php artisan migrate

# (Opsional) Jalankan seeder untuk data awal
php artisan db:seed
```

### 6. Storage Link

```bash
# Buat symbolic link untuk storage
php artisan storage:link
```

### 7. Compile Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 8. Jalankan Aplikasi

```bash
# Menggunakan built-in server Laravel
php artisan serve

# Atau akses melalui web server (Apache/Nginx)
# http://localhost/paramita/public
```

## Konfigurasi Tambahan

### Cache Configuration

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### File Permissions (Linux/Mac)

```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

## Penggunaan

1. Akses aplikasi melalui browser di `http://localhost:8000` (jika menggunakan `php artisan serve`)
2. [Tambahkan instruksi penggunaan aplikasi]

## Kontribusi

Jika Anda ingin berkontribusi pada project ini:

1. Fork repository ini
2. Buat branch untuk fitur baru (`git checkout -b fitur-baru`)
3. Commit perubahan (`git commit -am 'Menambah fitur baru'`)
4. Push ke branch (`git push origin fitur-baru`)
5. Buat Pull Request

## Troubleshooting

### Error "Permission denied"

```bash
# Linux/Mac
sudo chown -R $USER:www-data storage
sudo chown -R $USER:www-data bootstrap/cache
```

### Error "Key not found"

```bash
php artisan key:generate
```

### Error Database Connection

-   Pastikan MySQL service berjalan
-   Cek konfigurasi database di file `.env`
-   Pastikan database sudah dibuat

## Lisensi

Project ini menggunakan lisensi [MIT License](https://opensource.org/licenses/MIT).

## Kontak

-   **Developer**: [Nama Anda]
-   **Email**: [email@example.com]
-   **GitHub**: [https://github.com/username](https://github.com/username)

---

<p align="center">Dibuat dengan ❤️ menggunakan Laravel</p>
