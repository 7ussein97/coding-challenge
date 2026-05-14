# Installation Guide

Complete step-by-step setup for UTAS CP Platform on a local machine (Windows/Linux/macOS with XAMPP or native PHP).

---

## Prerequisites

| Requirement | Minimum Version |
|-------------|----------------|
| PHP | 8.2+ |
| Composer | 2.x |
| MySQL | 8.0+ (or MariaDB 10.6+) |
| PHP Extensions | `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `fileinfo`, `gd` |

> **XAMPP users:** PHP 8.2+ and MySQL are included. Ensure Apache & MySQL are running in the XAMPP control panel (only MySQL is needed for `php artisan serve`).

---

## Step 1 — Clone the Project

```bash
git clone <your-repo-url> UTAS
cd UTAS
```

Or if you already have the files:

```bash
cd C:/xampp/htdocs/UTAS   # Windows XAMPP
# or
cd /var/www/html/UTAS     # Linux
```

---

## Step 2 — Install PHP Dependencies

```bash
composer install --no-dev --optimize-autoloader
```

> For development (includes testing tools):
> ```bash
> composer install
> ```

---

## Step 3 — Configure Environment

```bash
cp .env.example .env
```

Open `.env` and set the following:

```env
APP_NAME="UTAS CP Platform"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=utas_cp
DB_USERNAME=root
DB_PASSWORD=          # leave blank if XAMPP root has no password
```

---

## Step 4 — Generate Application Key

```bash
php artisan key:generate
```

---

## Step 5 — Create MySQL Database

**Option A — CLI:**
```bash
mysql -u root -e "CREATE DATABASE utas_cp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

**Option B — phpMyAdmin:**
Navigate to `http://localhost/phpmyadmin` → New → Name: `utas_cp` → Create.

---

## Step 6 — Run Migrations

```bash
# Fresh install (drops and recreates all tables):
php artisan migrate:fresh --seed

# Or just migrate (keeps existing data):
php artisan migrate
```

The `--seed` flag creates three demo accounts:
| Role  | Email              | Password |
|-------|--------------------|----------|
| Admin | admin@admin.com    | password |
| Judge | judge@judge.com    | password |
| Team  | alpha@team.com     | password |

---

## Step 7 — Create Storage Symlink

```bash
php artisan storage:link
```

This creates `public/storage → storage/app/public` so uploaded images are web-accessible.

---

## Step 8 — Start the Server

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

| Access | URL |
|--------|-----|
| Localhost | http://localhost:8000 |
| Same LAN device | http://\<your-ip\>:8000 |

To find your local IP:
- **Windows:** `ipconfig` → IPv4 Address
- **Linux/macOS:** `ip addr show` or `ifconfig`

---

## Troubleshooting

### "Class not found" errors
```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

### Blank page / 500 error
```bash
# Check logs:
tail -f storage/logs/laravel.log    # Linux/macOS
type storage\logs\laravel.log       # Windows
```
Make sure `storage/` and `bootstrap/cache/` are writable:
```bash
chmod -R 775 storage bootstrap/cache   # Linux/macOS
```

### MySQL connection refused
- Ensure MySQL/MariaDB service is running
- XAMPP: Start MySQL from the XAMPP Control Panel
- Verify credentials in `.env` match your MySQL setup

### Images not showing
```bash
php artisan storage:link
```

### Port 8000 already in use
```bash
php artisan serve --host=0.0.0.0 --port=8080
# Then access via http://localhost:8080
```

---

## Production Deployment Notes

For a production server (not required for competition use):
1. Set `APP_ENV=production` and `APP_DEBUG=false` in `.env`
2. Run `php artisan config:cache && php artisan route:cache`
3. Use a proper web server (Nginx/Apache) instead of `artisan serve`
4. Set up proper file permissions for `storage/` and `bootstrap/cache/`
