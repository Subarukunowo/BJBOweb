# BJBO Sedagho: Pasar Digital Komunitas Kabupaten Bengkalis

<p align="center">
  <img src="public/images/bjbo.png" alt="BJBO Sedagho Logo" width="140" style="border-radius: 16px;">
</p>

<p align="center">
  <strong>Aplikasi Jual Beli Komunitas Kabupaten Bengkalis</strong><br>
  Dibangun menggunakan Laravel 11, Tailwind CSS, Laravel Reverb, Shadcn UI Chart, dan Midtrans Payment Gateway.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 11">
  <img src="https://img.shields.io/badge/Tailwind_CSS-3.4-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/Shadcn_UI-Charts-09090b?style=for-the-badge" alt="Shadcn UI">
  <img src="https://img.shields.io/badge/Laravel_Reverb-Real--time-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel Reverb">
  <img src="https://img.shields.io/badge/Midtrans-Snap_%26_QRIS-002855?style=for-the-badge" alt="Midtrans">
  <img src="https://img.shields.io/badge/PHPUnit-Tests-brightgreen?style=for-the-badge" alt="PHPUnit">
  <img src="https://img.shields.io/badge/PHPStan-Level_5-blue?style=for-the-badge" alt="PHPStan">
  <img src="https://img.shields.io/badge/Pint-PSR--12-green?style=for-the-badge" alt="Pint">
</p>

---

## 📌 Tentang Proyek

**BJBO Sedagho** (Barang Jual Beli Online Sedagho) adalah aplikasi jual beli berbasis web untuk masyarakat di wilayah Kabupaten Bengkalis (Bengkalis Kota, Mandau, Duri, Pinggir, Bukit Batu, Siak Kecil, Bandar Laksamana, Rupat, Rupat Utara, Bathin Solapan, Talang Muandau, dan Bantan).

Aplikasi ini memfasilitasi transaksi jual beli lokal dengan menyediakan fitur etalase produk, keranjang belanja, obrolan langsung antara penjual dan pembeli, sistem rekening bersama (rekber), struk invoice digital, saldo BJPay, serta dasbor analitik admin.

---

## ✨ Fitur Aplikasi

### 🛒 1. Antarmuka dan Katalog Produk
* **Navigasi Terpadu:** Pengelompokan menu navigasi menggunakan popover interaktif:
  * **Keranjang Belanja (*Cart*):** Akses cepat dengan indikator jumlah barang di keranjang.
  * **Notifikasi Popover:** Tab Transaksi (tahapan pesanan) dan Tab Update riwayat notifikasi.
  * **Kotak Masuk Popover:** Tab pesan chat dan tab ulasan produk.
  * **Menu Profil Rata Kanan:** Akses informasi akun, saldo BJPay, Rekber, Wishlist, dan Favorit.
* **Proporsi Logo Optimal:** Logo brand BJBO proporsional (80-85% tinggi navbar) agar terlihat jelas dan rapi.
* **Tata Letak Etalase Centered:** Grid produk terpusat (*centered layout*), bar filter kategori terpusat, serta tombol *"Muat Lebih Banyak"* yang mengarahkan ke katalog saat produk mencapai batas tampilan.
* **Interaksi & Animasi UI:** Mikro animasi berbasis CSS untuk interaksi yang responsif:
  1. *Scale from 0.97* pada modal dan popover.
  2. *Press feedback* saat tombol ditekan.
  3. *Shake on error* saat input form tidak sesuai.
  4. *Directional slides* pada notifikasi.
  5. *Smooth height auto* pada rincian belanja.
  6. *GPU-accelerated transforms* untuk kelancaran rendering.
  7. *Instant theme switch* pada mode tampilan.
  8. *Tooltip skip delay* pada deretan ikon navigasi.
  9. *Velocity springs* pada hover kartu produk.
  10. *Elastic rubber banding* pada scroll etalase.

### 🛍️ 2. Keranjang Belanja (*Shopping Cart*)
* Menambahkan produk ke keranjang dari kartu etalase maupun halaman rincian produk.
* Pengaturan kuantitas barang, validasi stok, dan kalkulasi subtotal belanja.
* Alur checkout pesanan dari daftar keranjang.

### 🧾 3. Struk Digital & Invoice Otomatis
* Pembuatan nomor invoice otomatis (`INV-YYYYMMDD-XXXXXX`) saat transaksi dibuat.
* Rincian harga barang, ongkos kirim, diskon promo, dan potongan biaya pengiriman.
* Tampilan struk digital siap cetak (*print-friendly*) dengan status pembayaran (*Lunas/Menunggu Pembayaran*).

### 💳 4. Metode Pembayaran (Midtrans, COD, & BJPay)
* **Gerbang Pembayaran Midtrans & QRIS:** Pembayaran via Midtrans Snap, QRIS, dan Virtual Account bank.
* **COD (*Cash on Delivery*):** Pilihan pembayaran tunai saat barang diterima dari kurir.
* **BJPay (Saldo Internal BJBO):**
  * Saldo internal pengguna untuk transaksi langsung.
  * Potongan ongkos kirim sebesar Rp 5.000 untuk transaksi yang menggunakan saldo BJPay.
  * Pemotongan saldo otomatis dengan pengecekan kecukupan saldo.

### 📊 5. Dasbor Analitik Kategori Terlaris Admin (Shadcn UI)
* **Dua Tipe Diagram Interaktif:** Pilihan tampilan **Diagram Batang (*Bar Chart*)** dan **Diagram Lingkaran (*Donut Chart*)** berbasis SVG tanpa reload halaman.
* **4 Filter Rentang Waktu:**
  * **1 Minggu Terakhir (`1_minggu`)**: Data penjualan 7 hari terakhir.
  * **1 Bulan Terakhir (`1_bulan`)**: Data penjualan 30 hari terakhir.
  * **1 Tahun Terakhir (`1_tahun`)**: Data penjualan 365 hari terakhir.
  * **Semua Waktu (`all_time`)**: Seluruh riwayat penjualan barang laku.
* **3 Kartu Ringkasan:** Total Barang Laku, Total Omzet, dan Kategori Terlaris.
* **Rincian Kategori:** Titik warna kategori, jumlah unit terjual, bilah kemajuan persentase (*progress bar*), total omzet Rupiah, serta *tooltip* saat kursor diarahkan ke grafik.
* **Caching Data:** Didukung service `CategoryAnalyticsService` dengan caching selama 60 detik.

### 🏪 6. Pendaftaran Toko & Verifikasi Penjual
* **Toko Perorangan:** Pendaftaran toko dengan verifikasi NIK dan foto KTP.
* **Official Store:** Pendaftaran toko dengan verifikasi dokumen legalitas (NIB, NPWP, atau HAKI).
* **Proteksi Seller:** Pengguna wajib memiliki toko yang disetujui untuk dapat mengunggah barang jualan.
* **Dashboard Seller Center (`/user/profil`):** Statistik produk, pengelolaan etalase, dan penanda status barang terjual (*sold out*).

### 💬 7. Chat Real-time & Sensor Keamanan (Laravel Reverb)
* **Pesan Real-time:** Pertukaran pesan langsung antara penjual dan pembeli menggunakan WebSocket Laravel Reverb.
* **Sensor Nomor Kontak:** Sensor otomatis terhadap nomor telepon/kontak luar pada pesan obrolan.
* **Laporan Pelanggaran:** Fitur pelaporan barang atau pesan yang dapat ditinjau oleh administrator.

### 🛡️ 8. Rekening Bersama (Rekber) & Penarikan Dana
* **Perlindungan Pembeli:** Dana transaksi ditahan sementara di sistem rekber hingga barang diterima pembeli.
* **Pencairan Dana:** Penjual dapat mendaftarkan nomor rekening bank untuk penarikan saldo penjualan.

### 🤖 9. Rekomendasi Produk
* Rekomendasi produk berdasarkan kata kunci pencarian dan interaksi produk.
* Filter otomatis agar barang milik penjual tidak muncul pada daftar rekomendasi miliknya sendiri.
* Pengolahan data fungsional menggunakan collection Laravel.

---

## 🛠️ Arsitektur & Teknologi

| Komponen | Versi / Teknologi |
| :--- | :--- |
| **Backend Framework** | Laravel 11.x (PHP 8.2+) |
| **Frontend Styling** | Tailwind CSS 3.4 & Shadcn UI Tokens |
| **Asset Bundler** | Vite 5.x |
| **WebSockets** | Laravel Reverb & Echo |
| **Payment Gateway** | Midtrans (Snap & QRIS) |
| **Database** | PostgreSQL / MySQL / SQLite |
| **Testing Suite** | PHPUnit 11.x (Unit & Feature Tests) |
| **Static Analysis** | Larastan / PHPStan (Level 5) |
| **Code Formatter** | Laravel Pint (PSR-12) |

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

## 🧪 Pengujian Otomatis & Kualitas Kode

### 1. Eksekusi Test Suite (PHPUnit)
```bash
php vendor/phpunit/phpunit/phpunit
```
Pengujian mencakup unit test dan feature test untuk alur fitur utama:
- `AuthFeatureTest`: Autentikasi akun, login, dan OTP.
- `UserEcommerceFeatureTest`: Alur transaksi, wishlist, dan penanda barang laku.
- `CartFeatureTest`: Keranjang belanja, perubahan kuantitas, dan subtotal.
- `InvoiceAndPaymentFeatureTest`: Struk invoice, diskon ongkir BJPay, dan COD.
- `SecurityAndChatFeatureTest`: Sensor kontak chat dan pelaporan barang.
- `AdminFeatureTest`: Analitik admin, verifikasi toko, dan pencairan dana rekber.
- `CategoryAnalyticsServiceTest`: Agregasi penjualan kategori produk dan caching.

### 2. Analisis Statis (PHPStan Level 5)
```bash
.\vendor\bin\phpstan analyse --memory-limit=1G
```

### 3. Pengecekan Standar Gaya Kode (Laravel Pint)
```bash
.\vendor\bin\pint --test
```

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
│       ├── components/       # Komponen navbar terpadu, kartu produk, dll.
│       ├── layouts/          # Template tata letak admin dan publik
│       └── pages/            # Halaman admin (analitik) & pengguna (cart, bjpay, invoice)
├── routes/
│   └── web.php               # Pendaftaran rute web aplikasi
└── tests/
    ├── Feature/              # Pengujian integrasi alur fitur lengkap
    └── Unit/                 # Pengujian unit layanan dan kalkulasi matematika
```

---

## 📄 Hak Cipta

&copy; **BJBO Sedagho**. Dikelola oleh **PT Subaru Technologi Bangsa**.<br>
Seluruh hak cipta dilindungi undang-undang.
