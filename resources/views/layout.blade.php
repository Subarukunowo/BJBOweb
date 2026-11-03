<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BJBO - Belanjo & Jual Barang Online Sedagho</title>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Vite + Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bjbo-bg text-gray-900 font-sans min-h-screen flex flex-col">
@include('sweetalert::alert')

<!-- Komponen Navbar Tamu -->
@include('components.navbar-guest')

<!-- Konten Utama -->
<main class="container mx-auto px-3 sm:px-4 my-4 sm:my-6 flex-grow pb-20 lg:pb-0">
    <!-- Komponen Hero Banner -->
    @include('components.hero-banner')

    <!-- Kategori Bar -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <h5 class="font-bold text-gray-900 text-sm sm:text-base mb-0">Kategori Pilihan</h5>
            <span class="text-gray-500 text-[11px] sm:text-xs">Geser kategori untuk memfilter</span>
        </div>
        @include('components.category-bar', ['activeCategory' => null, 'baseRoute' => 'layout'])
    </div>

    <!-- Etalase Produk Terverifikasi (Loop 1) -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h4 class="font-bold text-gray-900 text-base sm:text-lg mb-0">Produk Terbaru</h4>
            <span class="text-gray-500 text-[11px] sm:text-xs">Barang terverifikasi kurator</span>
        </div>

        @if(isset($postingans) && $postingans->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-2.5 sm:gap-4">
                @foreach ($postingans as $item)
                    @include('components.product-card', ['item' => $item, 'isAuth' => false])
                @endforeach
            </div>
        @else
            <div class="text-center py-12 bg-white rounded-xl border border-bjbo-border">
                <i class="bi bi-inbox text-4xl text-gray-400 mb-3 block"></i>
                <h5 class="font-bold text-gray-800">Belum ada postingan produk</h5>
                <p class="text-gray-500 text-xs mb-4">Jadilah yang pertama menjual barang Anda di BJBO Sedagho.</p>
                <a href="{{ route('login') }}" class="bjbo-btn-accent">
                    Pasang Iklan Sekarang
                </a>
            </div>
        @endif
    </div>
</main>

<!-- Komponen Mobile Bottom Nav (Khusus Handphone & Tablet Kecil) -->
@include('components.mobile-bottom-nav')

<!-- Komponen Footer -->
@include('components.footer')

</body>
</html>
