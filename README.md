# Dashboard Indikator Makro Kota Tasikmalaya

Aplikasi web Sistem Informasi Dashboard Indikator Makro untuk pemantauan, visualisasi, dan analisis indikator capaian pembangunan daerah (Bapperida Kota Tasikmalaya).

---

## Fitur Utama

- **Dashboard Makro Interaktif**:
  - Ringkasan indikator makro utama (IPM, Pertumbuhan Ekonomi, Kemiskinan, Pengangguran Terbuka, Gini Ratio, dll).
  - Filter tahun instan dan visualisasi perbandingan capaian riil vs target (RKPD / RPJMD).
  - Tren grafik interaktif per indikator berbasis Chart.js.
- **Analisis & Proyeksi Indikator**:
  - Filter rentang tahun kustom (misal: 2010 - 2024).
  - Analisis pertumbuhan tahun ke tahun (*Growth Rate*).
  - Estimasi dan proyeksi masa depan (*Linear Regression & Trend Projection*).
  - Detail riwayat capaian dan target secara komprehensif.
- **Manajemen Data (Admin Panel)**:
  - CRUD Indikator (Hierarki Indikator Utama, Komponen, dan Subindikator).
  - CRUD Capaian Pembangunan Daerah.
  - CRUD Target Pembangunan Daerah (Dukungan nilai desimal koma maupun rentang angka target seperti `9,2 - 10,16`).
  - Import Data Capaian & Target massal via Excel (`.xlsx`) lengkap dengan template otomatis.
- **Multi-Role & Keamanan**:
  - Guard terpisah: Akun Administrator dan Pengguna Publik (Register/Login).
  - Manajemen Pengguna oleh Admin.
  - Pengaturan URL Login Admin yang dapat diubah secara dinamis (*Dynamic Admin Login Path*) untuk proteksi keamanan dari brute-force.
- **Performa Tinggi**:
  - Database Indexing B-Tree teroptimasi pada kolom pencarian dan filter tahun.
  - Animasi transisi instan tanpa delay render buatan.

---

## Tech Stack

- **Backend Framework**: [Laravel 10](https://laravel.com) (PHP 8.1+)
- **Database**: MySQL / MariaDB (Didukung SQLite untuk automated testing)
- **Frontend**: Blade Templating, Vanilla CSS (Modern Clean UI, Responsive Design)
- **Visualisasi**: Chart.js
- **Pengolahan Data Excel**: Maatwebsite / Laravel-Excel & PhpSpreadsheet
- **Testing**: PHPUnit / Pest Feature Test Suite (82 tests, 376 assertions)

---

##  Panduan Instalasi Lokal

### 1. Clone Repository
```bash
git clone https://github.com/panipadika/dashboard-data.git
cd dashboard-data
```

### 2. Install Dependensi PHP & Frontend
```bash
composer install
npm install && npm run build
```

### 3. Konfigurasi Environment
Salin file `.env.example` ke `.env`:
```bash
cp .env.example .env
```
Sesuaikan konfigurasi database pada file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_dashboardmakro
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Jalankan Migrasi & Seeder
```bash
php artisan migrate --seed
```

### 6. Jalankan Server Lokal
```bash
php artisan serve
```
Akses aplikasi melalui browser di `http://127.0.0.1:8000`.

---

## Menjalankan Automated Test
Aplikasi telah dilengkapi dengan unit dan feature test menyeluruh untuk memastikan semua endpoint, autentikasi, kalkulasi analisis, import Excel, dan CRUD bekerja tanpa kendala:
```bash
php artisan test
```

---

## 📄 Lisensi
Dikembangkan untuk keperluan internal dan publikasi data pembangunan Bappelitbangda Kota Tasikmalaya.
