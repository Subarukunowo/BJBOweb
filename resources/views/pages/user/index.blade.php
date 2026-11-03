<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BJBO - Beranda Pengguna</title>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Vite + Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bjbo-bg text-gray-900 font-sans min-h-screen flex flex-col">

@include('sweetalert::alert')

<!-- Komponen Navbar Pengguna -->
@include('components.navbar-user')

<!-- Konten Utama -->
<main class="container mx-auto px-3 sm:px-4 my-4 sm:my-6 flex-grow pb-20 lg:pb-0">
    <!-- Banner Pengguna Tokopedia Style -->
    <div class="bg-gradient-to-br from-bjbo-primary to-blue-950 text-white rounded-2xl p-4 sm:p-7 mb-6 shadow-sm flex flex-wrap items-center justify-between gap-4">
        <div>
            <span class="inline-block bg-white/15 text-white border border-white/20 text-[11px] sm:text-xs font-mono font-bold px-2.5 sm:px-3 py-0.5 rounded-full mb-2">
                Pasar Daring Sedagho Bengkalis
            </span>
            <h2 class="text-lg sm:text-2xl font-extrabold mb-1">Halo, {{ $user->name }}!</h2>
            <p class="text-gray-200 text-xs sm:text-sm max-w-xl">Belanja barang kebutuhan dari tetangga atau mulai buka etalase jualan Anda sendiri hari ini.</p>
        </div>
        @if($user->hasActiveStore())
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <a href="{{ route('user.profil') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold bg-white text-bjbo-primary hover:bg-gray-100 transition shadow-sm flex-1 sm:flex-initial">
                    <i class="bi bi-shop text-base"></i> <span>Kelola Toko Saya</span>
                </a>
            </div>
        @endif
    </div>

    <!-- Filter Kategori -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <h5 class="font-bold text-gray-900 text-sm sm:text-base mb-0">Kategori Barang</h5>
            <span class="text-gray-500 text-[11px] sm:text-xs">Geser kategori untuk memfilter</span>
        </div>
        @include('components.category-bar', ['activeCategory' => null, 'baseRoute' => 'user.dashboard'])
    </div>

    <!-- Grid Produk Terverifikasi (Loop 1) -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h4 class="font-bold text-gray-900 text-base sm:text-lg mb-0">Rekomendasi Untuk Anda</h4>
            <span class="text-gray-500 text-[11px] sm:text-xs">Total: {{ $postingans->count() }} barang</span>
        </div>

        @if($postingans->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-2.5 sm:gap-4">
                @foreach ($postingans as $item)
                    @include('components.product-card', ['item' => $item, 'isAuth' => true])
                @endforeach
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
