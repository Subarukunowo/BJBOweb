<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $postingan->name }} - E-Commerce BJBO Sedagho</title>
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

<main class="container mx-auto px-3 sm:px-4 my-4 sm:my-6 flex-grow max-w-7xl pb-24 lg:pb-8">
    <!-- Breadcrumb Tokopedia Style -->
    <nav class="flex items-center gap-2 text-xs text-gray-500 mb-4 sm:mb-5 overflow-x-auto whitespace-nowrap scrollbar-none">
        <a href="{{ Auth::check() ? route('user.dashboard') : route('layout') }}" class="hover:text-bjbo-primary transition">Beranda</a>
        <i class="bi bi-chevron-right text-[10px]"></i>
        <a href="{{ route('postingan.kategori', $postingan->category) }}" class="hover:text-bjbo-primary transition">{{ $postingan->category }}</a>
        <i class="bi bi-chevron-right text-[10px]"></i>
        <span class="text-gray-800 font-semibold truncate max-w-xs">{{ $postingan->name }}</span>
    </nav>

    <!-- Layout Multi-Device: Mobile (1-kolom), Tablet (2-kolom), Laptop/Desktop (3-kolom) -->
    <div class="grid grid-cols-1 md:grid-cols-12 lg:grid-cols-12 gap-5 lg:gap-6 items-start">
        <!-- Kolom Kiri: Galeri Foto Produk & Jaminan (md:col-span-5 lg:col-span-4) -->
        <div class="md:col-span-5 lg:col-span-4 space-y-4 md:sticky md:top-20">
            <div class="bg-white border border-bjbo-border rounded-2xl p-3 shadow-xs">
                <div class="relative w-full aspect-square bg-gray-100 rounded-xl overflow-hidden group">
                    <div class="absolute top-3 left-3 z-10 flex flex-col gap-1.5">
                        <span class="bg-black/75 backdrop-blur-xs text-white text-xs font-bold px-2.5 py-1 rounded">
                            {{ $postingan->category }}
                        </span>
                        <span class="bg-white/90 backdrop-blur-xs text-bjbo-primary text-xs font-bold px-2 py-0.5 rounded border border-blue-200">
                            {{ $postingan->kondisi ?? 'Bekas (Pre-loved)' }}
                        </span>
                    </div>

                    @if($postingan->is_sold)
                        <div class="absolute inset-0 z-10 bg-black/60 flex items-center justify-center">
                            <span class="text-white text-sm font-extrabold tracking-wider bg-black/80 px-4 py-1.5 rounded-full border border-white/20">
                                SUDAH TERJUAL
                            </span>
                        </div>
                    @endif

                    <img src="{{ $postingan->image_url }}" alt="{{ $postingan->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-200 {{ $postingan->is_sold ? 'grayscale' : '' }}">
                </div>
            </div>

            <!-- Jaminan Transaksi Aman Sedagho -->
            <div class="bg-blue-50/60 border border-blue-100 rounded-2xl p-4 text-xs space-y-2">
                <div class="flex items-center gap-2 font-bold text-bjbo-primary">
                    <i class="bi bi-shield-check text-base"></i>
                    <span>Jaminan Transaksi Aman Sedagho</span>
                </div>
                <p class="text-gray-600 leading-relaxed text-[11px]">
                    Didukung pembayaran QRIS otomatis dan verifikasi komunitas lokal untuk menghindari penipuan barang.
                </p>
                <div class="pt-2 border-t border-blue-100 flex items-center justify-between text-[11px] text-gray-500 font-semibold">
                    <span><i class="bi bi-geo-alt"></i> Area Kab. Bengkalis</span>
                    <span><i class="bi bi-eye"></i> Dilihat {{ $postingan->views_count }}x</span>
                </div>
            </div>
        </div>

        <!-- Kolom Tengah: Info Lengkap, Spesifikasi & Ulasan (md:col-span-7 lg:col-span-5) -->
        <div class="md:col-span-7 lg:col-span-5 space-y-6">
            <!-- Header Informasi Produk -->
            <div class="bg-white border border-bjbo-border rounded-2xl p-4 sm:p-6 shadow-xs">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="text-xs font-semibold text-bjbo-primary bg-blue-50 px-2.5 py-0.5 rounded border border-blue-200">
                        {{ $postingan->category }}
                    </span>
                    <span class="text-xs font-semibold text-gray-600 bg-gray-100 px-2 py-0.5 rounded border border-gray-200">
                        {{ $postingan->kondisi ?? 'Bekas (Pre-loved)' }}
                    </span>
                </div>

                <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 leading-snug mb-3">
                    {{ $postingan->name }}
                </h1>

                <!-- Metrik Tokopedia Style -->
                <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500 pb-4 border-b border-bjbo-border">
                    @if($postingan->ulasan->count() > 0 && $postingan->rating_score)
                        <span class="flex items-center gap-1 font-extrabold text-amber-600">
                            <i class="bi bi-star-fill text-amber-400"></i> {{ $postingan->rating_score }}
                        </span>
                        <span>&bull;</span>
                        <span>{{ $postingan->ulasan->count() }} Ulasan Pembeli</span>
                    @else
                        <span class="flex items-center gap-1 text-gray-400 font-medium">
                            <i class="bi bi-star text-gray-300"></i> Belum ada ulasan
                        </span>
                    @endif
                    <span>&bull;</span>
                    <span class="text-green-700 font-bold"><i class="bi bi-check-circle"></i> Terverifikasi</span>
                </div>

                <!-- Harga Utama & Tipe Penawaran -->
                <div class="py-4">
                    <div class="text-xs text-gray-400 font-medium mb-1">Harga Barang</div>
                    <div class="flex items-baseline gap-3">
                        <span class="bjbo-price text-3xl font-extrabold text-bjbo-accent">
                            {{ $postingan->formatted_price }}
                        </span>
                        <span class="text-xs font-bold text-bjbo-primary bg-blue-50 border border-blue-200 px-2 py-0.5 rounded">
                            {{ $postingan->tipe_penawaran ?? 'Bisa Nego Sedagho' }}
                        </span>
                    </div>
                </div>

                <!-- Spesifikasi Grid Tokopedia Style -->
                <div class="pt-4 border-t border-bjbo-border">
                    <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider mb-3">Spesifikasi Produk</h3>
                    <div class="grid grid-cols-2 gap-y-2.5 text-xs">
                        <div class="text-gray-500">Kondisi Barang</div>
                        <div class="font-bold text-gray-800">{{ $postingan->kondisi ?? 'Bekas (Pre-loved)' }}</div>

                        <div class="text-gray-500">Kategori</div>
                        <div class="font-bold text-bjbo-primary">{{ $postingan->category }}</div>

                        <div class="text-gray-500">Lokasi Domisili</div>
                        <div class="font-bold text-gray-800"><i class="bi bi-geo-alt text-gray-400"></i> {{ $postingan->lokasi }}</div>

                        <div class="text-gray-500">Sistem Transaksi</div>
                        <div class="font-bold text-gray-800">COD / QRIS / Ambil Sendiri</div>

                        <div class="text-gray-500">Waktu Tayang</div>
                        <div class="font-bold text-gray-800">{{ $postingan->created_at->format('d M Y') }}</div>
                    </div>
                </div>
            </div>

            <!-- Tab Deskripsi Produk -->
            <div class="bg-white border border-bjbo-border rounded-2xl p-6 shadow-xs">
                <h3 class="text-sm font-bold text-gray-900 pb-3 border-b border-bjbo-border mb-3">Deskripsi Lengkap</h3>
                <div class="text-gray-700 text-xs sm:text-sm leading-relaxed whitespace-pre-line font-sans">
                    {{ $postingan->description }}
                </div>
            </div>

            <!-- Bagian Ulasan Pembeli (Loop 1) -->
            <div class="bg-white border border-bjbo-border rounded-2xl p-6 shadow-xs">
                <div class="flex items-center justify-between pb-3 border-b border-bjbo-border mb-4">
                    <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                        <i class="bi bi-star-fill text-amber-400"></i>
                        <span>Ulasan & Penilaian Pembeli ({{ $postingan->ulasan->count() }})</span>
                    </h3>
                    @if($postingan->ulasan->count() > 0 && $postingan->rating_score)
                        <span class="text-xs font-bold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-full border border-amber-200 flex items-center gap-1">
                            <i class="bi bi-star-fill text-amber-400"></i>
                            <span>{{ $postingan->rating_score }} / 5.0</span>
                        </span>
                    @else
                        <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full border border-gray-200">
                            Belum ada penilaian
                        </span>
                    @endif
                </div>

                <div id="ulasanSection" data-postingan-id="{{ $postingan->id }}">
                    <div id="ulasanReviewsList" class="space-y-3 mb-6 {{ $postingan->ulasan->count() > 0 ? '' : 'hidden' }}">
                        @foreach ($postingan->ulasan as $ulasan)
                            <div class="border border-bjbo-border rounded-xl p-3.5 bg-gray-50/50">
                                <div class="flex items-center justify-between mb-1.5">
                                    <div class="flex items-center gap-2">
                                        @if($ulasan->user)
                                            <img src="{{ $ulasan->user->profile_picture_url }}" alt="{{ $ulasan->user->name }}" class="w-6 h-6 rounded-full object-cover border border-bjbo-border">
                                        @else
                                            <i class="bi bi-person-circle text-gray-400"></i>
                                        @endif
                                        <span class="font-bold text-xs text-gray-800">{{ $ulasan->user->name ?? 'Warga' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <div class="flex items-center text-xs text-amber-400 gap-0.5">
                                            <i class="bi {{ $ulasan->rating >= 1 ? 'bi-star-fill' : 'bi-star text-gray-300' }}"></i>
                                            <i class="bi {{ $ulasan->rating >= 2 ? 'bi-star-fill' : 'bi-star text-gray-300' }}"></i>
                                            <i class="bi {{ $ulasan->rating >= 3 ? 'bi-star-fill' : 'bi-star text-gray-300' }}"></i>
                                            <i class="bi {{ $ulasan->rating >= 4 ? 'bi-star-fill' : 'bi-star text-gray-300' }}"></i>
                                            <i class="bi {{ $ulasan->rating >= 5 ? 'bi-star-fill' : 'bi-star text-gray-300' }}"></i>
                                        </div>
                                        <span class="text-[11px] font-bold text-gray-700 ml-1">{{ $ulasan->rating }}.0</span>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-600 leading-relaxed">{{ $ulasan->komentar }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div id="ulasanEmptyState" class="text-center py-6 px-4 bg-gray-50/50 rounded-xl border border-dashed border-bjbo-border mb-6 {{ $postingan->ulasan->count() > 0 ? 'hidden' : '' }}">
                        <i class="bi bi-star text-2xl text-amber-300 mb-1.5 block"></i>
                        <p class="text-xs font-semibold text-gray-700">Belum ada ulasan untuk barang ini</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">Beli barang dan jadilah yang pertama memberikan penilaian pengalaman Anda!</p>
                    </div>
                </div>

                @if(Auth::check() && Auth::id() !== $postingan->user_id)
                    <!-- Form Kirim Ulasan dengan Bintang Interaktif -->
                    <div class="border-t border-bjbo-border pt-5">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Beri Ulasan Anda</h4>
                            <span class="text-[11px] text-gray-400">Pilih rating bintang transaksi</span>
                        </div>

                        <form action="{{ route('ulasan.store') }}" method="POST" class="space-y-3.5" id="reviewForm">
                            @csrf
                            <input type="hidden" name="postingan_id" value="{{ $postingan->id }}">
                            <input type="hidden" name="rating" id="selectedRating" value="5" required>

                            <!-- Widget Rating Bintang Interaktif -->
                            <div class="bg-gray-50 border border-bjbo-border rounded-xl p-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-center gap-1.5" id="starContainer" role="radiogroup" aria-label="Penilaian Bintang">
                                    <button type="button" class="star-btn text-2xl text-amber-400 hover:scale-115 transition-transform duration-150 cursor-pointer focus:outline-none" data-value="1" title="1 Bintang (Sangat Kecewa)">
                                        <i class="bi bi-star-fill"></i>
                                    </button>
                                    <button type="button" class="star-btn text-2xl text-amber-400 hover:scale-115 transition-transform duration-150 cursor-pointer focus:outline-none" data-value="2" title="2 Bintang (Kurang Puas)">
                                        <i class="bi bi-star-fill"></i>
                                    </button>
                                    <button type="button" class="star-btn text-2xl text-amber-400 hover:scale-115 transition-transform duration-150 cursor-pointer focus:outline-none" data-value="3" title="3 Bintang (Cukup)">
                                        <i class="bi bi-star-fill"></i>
                                    </button>
                                    <button type="button" class="star-btn text-2xl text-amber-400 hover:scale-115 transition-transform duration-150 cursor-pointer focus:outline-none" data-value="4" title="4 Bintang (Puas)">
                                        <i class="bi bi-star-fill"></i>
                                    </button>
                                    <button type="button" class="star-btn text-2xl text-amber-400 hover:scale-115 transition-transform duration-150 cursor-pointer focus:outline-none" data-value="5" title="5 Bintang (Sangat Puas!)">
                                        <i class="bi bi-star-fill"></i>
                                    </button>
                                </div>
                                <div class="text-xs font-bold text-gray-700 flex items-center gap-2">
                                    <span id="ratingScoreText" class="px-2.5 py-0.5 rounded bg-amber-100 text-amber-800 text-xs font-mono font-bold">5.0</span>
                                    <span id="ratingLabelText" class="text-gray-600 font-medium">Sangat Puas!</span>
                                </div>
                            </div>

                            <!-- Input Komentar Ulasan -->
                            <div>
                                <textarea name="komentar" rows="2" class="w-full px-3.5 py-2.5 text-xs border border-bjbo-border-strong rounded-xl focus:border-bjbo-primary focus:ring-1 focus:ring-bjbo-primary focus:outline-none transition resize-none" placeholder="Bagikan ulasan Anda mengenai kondisi barang, respon penjual, dan kelancaran transaksi..." required maxlength="500"></textarea>
                            </div>

                            <div class="flex items-center justify-between pt-1">
                                <span class="text-[11px] text-gray-400 hidden sm:inline">Ulasan akan ditampilkan secara publik di rincian produk ini.</span>
                                <button type="submit" class="bjbo-btn-primary text-xs py-2 px-5 font-bold flex items-center gap-1.5 ml-auto shadow-sm">
                                    <i class="bi bi-star-fill text-[11px] text-amber-300"></i>
                                    <span>Kirim Ulasan</span>
                                </button>
                            </div>
                        </form>
                    </div>
                @elseif(!Auth::check())
                    <div class="border-t border-bjbo-border pt-4 text-center">
                        <p class="text-xs text-gray-500 mb-2">Ingin memberikan ulasan untuk barang ini?</p>
                        <a href="{{ route('login') }}" class="bjbo-btn-outline text-xs py-1.5 px-4 font-bold inline-flex items-center gap-1.5">
                            <i class="bi bi-box-arrow-in-right"></i>
                            <span>Masuk Akun untuk Beri Ulasan</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Kolom Kanan: Floating Buy Box Tokopedia Style (md:col-span-12 lg:col-span-3) -->
        <div class="md:col-span-12 lg:col-span-3 space-y-4 lg:sticky lg:top-20">
            <div class="bg-white border border-bjbo-border rounded-2xl p-5 shadow-sm space-y-4">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Atur Pembelian</h3>

                <div class="pb-3 border-b border-bjbo-border">
                    <div class="text-[11px] text-gray-500 mb-0.5">Subtotal Harga</div>
                    <div class="bjbo-price text-2xl font-extrabold text-bjbo-primary">{{ $postingan->formatted_price }}</div>
                    <span class="text-[11px] text-green-700 font-semibold"><i class="bi bi-check-circle"></i> Bebas Biaya Layanan QRIS</span>
                </div>

                <!-- Tombol Transaksi -->
                <div class="space-y-2">
                    @if($postingan->is_sold)
                        <button type="button" disabled class="w-full py-2.5 text-xs font-bold rounded-xl bg-gray-200 text-gray-500 cursor-not-allowed text-center">
                            Barang Sudah Terjual
                        </button>
                    @elseif(Auth::check() && Auth::id() === $postingan->user_id)
                        <a href="{{ route('user.profil.edit', $postingan->id) }}" class="bjbo-btn-outline w-full py-2.5 text-xs font-bold text-center block">
                            <i class="bi bi-pencil"></i> Ubah Rincian Produk Anda
                        </a>
                    @else
                        <a href="{{ Auth::check() ? route('user.pesanan.checkout', $postingan->id) : route('login') }}" class="bjbo-btn-accent w-full py-2.5 text-xs font-bold text-center block shadow-md">
                            <i class="bi bi-qr-code"></i> Beli Sekarang (QRIS / Midtrans)
                        </a>

                        <a href="{{ Auth::check() ? route('user.chat.start', $postingan->id) : route('login') }}" class="w-full py-2.5 text-xs font-bold rounded-xl bg-bjbo-primary text-white hover:bg-bjbo-primary-hover transition text-center block shadow-sm flex items-center justify-center gap-1.5">
                            <i class="bi bi-chat-dots-fill"></i> Tanya Stok / Chat Penjual
                        </a>

                        @if(Auth::check())
                            <form action="{{ route('user.favorit.toggle', $postingan->id) }}" method="POST" class="w-full" data-favorite-form data-postingan-id="{{ $postingan->id }}">
                                @csrf
                                @php
                                    $isFav = $postingan->isFavoritedBy(Auth::user());
                                @endphp
                                <button type="submit" class="w-full py-2 text-xs font-semibold rounded-xl border border-gray-300 hover:bg-gray-50 text-gray-700 transition flex items-center justify-center gap-1.5" data-favorite-btn="{{ $postingan->id }}">
                                    <i class="bi {{ $isFav ? 'bi-heart-fill text-red-500' : 'bi-heart text-gray-500' }}"></i>
                                    <span class="fav-text">{{ $isFav ? 'Tersimpan di Favorit' : 'Tambah ke Favorit' }}</span>
                                </button>
                            </form>
                        @endif
                    @endif
                </div>

                <!-- Kartu Informasi Toko Penjual Komunitas -->
                <div class="pt-4 border-t border-bjbo-border">
                    <div class="flex items-center gap-3">
                        @if($postingan->user)
                            <img src="{{ $postingan->user->profile_picture_url }}" alt="{{ $postingan->username }}" class="w-10 h-10 rounded-full object-cover border border-bjbo-border flex-shrink-0">
                        @else
                            <div class="w-10 h-10 rounded-full bg-blue-50 text-bjbo-primary flex items-center justify-center font-bold text-sm">W</div>
                        @endif
                        <div class="min-w-0 flex-grow">
                            <div class="font-bold text-xs text-gray-900 truncate">{{ $postingan->username ?? 'Warga BJBO' }}</div>
                            <div class="text-[10px] text-green-700 font-semibold flex items-center gap-1">
                                <i class="bi bi-patch-check-fill"></i> Penjual Terverifikasi
                            </div>
                            <div class="text-[11px] text-gray-400 mt-0.5"><i class="bi bi-geo-alt"></i> {{ $postingan->lokasi }}</div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Lapor -->
                <div class="pt-2 text-center">
                    <button type="button" class="btn-report text-[11px] text-gray-400 hover:text-red-600 transition inline-flex items-center gap-1" data-id="{{ $postingan->id }}">
                        <i class="bi bi-flag"></i> Laporkan Masalah / Penipuan
                    </button>
                </div>
            </div>

            <!-- Barang Terkait (Loop 2) -->
            @if(isset($postingansama) && $postingansama->count() > 0)
                <div class="bg-white border border-bjbo-border rounded-2xl p-4 shadow-xs">
                    <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider mb-3">Produk Serupa</h4>
                    <div class="space-y-2.5">
                        @foreach($postingansama->take(3) as $related)
                            <a href="{{ route('user.detail.postingan', $related->id) }}" class="flex items-center gap-2.5 p-2 rounded-xl border border-bjbo-border hover:border-bjbo-primary/40 hover:bg-gray-50 transition group">
                                <img src="{{ $related->image_url }}" alt="{{ $related->name }}" class="w-12 h-12 rounded-lg object-cover flex-shrink-0">
                                <div class="min-w-0 flex-grow">
                                    <div class="font-bold text-xs text-gray-800 truncate group-hover:text-bjbo-primary transition">{{ $related->name }}</div>
                                    <div class="bjbo-price text-xs font-extrabold">{{ $related->formatted_price }}</div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</main>

<!-- Floating Action Bar Khusus Handphone (< lg) -->
<div class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-bjbo-border p-2.5 sm:p-3 shadow-xl safe-area-bottom">
    <div class="flex items-center justify-between gap-2.5 max-w-xl mx-auto">
        <div class="min-w-0 flex-shrink">
            <span class="text-[10px] text-gray-500 block leading-tight">Harga Barang</span>
            <span class="bjbo-price text-sm sm:text-base font-extrabold text-bjbo-accent truncate block">
                {{ $postingan->formatted_price }}
            </span>
        </div>

        <div class="flex items-center gap-1.5 sm:gap-2 flex-shrink-0">
            @if(Auth::check())
                <form action="{{ route('user.favorit.toggle', $postingan->id) }}" method="POST" class="m-0" data-favorite-form data-postingan-id="{{ $postingan->id }}">
                    @csrf
                    @php
                        $isFavMobile = $postingan->isFavoritedBy(Auth::user());
                    @endphp
                    <button type="submit" class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl border border-gray-300 hover:bg-gray-50 text-gray-700 flex items-center justify-center transition active:scale-95" title="Favorit" data-favorite-btn-mobile="{{ $postingan->id }}">
                        <i class="bi {{ $isFavMobile ? 'bi-heart-fill text-red-500' : 'bi-heart text-gray-600' }} text-base"></i>
                    </button>
                </form>
            @endif

            @if($postingan->is_sold)
                <button type="button" disabled class="py-2 px-3 sm:px-4 text-xs font-bold rounded-xl bg-gray-200 text-gray-500 cursor-not-allowed">
                    Sudah Terjual
                </button>
            @elseif(Auth::check() && Auth::id() === $postingan->user_id)
                <a href="{{ route('user.profil.edit', $postingan->id) }}" class="bjbo-btn-outline py-2 px-3 sm:px-4 text-xs font-bold">
                    <i class="bi bi-pencil"></i> Ubah
                </a>
            @else
                <a href="{{ Auth::check() ? route('user.chat.start', $postingan->id) : route('login') }}" class="bjbo-btn-primary py-2 px-2.5 sm:px-3 text-xs font-semibold flex items-center gap-1">
                    <i class="bi bi-chat-dots-fill"></i> <span class="hidden xs:inline">Tanya Stok</span>
                </a>

                <a href="{{ Auth::check() ? route('user.pesanan.checkout', $postingan->id) : route('login') }}" class="bjbo-btn-accent py-2 px-3 sm:px-4 text-xs font-bold flex items-center gap-1 shadow-md">
                    <i class="bi bi-bag-check-fill"></i> Beli Sekarang
                </a>
            @endif
        </div>
    </div>
</div>

<!-- Modal Laporan -->
@include('components.modal-laporan')

@include('components.footer')

<script>
document.addEventListener('DOMContentLoaded', function () {
    const starContainer = document.getElementById('starContainer');
    if (!starContainer) return;

    const starBtns = starContainer.querySelectorAll('.star-btn');
    const ratingInput = document.getElementById('selectedRating');
    const scoreText = document.getElementById('ratingScoreText');
    const labelText = document.getElementById('ratingLabelText');

    const ratingLabels = {
        1: { score: '1.0', label: 'Sangat Kecewa' },
        2: { score: '2.0', label: 'Kurang Puas' },
        3: { score: '3.0', label: 'Cukup' },
        4: { score: '4.0', label: 'Puas' },
        5: { score: '5.0', label: 'Sangat Puas!' }
    };

    let currentRating = parseInt(ratingInput.value, 10) || 5;

    function renderStars(rating) {
        starBtns.forEach(btn => {
            const val = parseInt(btn.dataset.value, 10);
            const icon = btn.querySelector('i');
            if (val <= rating) {
                btn.className = 'star-btn text-2xl text-amber-400 hover:scale-115 transition-transform duration-150 cursor-pointer focus:outline-none';
                icon.className = 'bi bi-star-fill';
            } else {
                btn.className = 'star-btn text-2xl text-gray-300 hover:scale-115 transition-transform duration-150 cursor-pointer focus:outline-none';
                icon.className = 'bi bi-star';
            }
        });
        const info = ratingLabels[rating] || ratingLabels[5];
        if (scoreText) scoreText.textContent = info.score;
        if (labelText) labelText.textContent = info.label;
    }

    starBtns.forEach(btn => {
        btn.addEventListener('mouseenter', function () {
            const hoverVal = parseInt(this.dataset.value, 10);
            renderStars(hoverVal);
        });

        btn.addEventListener('click', function () {
            currentRating = parseInt(this.dataset.value, 10);
            ratingInput.value = currentRating;
            renderStars(currentRating);
        });
    });

    starContainer.addEventListener('mouseleave', function () {
        renderStars(currentRating);
    });

    renderStars(currentRating);
});
</script>

</body>
</html>
