<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BJBO - Beranda Pengguna</title>
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Vite + Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bjbo-bg text-gray-900 font-sans min-h-screen flex flex-col">

@include('sweetalert::alert')

<!-- Komponen Navbar Pengguna -->
@include('components.navbar-user')

<!-- Konten Utama -->
<main class="max-w-7xl mx-auto px-3 sm:px-4 my-4 sm:my-6 flex-grow pb-20 lg:pb-0 w-full">
    <!-- Banner Pengguna Tokopedia Style -->
    <div class="bg-gradient-to-br from-bjbo-primary to-blue-950 text-white rounded-2xl p-4 sm:p-7 mb-6 shadow-sm flex flex-wrap items-center justify-between gap-4">
        <div>
            <span class="inline-block bg-white/15 text-white border border-white/20 text-[11px] sm:text-xs font-mono font-bold px-2.5 sm:px-3 py-0.5 rounded-full mb-2">
                Pasar Daring Sedagho Bengkalis
            </span>
            <h2 class="text-lg sm:text-2xl font-extrabold mb-1">Halo, {{ $user->name }}!</h2>
            <p class="text-gray-200 text-xs sm:text-sm max-w-xl">Belanja barang kebutuhan dari tetangga atau mulai buka etalase jualan Anda sendiri hari ini.</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            @if($user->hasActiveStore())
                <a href="{{ route('user.profil') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold bg-white text-bjbo-primary hover:bg-gray-100 transition shadow-sm flex-1 sm:flex-initial anim-press">
                    <i class="bi bi-shop text-base"></i> <span>Kelola Toko Saya</span>
                </a>
            @else
                <a href="{{ route('user.toko.daftar') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold bg-white text-bjbo-primary hover:bg-gray-100 transition shadow-sm flex-1 sm:flex-initial anim-press">
                    <i class="bi bi-shop-window text-base"></i> <span>Buka Toko Sekarang</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Filter Kategori Centered (Sesuai Gambar 2) -->
    <div class="mb-6 flex flex-col items-center justify-center">
        @include('components.category-bar', ['activeCategory' => null, 'baseRoute' => 'user.dashboard'])
    </div>

    <!-- Grid Produk Terverifikasi (6 Kolom Horizontal Centered Sesuai Gambar 2) -->
    <div class="mb-10">
        <div class="flex items-center justify-between mb-4">
            <h4 class="font-bold text-zinc-900 text-base sm:text-lg mb-0 flex items-center gap-2">
                <i class="bi bi-fire text-amber-500"></i> Rekomendasi Pilihan Untuk Anda
            </h4>
            <span class="text-zinc-500 text-xs font-medium">Total: {{ $postingans->count() }} barang</span>
        </div>

        @if($postingans->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 sm:gap-4 md:gap-4.5">
                @foreach ($postingans as $item)
                    @include('components.product-card', ['item' => $item, 'isAuth' => true])
                @endforeach
            </div>

            <!-- Tombol Muat Lebih Banyak (Redirect ke Semua Barang) -->
            <div class="mt-8 mb-4 flex justify-center items-center">
                <a href="{{ route('postingan.cari') }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full text-xs sm:text-sm font-semibold border border-zinc-200 bg-white hover:bg-zinc-50 text-zinc-800 shadow-xs hover:border-zinc-300 transition duration-150 anim-press group" title="Lihat semua barang di BJBO">
                    <span>Muat Lebih Banyak</span>
                    <svg class="w-4 h-4 text-zinc-500 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>
        @else
            <div class="text-center py-12 bg-white rounded-xl border border-bjbo-border">
                <i class="bi bi-inbox text-4xl text-gray-400 mb-3 block"></i>
                <h5 class="font-bold text-gray-800">Belum ada barang tersedia saat ini</h5>
                <p class="text-gray-500 text-xs mb-4">Kelola etalase dan tawarkan produk di Toko Anda.</p>
                @if($user->hasActiveStore())
                    <a href="{{ route('user.profil') }}" class="bjbo-btn-primary">
                        <i class="bi bi-shop mr-1"></i> Buka Kelola Toko
                    </a>
                @else
                    <a href="{{ route('user.toko.daftar') }}" class="bjbo-btn-primary">
                        <i class="bi bi-shop-window mr-1"></i> Buka Toko Sekarang
                    </a>
                @endif
            </div>
        @endif
    </div>
</main>

<!-- Komponen Modal Laporan Dinamis -->
@include('components.modal-laporan')

<!-- Komponen Mobile Bottom Nav (Khusus Handphone & Tablet Kecil) -->
@include('components.mobile-bottom-nav')

<!-- Komponen Footer -->
@include('components.footer')

</body>
</html>
