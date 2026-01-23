# Sistem Presensi Mahasiswa

Aplikasi web untuk mengelola presensi mahasiswa menggunakan Laravel.

## Requirements

- PHP >= 8.1
- MySQL
- Composer

## Instalasi

1. Clone repository
```bash
git clone <repository-url>
cd persensi
```

2. Install dependencies
```bash
composer install
```

3. Setup file .env
```bash
cp .env.example .env
php artisan key:generate
```

4. Konfigurasi database MySQL di file `.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=username_mysql
DB_PASSWORD=password_mysql
```

5. Jalankan migrasi database
```bash
php artisan migrate
```

6. Jalankan aplikasi
```bash
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000`

## Login

- **Admin**: Buat akun admin via command `php artisan user:create-admin`
- **User**: Akun user dibuat otomatis saat menambahkan mahasiswa

## Command: Buat Admin

### Interaktif
```bash
php artisan user:create-admin
```

Catatan:
- Jika email sudah ada, user akan dipromosikan jadi admin
- Jika password kosong, sistem akan auto-generate dan menampilkannya

## Task Scheduler (Auto Absen)

Aplikasi memakai scheduler untuk mengisi otomatis status `alpha` bagi mahasiswa yang belum absen setelah sesi berakhir. Jalankan scheduler Laravel tiap 1 menit lewat cron.

### Manual (jalanin sekali)
```bash
php artisan kehadiran:finalize-sesi
```

### Linux / macOS (cron)
```bash
* * * * * cd /path/ke/project && php artisan schedule:run >> /dev/null 2>&1
```

## Fitur

- Manajemen Data Mahasiswa
- Manajemen Sesi Presensi
- Manajemen Kehadiran
- Manajemen User
