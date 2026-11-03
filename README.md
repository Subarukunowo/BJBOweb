# BJBO Sedagho — Pasar Digital Komunitas Kabupaten Bengkalis

<p align="center">
  <img src="public/images/bjbo.png" alt="BJBO Sedagho Logo" width="140" style="border-radius: 16px;">
</p>

<p align="center">
  <strong>Platform E-Commerce & Marketplace Komunitas Terpercaya untuk Kabupaten Bengkalis</strong><br>
  Ditenagai oleh Laravel 11, Tailwind CSS, Laravel Reverb (Real-time WebSockets), dan Midtrans Payment Gateway.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 11">
  <img src="https://img.shields.io/badge/Tailwind_CSS-3.4-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/Laravel_Reverb-Real--time-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel Reverb">
  <img src="https://img.shields.io/badge/Midtrans-Snap_%26_QRIS-002855?style=for-the-badge" alt="Midtrans">
  <img src="https://img.shields.io/badge/PHPUnit-100%25_Passed-brightgreen?style=for-the-badge" alt="Tests">
</p>

---

## 📌 Tentang Proyek

**BJBO Sedagho** (Barang Jual Beli Online Sedagho) adalah platform *marketplace peer-to-peer* dan *official store* modern yang menghubungkan penjual lokal dan pembeli di seluruh wilayah Kabupaten Bengkalis (Bengkalis Kota, Mandau, Duri, Pinggir, Bukit Batu, Siak Kecil, Rupat, dan Bantan). 

Dibangun dengan standar UI/UX e-commerce terkemuka ala Tokopedia, sistem ini mengutamakan transaksi yang aman lewat **Rekening Bersama (Rekber/Escrow)**, **Verifikasi Identitas Toko**, **Moderasi Konten Otomatis**, dan **Komunikasi Real-time**.

---

## ✨ Fitur Utama

### 🛒 1. Pengalaman Belanja & Antarmuka Modern
* **Tokopedia-Style Clean UI:** Tampilan katalog responsif dengan banner promosi komunitas, carousel rekomendasi, dan bilah filter kategori horizontal yang mudah digeser (*touch-friendly*).
* **Mobile-First Bottom Navigation:** Bilah navigasi bawah mengambang khusus perangkat ponsel cerdas (*smartphone*) dengan akses instan ke Beranda, Chat, Toko, Pesanan, dan Akun.
* **Smart Search & Filters:** Pencarian barang instan berdasarkan kata kunci produk, kategori, dan filter wilayah/kecamatan di Bengkalis.
* **Wishlist & Favorit:** Simpan barang idaman dengan animasi interaktif tanpa me-refresh halaman.

### 🏪 2. Sistem Multi-Tier Toko & Verifikasi Penjual
* **Toko Perorangan (UMKM & Penjual Mandiri):** Pendaftaran cepat dengan validasi Nomor Induk Kependudukan (NIK 16 digit) dan foto KTP asli.
* **Official Store (Brand Resmi & Perusahaan):** Skema kurasi premium yang dilengkapi verifikasi legalitas NIB (Nomor Induk Berusaha), NPWP Perusahaan, serta Sertifikat HAKI resmi.
* **Proteksi Seller:** Pengguna yang belum memiliki toko aktif otomatis dicegah menjual produk dan diarahkan untuk melengkapi data toko terlebih dahulu.
* **Dashboard Seller Center (`/user/profil`):** Statistik performa toko (total produk, produk siap jual, pesanan masuk, saldo rekber), pengelolaan etalase, dan penanda status barang terjual (*sold out*).

### 💬 3. Chat Real-time Terenkripsi (Laravel Reverb & Echo)
* **Real-time Messaging:** Pertukaran pesan instan antara pembeli dan penjual secara langsung tanpa perlu me-refresh halaman browser.
* **Keamanan Privasi:** Sensor otomatis nomor HP dan kontak luar pada ruang obrolan untuk mencegah transaksi di luar platform yang berisiko penipuan.
* **Pelaporan Otomatis:** Deteksi kata-kata mencurigakan atau barang terlarang pada obrolan yang otomatis diteruskan ke admin kurasi.

### 💳 4. Integrasi Gerbang Pembayaran Midtrans & QRIS
* **Midtrans Snap In-Page:** Jendela pembayaran popup modern langsung di dalam website tanpa meninggalkan halaman BJBO.
* **QRIS Standar Nasional:** Pembayaran instan melalui scan QRIS yang kompatibel dengan seluruh aplikasi mobile banking (BCA, Mandiri, BRI, BNI) dan dompet digital (GoPay, OVO, Dana, ShopeePay).
* **Virtual Account Bank:** Kemudahan transfer ke rekening virtual bank nasional terkemuka.
* **Simulator Sandbox Terpadu:** Opsi pengujian pembayaran instan bebas biaya untuk mempermudah simulasi dan pengembangan.

### 🛡️ 5. Rekening Bersama (Rekber / Escrow) & Tarik Dana
* **Perlindungan Pembeli:** Dana transaksi ditampung di dompet rekber hingga pesanan diterima dengan baik oleh pembeli.
* **Pencairan Dana (Withdrawal):** Penjual dapat mendaftarkan nomor rekening bank lokal dan mengajukan penarikan saldo secara transparan dalam estimasi 2x24 jam kerja.

### 🤖 6. Mesin Rekomendasi Pintar (Recommendation Engine)
* Algoritma rekomendasi personal berbasis riwayat pencarian pengguna (*User Search Logs*) dan interaksi produk.
* **Filter Kepemilikan:** Menghindari produk milik penjual itu sendiri muncul di rekomendasi feed belanjanya.

---

## 🛠️ Arsitektur & Teknologi

| Komponen | Teknologi |
| :--- | :--- |
| **Backend Framework** | Laravel 11.x (PHP 8.2+) |
| **Frontend Styling** | Tailwind CSS 3.4 & Vanilla JS Modern |
| **Bundler & Build Tool** | Vite 5.x |
| **Broadcasting / WebSocket** | Laravel Reverb & Laravel Echo |
| **Payment Gateway** | Midtrans PHP SDK & Snap JS |
| **Database** | PostgreSQL / MySQL / SQLite |
| **Queue Worker** | Laravel Queue (`database` driver) |
| **Testing Suite** | PHPUnit 11 (Unit Tests & Feature Tests) |

---

## 🚀 Panduan Instalasi & Menjalankan

### 1. Kloning Repositori
```bash
git clone https://github.com/Subarukunowo/BJBOweb.git
cd BJBOweb
```

### 2. Instalasi Dependensi PHP & JavaScript
```bash
composer install
npm install
```

### 3. Konfigurasi Environment (`.env`)
Salin file konfigurasi contoh:
```bash
cp .env.example .env
php artisan key:generate
```

Sesuaikan pengaturan database, Reverb, dan kredensial Midtrans di file `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=bjbo_db
DB_USERNAME=postgres
DB_PASSWORD=your_password

# Konfigurasi Midtrans Sandbox
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxxxxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxxxxxx
MIDTRANS_IS_PRODUCTION=false

# Konfigurasi Laravel Reverb
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=your_reverb_id
REVERB_APP_KEY=your_reverb_key
REVERB_APP_SECRET=your_reverb_secret
REVERB_HOST="127.0.0.1"
REVERB_PORT=8080
REVERB_SCHEME=http
```

### 4. Migrasi & Seeder Database
```bash
php artisan migrate --seed
php artisan storage:link
```

### 5. Kompilasi Aset Frontend
```bash
npm run build
```

### 6. Menjalankan Layanan (Development)
Jalankan ketiga perintah berikut pada terminal terpisah:

```bash
# Terminal 1: Server Aplikasi Web
php artisan serve

# Terminal 2: Server WebSocket Real-time Reverb
php artisan reverb:start --debug

# Terminal 3: Worker Pemroses Antrean Notifikasi
php artisan queue:listen
```

Buka peramban Anda di: `http://127.0.0.1:8000`.

---

## 🧪 Pengujian Otomatis (Testing)

Proyek ini dilengkapi dengan *test suite* menyeluruh (Unit & Feature):
```bash
php artisan test
```

Cakupan pengujian meliputi:
- `AuthFeatureTest`: Registrasi, Login, Remember Me, dan OTP.
- `UserEcommerceFeatureTest`: Alur transaksi, wishlist, proteksi kepemilikan toko, dan toggle terjual.
- `SecurityAndChatFeatureTest`: Enkripsi chat, sensor nomor HP, dan moderasi konten terlarang.
- `AdminFeatureTest`: Kurasi toko perorangan/official, persetujuan penarikan rekber, dan moderasi laporan.

---

## 📄 Lisensi
Hak cipta &copy; 2024–2026 **BJBO Sedagho**. Dikelola oleh **PT Subaru Technologi Bangsa**.
Seluruh hak cipta dilindungi undang-undang.
