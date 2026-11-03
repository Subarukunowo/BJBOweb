<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist & Rekomendasi Spesial Untuk Anda - BJBO Sedagho</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" crossorigin="anonymous">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bjbo-bg text-gray-900 font-sans min-h-screen flex flex-col">

@include('sweetalert::alert')
@include('components.navbar-user')

<main class="container mx-auto px-3 sm:px-4 my-6 flex-grow max-w-6xl">
    <!-- Header Wishlist -->
    <div class="flex items-center justify-between gap-4 mb-6 pb-4 border-b border-bjbo-border">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-pink-100 text-pink-600 flex items-center justify-center text-xl flex-shrink-0">
                <i class="bi bi-bookmark-heart-fill"></i>
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Wishlist Produk Impian</h1>
                <p class="text-xs sm:text-sm text-gray-500">Daftar produk yang ingin Anda beli kemudian hari di pasar BJBO</p>
            </div>
        </div>

        <a href="{{ route('user.dashboard') }}" class="bjbo-btn-outline text-xs py-2 px-3.5">
            <i class="bi bi-compass"></i> Jelajahi Pasar
        </a>
    </div>

    <!-- Section 1: Produk Wishlist -->
    <div class="mb-12">
        @if($wishlists->isEmpty())
            <div class="bg-white border border-bjbo-border rounded-2xl p-10 text-center shadow-sm">
                <div class="w-16 h-16 rounded-full bg-pink-50 text-pink-500 mx-auto flex items-center justify-center text-3xl mb-3">
                    <i class="bi bi-heartbreak"></i>
                </div>
                <h3 class="text-base font-bold text-gray-800 mb-1">Wishlist Anda Masih Kosong</h3>
                <p class="text-xs text-gray-500 max-w-md mx-auto mb-5">Simpan barang yang Anda sukai ke dalam wishlist untuk mempermudah pembelian dan membantu algoritma memberikan rekomendasi terbaik.</p>
                <a href="{{ route('user.dashboard') }}" class="bjbo-btn-primary text-xs py-2.5 px-6 font-bold shadow">
                    Mulai Belanja Sekarang
                </a>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
                @foreach($wishlists as $item)
                    <div class="bg-white border border-bjbo-border rounded-2xl overflow-hidden shadow-xs hover:shadow-md transition flex flex-col justify-between group">
                        <div class="relative">
                            <a href="{{ route('user.detail.postingan', $item->id) }}">
                                <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-full h-40 sm:h-48 object-cover group-hover:scale-105 transition duration-300">
                            </a>
                            <form action="{{ route('user.wishlist.toggle', $item->id) }}" method="POST" class="absolute top-2 right-2">
                                @csrf
                                <button type="submit" class="w-8 h-8 rounded-full bg-white/90 text-pink-600 hover:bg-white flex items-center justify-center text-sm shadow transition" title="Hapus dari wishlist">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            <span class="absolute bottom-2 left-2 bg-black/60 backdrop-blur-xs text-white text-[10px] font-semibold px-2 py-0.5 rounded-full">
                                {{ $item->category }}
                            </span>
                        </div>

                        <div class="p-3 sm:p-4 flex flex-col flex-grow justify-between">
                            <div>
                                <h3 class="text-xs sm:text-sm font-bold text-gray-900 line-clamp-2 mb-1 group-hover:text-bjbo-primary transition">
                                    <a href="{{ route('user.detail.postingan', $item->id) }}">{{ $item->name }}</a>
                                </h3>
                                <p class="text-xs sm:text-sm font-extrabold text-bjbo-primary mb-2">Rp{{ number_format($item->price, 0, ',', '.') }}</p>
                            </div>

                            <div class="pt-2 border-t border-bjbo-border flex items-center justify-between text-[11px] text-gray-500">
                                <span><i class="bi bi-geo-alt"></i> {{ Str::limit($item->lokasi, 15) }}</span>
                                <a href="{{ route('user.pesanan.checkout', $item->id) }}" class="text-xs font-bold text-bjbo-accent hover:underline">
                                    Beli &rsaquo;
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Section 2: Rekomendasi Spesial Untuk Anda (For You / Personalization Engine) -->
    <div class="bg-gradient-to-r from-blue-900 to-indigo-900 rounded-2xl p-6 sm:p-8 text-white shadow-md mb-10">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <div>
                <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-amber-300 bg-amber-400/20 px-2.5 py-0.5 rounded-full mb-1">
                    <i class="bi bi-stars"></i> Algoritma Personalisasi For You
                </span>
                <h2 class="text-lg sm:text-2xl font-extrabold text-white">Rekomendasi Spesial Untuk Anda</h2>
                <p class="text-xs sm:text-sm text-blue-200 mt-1 max-w-xl">
                    Dipersonalisasi cerdas berdasarkan riwayat pencarian kata kunci (bobot 3), produk yang pernah Anda lihat (bobot 2), dan daftar wishlist/favorit (bobot 5).
                </p>
            </div>
        </div>

        @if($recommendations->isEmpty())
            <p class="text-xs text-blue-200">Belum ada rekomendasi yang cukup. Jelajahi produk lain untuk mendapatkan rekomendasi terbaik.</p>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                @foreach($recommendations as $rec)
                    <div class="bg-white rounded-xl overflow-hidden shadow text-gray-900 flex flex-col justify-between group hover:shadow-lg transition">
                        <div class="relative">
                            <a href="{{ route('user.detail.postingan', $rec->id) }}">
                                <img src="{{ $rec->image_url }}" alt="{{ $rec->name }}" class="w-full h-32 sm:h-36 object-cover group-hover:scale-105 transition duration-300">
                            </a>
                            <span class="absolute top-2 left-2 bg-amber-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded">
                                For You
                            </span>
                        </div>
                        <div class="p-3 flex flex-col flex-grow justify-between">
                            <div>
                                <h4 class="text-xs font-bold text-gray-900 line-clamp-1 mb-1 group-hover:text-bjbo-primary transition">
                                    <a href="{{ route('user.detail.postingan', $rec->id) }}">{{ $rec->name }}</a>
                                </h4>
                                <p class="text-xs font-extrabold text-bjbo-primary">Rp{{ number_format($rec->price, 0, ',', '.') }}</p>
                            </div>
                            <div class="pt-2 mt-2 border-t border-gray-100 flex items-center justify-between text-[11px]">
                                <span class="text-gray-400">{{ Str::limit($rec->category, 12) }}</span>
                                <a href="{{ route('user.detail.postingan', $rec->id) }}" class="text-bjbo-primary font-bold hover:underline">
                                    Lihat &rsaquo;
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</main>

@include('components.footer')
</body>
</html>
