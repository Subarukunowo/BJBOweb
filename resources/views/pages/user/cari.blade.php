<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pencarian: "{{ $keyword }}" - BJBO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bjbo-bg text-gray-900 font-sans min-h-screen flex flex-col">

@include('sweetalert::alert')

<!-- Header Navigasi -->
@if(Auth::check())
    @include('components.navbar-user')
@else
    @include('components.navbar-guest')
@endif

<main class="container mx-auto px-3 sm:px-4 my-4 sm:my-6 flex-grow pb-20 lg:pb-0">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-lg sm:text-2xl font-extrabold text-gray-900 mb-1">
                Hasil Pencarian: <span class="text-bjbo-primary">"{{ $keyword }}"</span>
            </h1>
            <span class="text-gray-500 text-xs">Ditemukan {{ $postingan->count() }} barang</span>
        </div>
        <a href="{{ Auth::check() ? route('user.dashboard') : route('layout') }}" class="bjbo-btn-outline text-xs">
            <i class="bi bi-arrow-left"></i> Kembali ke Beranda
        </a>
    </div>

    <!-- Grid Hasil Pencarian (Loop 1) -->
    @if($postingan->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-2.5 sm:gap-4">
            @foreach ($postingan as $item)
                @include('components.product-card', ['item' => $item, 'isAuth' => Auth::check()])
            @endforeach
        </div>
    @else
        <div class="text-center py-12 bg-white rounded-xl border border-bjbo-border max-w-lg mx-auto p-4 sm:p-6">
            <i class="bi bi-search text-4xl text-gray-400 mb-3 block"></i>
            <h5 class="font-bold text-gray-800 text-base">Tidak ditemukan barang dengan kata kunci "{{ $keyword }}"</h5>
            <p class="text-gray-500 text-xs mt-1 mb-4">Coba gunakan kata kunci yang lebih umum atau periksa penulisan kata.</p>
            <a href="{{ Auth::check() ? route('user.dashboard') : route('layout') }}" class="bjbo-btn-primary text-xs py-2 px-4">
                Kembali ke Beranda
            </a>
        </div>
    @endif
</main>

<!-- Komponen Mobile Bottom Nav (Khusus Handphone & Tablet Kecil) -->
@include('components.mobile-bottom-nav')

@include('components.footer')

</body>
</html>
