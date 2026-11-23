# BJBO Sedagho: Pasar Digital Komunitas Kabupaten Bengkalis

<p align="center">
  <img src="public/images/bjbo.png" alt="BJBO Sedagho Logo" width="140" style="border-radius: 16px;">
</p>

<p align="center">
  <strong>Platform E-Commerce & Marketplace Komunitas Terpercaya untuk Kabupaten Bengkalis</strong><br>
  Ditenagai oleh Laravel 11, Tailwind CSS, Laravel Reverb (Real-time WebSockets), Shadcn UI Chart, dan Midtrans Payment Gateway.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 11">
  <img src="https://img.shields.io/badge/Tailwind_CSS-3.4-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/Shadcn_UI-Charts-09090b?style=for-the-badge" alt="Shadcn UI">
  <img src="https://img.shields.io/badge/Laravel_Reverb-Real--time-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel Reverb">
  <img src="https://img.shields.io/badge/Midtrans-Snap_%26_QRIS-002855?style=for-the-badge" alt="Midtrans">
  <img src="https://img.shields.io/badge/PHPUnit-102_Passed-brightgreen?style=for-the-badge" alt="Tests">
  <img src="https://img.shields.io/badge/PHPStan-Level_5_Clean-blue?style=for-the-badge" alt="PHPStan">
  <img src="https://img.shields.io/badge/Pint-PSR--12_Clean-green?style=for-the-badge" alt="Pint">
</p>

---

## 📌 Tentang Proyek

**BJBO Sedagho** (Barang Jual Beli Online Sedagho) adalah platform *marketplace peer-to-peer* dan *official store* modern yang menghubungkan penjual lokal dan pembeli di seluruh wilayah Kabupaten Bengkalis (Bengkalis Kota, Mandau, Duri, Pinggir, Bukit Batu, Siak Kecil, Bandar Laksamana, Rupat, Rupat Utara, Bathin Solapan, Talang Muandau, dan Bantan). 

Dibangun dengan arsitektur modern berstandar enterprise, sistem ini memadukan kemudahan transaksi belanja, transparansi rekber (escrow), dompet digital lokal **BJPay**, struk invoice otomatis, serta dasbor analitik administrator berbasis **Shadcn UI**.

---

## ✨ Fitur Unggulan Sistem

### 🛒 1. Pengalaman Belanja & Antarmuka Modern (Tokopedia Style)
* **Navbar Terpadu & Berkelompok:** Navigasi pengguna bergaya Tokopedia yang rapi dengan pengelompokan ikon aksi ke dalam popover interaktif:
  * **Keranjang Belanja (*Cart*):** Akses cepat dengan lencana counter live real-time.
  * **Notifikasi Popover:** Tab Transaksi (ringkasan 4 status tahapan pesanan) dan Tab Update riwayat notifikasi sistem.
  * **Kotak Masuk Popover:** Penyatuan modul pesan obrolan tanya stok dan ulasan penilaian produk etalase.
  * **Menu Profil Rata Kanan:** Akses akun pengguna, ringkasan saldo BJPay, manajemen Rekber, Wishlist, dan Favorit.
* **Proporsi Logo Optimal:** Logo brand BJBO proporsional (80-85% tinggi navbar) yang dominan dan tajam.
* **Tata Letak Etalase Centered:** Grid produk berjarak lega dan terpusat di tengah layar (*centered layout*), bar filter kategori terpusat, serta tombol *"Muat Lebih Banyak"* yang mengarahkan ke katalog lengkap saat produk mencapai batas grid.
* **10 Niche Animation Hacks:** Mikro animasi berbasis CSS GPU-accelerated:
  1. *Scale from 0.97* pada modal dan dialog popover.
  2. *Press feedback* taktil seketika pada tombol aksi beli dan navigasi.
  3. *Shake on error* saat validasi form atau saldo kurang.
  4. *Directional slides* untuk notifikasi dan drawer.
  5. *Smooth height auto* pada akordeon dan rincian belanja.
  6. *GPU-accelerated transforms* untuk performa rendering 60/120 FPS.
  7. *Instant theme switch* tanpa kedipan warna transisi.
  8. *Tooltip skip delay* untuk kenyamanan navigasi deretan ikon.
  9. *Velocity springs* elastis pada hover kartu produk.
  10. *Elastic rubber banding* pada batas scroll etalase.

### 🛍️ 2. Keranjang Belanja (*Shopping Cart*)
* Menambahkan produk ke keranjang langsung dari kartu etalase maupun halaman detail produk.
* Pengaturan kuantitas barang, validasi ketersediaan stok, dan penghitungan subtotal otomatis.
* Alur checkout terpadu langsung dari daftar belanja keranjang.

### 🧾 3. Struk Digital & Invoice Resmi Otomatis
* Generator nomor seri invoice unik (`INV-YYYYMMDD-XXXXXX`) yang diterbitkan otomatis saat transaksi dibuat.
* Rincian lengkap harga barang, ongkos kirim, diskon promo/voucher, dan potongan khusus metode pembayaran.
* Tampilan struk digital siap cetak (*print-friendly receipt*) dengan stempel status pembayaran (*Lunas/Menunggu Pembayaran*).

### 💳 4. Metode Pembayaran Fleksibel (Midtrans, COD, & BJPay)
* **Gerbang Pembayaran Midtrans & QRIS:** Integrasi Midtrans Snap in-page dengan QRIS standar nasional dan Virtual Account bank.
* **COD (*Cash on Delivery*):** Opsi bayar tunai di tempat saat kurir menyerahkan barang kepada pembeli.
* **BJPay (Dompet Saldo Internal BJBO):**
  * Saldo digital terintegrasi untuk transaksi instan tanpa biaya admin pihak ketiga.
  * Potongan/diskon biaya pengiriman eksklusif sebesar Rp 5.000 untuk setiap pesanan yang menggunakan metode pembayaran BJPay.
  * Pengurangan saldo atomik dengan validasi kecukupan dana yang aman.

### 📊 5. Dasbor Analitik Kategori Terlaris Admin (Shadcn UI)
* **Dua Tipe Diagram Interaktif:** Toggle instan antara **Diagram Batang (*Bar Chart*)** dan **Diagram Lingkaran (*Donut Chart*)** berbasis SVG responsif tanpa reload halaman.
* **4 Filter Rentang Waktu Dinamis:**
  * **1 Minggu Terakhir (`1_minggu`)**: Tren pergerakan transaksi 7 hari terakhir.
  * **1 Bulan Terakhir (`1_bulan`)**: Penjualan bulanan 30 hari terakhir.
  * **1 Tahun Terakhir (`1_tahun`)**: Rekapitulasi tahunan 365 hari terakhir.
  * **Semua Waktu (`all_time`)**: Seluruh riwayat penjualan barang laku.
* **3 Kartu Ringkasan KPI:** Total Barang Laku, Total Omzet Transaksi (angka monospaced), dan Kategori Terlaris.
* **Tabel Rincian Kategori:** Indikator titik warna, jumlah unit terjual, bilah kemajuan (*progress bar*) kontribusi pasar, nominal omzet Rupiah, serta *floating tooltip* saat hover.
* **Kueri Teroptimasi & Caching:** Didukung service `CategoryAnalyticsService` dengan caching `Cache::remember` selama 60 detik.

### 🏪 6. Sistem Multi-Tier Toko & Verifikasi Penjual
* **Toko Perorangan (UMKM & Penjual Mandiri):** Pendaftaran dengan validasi Nomor Induk Kependudukan (NIK 16 digit) dan foto KTP.
* **Official Store (Brand Resmi & Korporasi):** Skema kurasi premium dengan verifikasi legalitas NIB, NPWP Perusahaan, serta Sertifikat HAKI resmi.
* **Proteksi Seller:** Mencegah penjualan tanpa toko aktif dan mengarahkan pengguna melengkapi data toko terlebih dahulu.
* **Dashboard Seller Center (`/user/profil`):** Statistik toko, pengelolaan etalase, dan penanda status barang terjual (*sold out*).

### 💬 7. Chat Real-time & Sensor Keamanan (Laravel Reverb)
* **Real-time Messaging:** Pertukaran pesan obrolan instan pembeli-penjual melalui WebSocket privat Reverb.
* **Sensor Privasi:** Sensor otomatis nomor telepon/WhatsApp pada obrolan untuk mencegah transaksi di luar platform yang rawan penipuan.
* **Pelaporan Otomatis:** Deteksi barang terlarang dan aduan komunitas yang langsung diteruskan ke dasbor admin.

### 🛡️ 8. Rekening Bersama (Rekber / Escrow) & Tarik Dana
* **Perlindungan Pembeli:** Dana transaksi ditahan aman dalam sistem escrow hingga pesanan diterima dengan baik oleh pembeli.
* **Pencairan Dana (Withdrawal):** Penjual dapat mendaftarkan nomor rekening bank dan mengajukan penarikan saldo dalam estimasi 2x24 jam kerja.

### 🤖 9. Mesin Rekomendasi Pintar (Recommendation Engine)
* Formula afinitas personal berbasis bobot wishlist ($W=5$), pencarian ($W=3$), dan riwayat tampilan ($W=2$).
* Filter kepemilikan otomatis agar barang milik penjual tidak muncul di feed belanjanya sendiri.
* Diimplementasikan 100% bebas perulangan prosedural menggunakan pipeline koleksi Laravel.

---

## 🛠️ Arsitektur & Teknologi

| Lapisan | Komponen | Versi / Teknologi |
| :--- | :--- | :--- |
| **Backend Framework** | Laravel Framework | 11.x (PHP 8.2+) |
| **Frontend Styling** | Tailwind CSS & Shadcn UI Tokens | Tailwind 3.4 & Vanilla JS Modern |
| **Asset Bundler** | Vite | 5.x |
| **WebSockets** | Laravel Reverb & Laravel Echo | Real-time Private Channels |
| **Payment Gateway** | Midtrans | Snap In-Page & QRIS Integration |
| **Database** | PostgreSQL / MySQL / SQLite | PostgreSQL (Production/Dev), SQLite (Testing) |
| **Testing Suite** | PHPUnit | 11.x (102 tests, 444 assertions, 100% Pass) |
| **Static Analysis** | Larastan / PHPStan | Level 5 (88 files, 0 errors) |
| **Code Formatter** | Laravel Pint | PSR-12 Standard Clean |

---

## 🚀 Panduan Instalasi & Menjalankan Proyek

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

Sesuaikan konfigurasi database, Reverb, dan kredensial Midtrans di file `.env`:
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

# Konfigurasi Laravel Reverb WebSockets
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
Jalankan perintah berikut pada terminal terpisah:

```bash
# Terminal 1: Server Web Utama Laravel
php artisan serve

# Terminal 2: Server WebSocket Real-time Reverb
php artisan reverb:start --debug

# Terminal 3: Worker Antrean Pesanan & Notifikasi (Opsional)
php artisan queue:listen
```

Akses aplikasi di peramban Anda melalui URL: `http://127.0.0.1:8000`.

---

## 🧪 Kualitas Kode & Pengujian Otomatis

Proyek BJBO Sedagho menerapkan standar rekayasa perangkat lunak ketat:

### 1. Eksekusi Test Suite (PHPUnit)
```bash
php vendor/phpunit/phpunit/phpunit
```
Hasil pengujian: **102 tests, 444 assertions, 100% lulus**:
- `AuthFeatureTest`: Autentikasi, registrasi, remember me, dan verifikasi OTP.
- `UserEcommerceFeatureTest`: Alur transaksi, wishlist, proteksi toko, dan toggle barang laku.
- `CartFeatureTest`: Penambahan barang ke keranjang, pembaruan kuantitas, subtotal, dan pembersihan keranjang.
- `InvoiceAndPaymentFeatureTest`: Generator struk invoice, diskon promo, pemotongan ongkir BJPay, dan COD.
- `SecurityAndChatFeatureTest`: Sensor nomor telepon, enkripsi obrolan, dan pelaporan barang terlarang.
- `AdminFeatureTest`: Dasbor analitik diagram Shadcn UI, moderasi toko, dan persetujuan pencairan rekber.
- `CategoryAnalyticsServiceTest`: Verifikasi agregasi penjualan per kategori untuk 4 rentang waktu dan caching.

### 2. Analisis Statis (PHPStan Level 5)
```bash
.\vendor\bin\phpstan analyse --memory-limit=1G
```
Hasil: **88/88 files analysed, 0 errors**.

### 3. Pengecekan Standar Gaya Kode (Laravel Pint)
```bash
.\vendor\bin\pint --test
```
Hasil: **166 files checked, 100% PSR-12 compliant**.

### 4. Standar Bebas Loop Konvensional (Zero-Loop Architecture)
Seluruh fungsi bisnis dan layanan (services) ditulis murni tanpa struktur perulangan `for`, `foreach`, `while`, dan `do-while`. Manipulasi data memanfaatkan method collection Laravel yang ekspresif, aman, dan modular.

---

## 📂 Struktur Direktori Utama

```text
BJBOweb/
├── app/
│   ├── Events/               # Event WebSockets (ChatMessageSent, dll.)
│   ├── Http/Controllers/     # Controller Admin dan Pengguna (Cart, BjPay, Admin, dll.)
│   ├── Models/               # Eloquent Models (User, Postingan, Pesanan, Cart, Invoice, dll.)
│   ├── Repositories/         # Pola abstraksi akses data terpusat
│   └── Services/             # Logika bisnis inti (Analytics, Invoice, Pesanan, Rekber, Chat)
├── database/
│   ├── migrations/           # Skema tabel database (termasuk carts, invoices, bjpay)
│   └── seeders/              # Data uji coba akun, produk, dan transaksi analitik
├── resources/
│   ├── css/
│   │   ├── app.css           # Konfigurasi Tailwind CSS
│   │   └── components/       # Modul animasi mikro (animations.css)
│   └── views/
│       ├── components/       # Komponen navbar Tokopedia-style, kartu produk, dll.
│       ├── layouts/          # Template tata letak admin dan publik
│       └── pages/            # Halaman admin (analitik Shadcn UI) & pengguna (cart, bjpay, invoice)
├── routes/
│   └── web.php               # Pendaftaran rute web aplikasi
└── tests/
    ├── Feature/              # Pengujian integrasi alur fitur lengkap
    └── Unit/                 # Pengujian unit layanan dan kalkulasi matematika
```

---

## 📄 Lisensi & Hak Cipta

Hak cipta &copy; 2024-2026 **BJBO Sedagho**. Dikelola oleh **PT Subaru Technologi Bangsa**.<br>
Seluruh hak cipta dilindungi undang-undang.
