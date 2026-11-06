# Product Requirement Document (PRD) - Outgear Lite (v1.1)

## 1. Overview
Outgear Lite adalah aplikasi manajemen persewaan peralatan outdoor berbasis web yang dirancang untuk menjalankan arsitektur micro-services sederhana menggunakan Docker Compose (Nginx, PHP-FPM 8.4, MariaDB, Queue Worker, dan Scheduler).

---

## 2. Technical Stack
- **Framework:** Laravel 12 / PHP 8.4
- **Database:** MariaDB
- **Web Server:** Nginx
- **Background Processing:** Laravel Queue Worker
- **Automation Tasks:** Laravel Scheduler
- **Containerization:** Docker & Docker Compose

---

## 3. Core Features & Functional Requirements

### 3.1 Master Data Barang
- **CRUD Barang:** Admin dapat menambah barang baru (Nama Alat, Tarif per Hari, Stok).
- **Stok Management:** Stok barang berkurang otomatis saat terjadi transaksi sewa aktif dan bertambah kembali saat barang dikembalikan.

### 3.2 Transaksi Penyewaan (Queue Worker Integration)
- **Buat Transaksi:** Memilih barang, menginput nama penyewa, dan lama sewa (hari).
- **Validasi Stok:** Sistem menolak transaksi jika stok barang bernilai 0.
- **Asynchronous Notification Job:** Setiap transaksi baru dikirim ke antrean (`ProcessRentalNotification`) untuk diproses di latar belakang oleh Container Queue Worker.

### 3.3 Pengembalian Barang & Status Management
- **Daftar Transaksi Aktif:** Menampilkan transaksi dengan status `ACTIVE`.
- **Fitur Pengembalian (Return):** Tombol aksi untuk memproses pengembalian barang.
- **Perubahan Status:** Mengubah status transaksi dari `ACTIVE` ke `RETURNED` dan mengembalikan stok barang ke master data.

### 3.4 Automated Scheduler Tasks (Scheduler Container Integration)
- **Cron Jobs Execution:** Container Scheduler menjalankan task `schedule:run` secara otomatis.
- **Due-Date Check:** Memeriksa transaksi yang melewati batas hari sewa dan mencetak log peringatan keterlambatan di `storage/logs/laravel.log`.

---

## 4. Non-Functional Requirements & Security
- **Web Server Protection:** File sensitif (seperti `.env`) wajib mengembalikan HTTP 403 Forbidden pada Nginx.
- **Framework Caching:** Mendukung otomasi cache config, routes, views, dan events melalui skrip `deploy/deploy.sh`.