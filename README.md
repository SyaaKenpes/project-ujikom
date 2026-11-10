# Peminjaman Alat API

REST API peminjaman alat berbasis **Laravel (PHP 8.4)** + **MySQL 8.0**, dijalankan dengan **Docker Compose**.
Dibuat untuk keperluan Uji Kompetensi (UKOM) — Pasyha Zulfahmi Rochmat.

---

## Daftar Isi

1. [Ringkasan Layanan](#ringkasan-layanan)
2. [Prasyarat (sekali saja)](#prasyarat-sekali-saja)
3. [Setup SSH Key GitHub (sekali saja)](#setup-ssh-key-github-sekali-saja)
4. [Setup Project dari Nol](#setup-project-dari-nol)
5. [Urutan Cepat Hari H](#urutan-cepat-hari-h)
6. [Perintah Sehari-hari](#perintah-sehari-hari)
7. [Troubleshooting](#troubleshooting)

---

## Ringkasan Layanan

| Service (`docker compose`) | Container | Port | Fungsi |
|---|---|---|---|
| `app` | `ujikom_app` | `8090:80` | Backend Laravel / Web App |
| `db` | `ujikom_db` | `3309:3306` | Database MySQL 8.0 (DB: `ujikom_api`) |
| `phpmyadmin` | `ujikom_phpmyadmin` | `8091:80` | GUI pengelolaan database |

| Akses | Alamat |
|---|---|
| Web App / API | http://localhost:8090 |
| phpMyAdmin | http://localhost:8091 |

**Kredensial database**

| Key | Value |
|---|---|
| Database | `ujikom_api` |
| Username | `user` |
| Password | `password` |
| Root password | `root_password` |

> Saat `docker compose exec`, pakai **nama service** (`app`), bukan nama container (`ujikom_app`).

---

## Prasyarat (sekali saja)

### A. Di Windows
Install: **Docker Desktop**, **VS Code** (+ ekstensi *WSL*), **Git**.

Buka **PowerShell (Run as Administrator)**:

```powershell
wsl --install
```

Restart komputer jika diminta, lalu buat user Ubuntu.
Pastikan Docker Desktop: *Settings → Resources → WSL Integration* → aktifkan untuk **Ubuntu**.

### B. Di WSL Ubuntu
Buka VS Code → klik pojok kiri bawah → **Connect to WSL (Ubuntu)** → buka terminal (`Ctrl + ~`):

```bash
sudo apt update
sudo apt install php-cli php-mbstring php-xml php-curl php-zip php-mysql composer git -y
```

---

## Setup SSH Key GitHub (sekali saja)

> Key **harus dibuat di terminal WSL Ubuntu**, bukan di Git Bash Windows.

```bash
# 1. Generate key (tekan Enter 3x sampai selesai)
ssh-keygen -t ed25519 -C "pasyha@gmail.com"

# 2. Tampilkan public key, lalu salin seluruh isinya
cat ~/.ssh/id_ed25519.pub
```

3. Buka GitHub → **Settings → SSH and GPG keys → New SSH key** → tempel → Save.
4. Tes koneksi:

```bash
ssh -T git@github.com
```

---

## Setup Project dari Nol

Semua perintah dijalankan di **terminal WSL Ubuntu**.

### 1. Clone repository

```bash
git clone git@github.com:SyaaKenpes/project-ujikom.git
cd project-ujikom
```

### 2. Nyalakan container Docker

Dari folder utama project (yang ada `docker-compose.yml`):

```bash
docker compose up -d
```

Tunggu ±30 detik pada run pertama agar MySQL selesai inisialisasi. Cek status:

```bash
docker compose ps
```

### 3. Install dependensi & buat file `.env`

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
```

### 4. Atur `.env`

Buka `backend/.env` di VS Code dan **pastikan nilai berikut** (ganti baris yang sudah ada):

```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=ujikom_api
DB_USERNAME=root
DB_PASSWORD=password

SESSION_DRIVER=file
```

> `.env.example` bawaan memakai `DB_CONNECTION=sqlite` dan baris `DB_*` ter-comment (`#`).
> Hapus tanda `#` dan ubah nilainya seperti di atas, kalau tidak migrasi tidak akan masuk ke MySQL.

Opsional — otomatis lewat `sed` (jalankan di folder `backend`):

```bash
sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=mysql/' .env
sed -i 's/^# *DB_HOST=.*/DB_HOST=db/' .env
sed -i 's/^# *DB_PORT=.*/DB_PORT=3306/' .env
sed -i 's/^# *DB_DATABASE=.*/DB_DATABASE=ujikom_api/' .env
sed -i 's/^# *DB_USERNAME=.*/DB_USERNAME=root/' .env
sed -i 's/^# *DB_PASSWORD=.*/DB_PASSWORD=password/' .env
sed -i 's/^SESSION_DRIVER=.*/SESSION_DRIVER=file/' .env
```

### 5. Izin folder `storage` & `cache`

Masih di folder `backend`:

```bash
sudo chmod -R 777 storage bootstrap/cache
```

### 6. Migrasi database + seeder

Kembali ke folder utama, lalu jalankan lewat Docker:

```bash
cd ..
docker compose exec app-laravel php artisan config:clear
docker compose exec app-laravel php artisan migrate --seed
```

### 7. Selesai — tes

- Web App: http://localhost:8090
- phpMyAdmin: http://localhost:8091 (user root / pass password)
- Endpoint login: `POST http://localhost:8090/api/login`

---

## Urutan Cepat Hari H

```bash
# 1. Clone & masuk folder
git clone git@github.com:SyaaKenpes/project-ujikom.git && cd project-ujikom

# 2. Docker
docker compose up -d

# 3. Dependensi & env (jika belum ada .env)
composer install && cp .env.example .env && php artisan key:generate

# 4. Edit .env (DB_CONNECTION=mysql, DB_HOST=db, DB_DATABASE=ujikom_api, DB_USERNAME=user, DB_PASSWORD=password, SESSION_DRIVER=file)
#    lalu beri izin permission & clear config:
docker exec -u 0 -it ujikom_app chmod -R 777 storage bootstrap/cache public
docker exec -it ujikom_app php artisan config:clear

# 5. Migrasi
docker exec -it ujikom_app php artisan migrate:fresh --seed
```

---

## Perintah Sehari-hari

```bash
docker compose up -d                # nyalakan semua container
docker compose down                 # matikan semua container
docker compose down -v              # matikan + HAPUS data database (reset total)
docker compose ps                   # lihat status container
docker compose logs -f app-laravel  # lihat log Laravel

# Artisan lewat Docker (dari folder utama)
docker compose exec app-laravel php artisan migrate:fresh --seed
docker compose exec app-laravel php artisan route:list
docker compose exec app-laravel php artisan config:clear
```

---

## Troubleshooting

| Error | Penyebab | Solusi |
|---|---|---|
| `Permission denied (publickey)` | SSH key dibuat di Git Bash Windows, bukan WSL | Jalankan `ssh-keygen` di terminal WSL, daftarkan `id_ed25519.pub` ke GitHub |
| `Could not open input file: artisan` | Terminal masih di folder utama | `cd backend` dulu sebelum `php artisan ...` |
| `service "..." is not running` / `no such service` | Memakai nama container, bukan nama service | Pakai `app-laravel` dari folder utama: `docker compose exec app-laravel ...` |
| `Permission denied` pada `laravel.log` | WSL tidak punya izin tulis di folder log | `sudo chmod -R 777 backend/storage backend/bootstrap/cache` |
| `Table 'sessions' doesn't exist` | `SESSION_DRIVER=database` sebelum migrasi | Ubah ke `SESSION_DRIVER=file`, lalu `docker compose exec app-laravel php artisan config:clear` |
| `Connection refused` / `SQLSTATE[HY000] [2002]` saat migrasi | MySQL belum siap atau `DB_HOST` salah | Tunggu ±30 detik, pastikan `DB_HOST=mysql-server` (bukan `127.0.0.1`), ulangi migrasi |
| Migrasi memakai SQLite, bukan MySQL | `DB_CONNECTION` masih `sqlite` | Set `DB_CONNECTION=mysql` dan hapus `#` pada baris `DB_*` di `.env` |
| Download image Docker timeout | Koneksi WSL ke registry lambat/putus | Ulangi `docker compose up -d`, atau set DNS: `sudo sh -c 'echo "nameserver 8.8.8.8" > /etc/resolv.conf'` |
| `docker: command not found` di WSL | Integrasi WSL Docker Desktop belum aktif | Docker Desktop → Settings → Resources → WSL Integration → aktifkan Ubuntu |

---

## Struktur Project

```
peminjaman-alat-api/
├── docker-compose.yml   # 3 service: mysql-server, app-laravel, phpMyadmin
└── backend/             # Aplikasi Laravel (API)
    ├── Dockerfile       # php:8.4-cli + pdo_mysql + composer
    ├── app/
    ├── database/        # migrations & seeders
    └── routes/api.php   # endpoint API (login, katalog, peminjaman, dll.)
```
