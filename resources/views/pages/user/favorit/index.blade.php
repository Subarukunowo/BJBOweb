<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barang Favorit Saya - BJBO Sedagho</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" integrity="sha384-QuGBSgV5Im3DzL2z+8Ko9/hqNy/N0O7zwvXAtfd1MvPKWa/UbeLV65cfm4BV5Wgq" crossorigin="anonymous">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bjbo-bg text-gray-900 font-sans min-h-screen flex flex-col">

@include('sweetalert::alert')

<!-- Header Navigasi -->
@include('components.navbar-user')

<main class="max-w-7xl mx-auto px-3 sm:px-4 my-4 sm:my-6 flex-grow pb-20 lg:pb-0 w-full">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-lg sm:text-2xl font-extrabold text-zinc-900 mb-1 flex items-center gap-2">
                <i class="bi bi-heart-fill text-red-500"></i> Barang Favorit Saya
            </h1>
            <span class="text-zinc-500 text-xs font-medium">Daftar barang yang Anda tandai untuk dipantau (Total: {{ $favorits->count() }})</span>
        </div>
        <a href="{{ route('user.dashboard') }}" class="bjbo-btn-outline text-xs">
            <i class="bi bi-arrow-left"></i> Kembali ke Beranda
        </a>
    </div>

    <!-- Grid Barang Favorit (6 Kolom Horizontal Centered Sesuai Gambar 2) -->
    @if($favorits->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 sm:gap-4 md:gap-4.5 mb-10">
            @foreach ($favorits as $item)
                @include('components.product-card', ['item' => $item, 'isAuth' => true])
            @endforeach
        </div>
    @else
        <div class="text-center py-12 sm:py-16 bg-white rounded-2xl border border-bjbo-border max-w-md mx-auto p-4 sm:p-6 shadow-sm">
            <div class="w-14 h-14 sm:w-16 sm:h-16 mx-auto mb-3 rounded-full bg-red-50 text-red-400 flex items-center justify-center text-2xl sm:text-3xl">
                <i class="bi bi-heart"></i>
            </div>
            <h5 class="font-bold text-gray-800 text-sm sm:text-base mb-1">Belum ada barang favorit</h5>
            <p class="text-gray-500 text-xs mb-5 leading-relaxed">
                Tekan tombol ikon hati pada barang yang Anda sukai di beranda untuk menyimpannya di halaman ini.
            </p>
            <a href="{{ route('user.dashboard') }}" class="bjbo-btn-primary text-xs py-2 px-4">
                Jelajahi Produk Sekarang
            </a>
        </div>
    @endif
</main>

<!-- Komponen Mobile Bottom Nav (Khusus Handphone & Tablet Kecil) -->
@include('components.mobile-bottom-nav')

@include('components.footer')

</body>
</html>
