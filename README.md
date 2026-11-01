Dashboard Indikator Makro Kota Tasikmalaya

Aplikasi web Sistem Informasi Dashboard Indikator Makro untuk pemantauan, visualisasi, dan analisis indikator capaian pembangunan daerah (Bapperida Kota Tasikmalaya).

Fitur Utama

- Dashboard Makro Interaktif:
  - Ringkasan indikator makro utama (IPM, Pertumbuhan Ekonomi, Kemiskinan, Pengangguran Terbuka, Gini Ratio, dll).
  - Filter tahun instan dan visualisasi perbandingan capaian riil vs target (RKPD / RPJMD).
  - Tren grafik interaktif per indikator berbasis Chart.js.
- Analisis & Proyeksi Indikator:
  - Filter rentang tahun kustom (misal: 2010 - 2024).
  - Analisis pertumbuhan tahun ke tahun.
  - Estimasi dan proyeksi masa depan.
  - Detail riwayat capaian dan target secara komprehensif.
- Manajemen Data (Admin Panel):
  - CRUD Indikator (Hierarki Indikator Utama, Komponen, dan Subindikator).
  - CRUD Capaian Pembangunan Daerah.
  - CRUD Target Pembangunan Daerah (Dukungan nilai desimal koma maupun rentang angka target seperti `9,2 - 10,16`).
  - Import Data Capaian & Target massal via Excel (`.xlsx`) lengkap dengan template otomatis.
- Multi-Role & Keamanan:
  - Guard terpisah: Akun Administrator dan Pengguna Publik (Register/Login).
  - Manajemen Pengguna oleh Admin.
  - Pengaturan URL Login Admin yang dapat diubah secara dinamis untuk proteksi keamanan dari brute-force.
- Performa Tinggi:
  - Database Indexing B-Tree teroptimasi pada kolom pencarian dan filter tahun.
  - Animasi transisi instan tanpa delay render buatan.
