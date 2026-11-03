<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan - BJBO Sedagho</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bjbo-bg text-gray-900 font-sans min-h-screen flex flex-col">

@include('sweetalert::alert')

<!-- Header Navigasi -->
@include('components.navbar-user')

<main class="container mx-auto px-3 sm:px-4 my-4 sm:my-6 flex-grow max-w-4xl pb-20 lg:pb-0">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 mb-1 flex items-center gap-2">
                <i class="bi bi-receipt text-bjbo-primary"></i> Riwayat Pesanan & Transaksi
            </h1>
            <span class="text-gray-500 text-xs">Kelola pesanan pembelian dan penjualan barang Anda (Total: {{ $pesanans->count() }})</span>
        </div>
        <a href="{{ route('user.dashboard') }}" class="bjbo-btn-outline text-xs">
            <i class="bi bi-arrow-left"></i> Kembali ke Beranda
        </a>
    </div>

    <!-- Daftar Pesanan (Loop 1) -->
    @if($pesanans->count() > 0)
        <div class="space-y-4">
            @foreach($pesanans as $pesanan)
                <div class="bg-white border border-bjbo-border rounded-2xl p-5 shadow-sm hover:shadow transition flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5 flex-grow">
                        <img src="{{ $pesanan->postingan->image_url }}" alt="{{ $pesanan->postingan->name }}" class="w-16 h-16 rounded-xl object-cover border border-bjbo-border flex-shrink-0">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-mono font-bold text-gray-700">#{{ $pesanan->order_number }}</span>
                                @if($pesanan->status === 'paid')
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-green-700 bg-green-50 border border-green-200 px-2 py-0.5 rounded">
                                        <i class="bi bi-check-circle-fill"></i> LUNAS
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded">
                                        <i class="bi bi-clock-history"></i> BELUM BAYAR
                                    </span>
                                @endif
                            </div>
                            <h3 class="font-bold text-sm text-gray-900 line-clamp-1">{{ $pesanan->postingan->name }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ $pesanan->created_at->format('d M Y, H:i') }} &bull;
                                @if($pesanan->user_id === Auth::id())
                                    <span class="text-blue-600 font-medium">Pembelian Anda</span>
                                @else
                                    <span class="text-emerald-600 font-medium">Penjualan Anda</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex sm:flex-col items-center sm:items-end justify-between w-full sm:w-auto gap-2 pt-3 sm:pt-0 border-t sm:border-t-0 border-bjbo-border">
                        <div class="bjbo-price text-base">
                            Rp{{ number_format($pesanan->getEffectivePrice(), 0, ',', '.') }}
                        </div>
                        <div class="flex items-center gap-1.5">
                            @if($pesanan->status === 'paid')
                                <a href="{{ route('user.pesanan.track', $pesanan->id) }}" class="bjbo-btn-outline text-xs py-1.5 px-2.5 text-bjbo-primary flex items-center gap-1" title="Lacak Pengiriman di Peta">
                                    <i class="bi bi-geo-alt"></i> Lacak
                                </a>
                            @endif
                            <a href="{{ route('user.pesanan.detail', $pesanan->id) }}" class="bjbo-btn-primary text-xs py-1.5 px-3">
                                Rincian <i class="bi bi-arrow-right text-xs"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-16 bg-white rounded-2xl border border-bjbo-border max-w-md mx-auto p-6 shadow-sm">
            <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-blue-50 text-bjbo-primary flex items-center justify-center text-3xl">
                <i class="bi bi-bag-x"></i>
            </div>
            <h5 class="font-bold text-gray-800 text-base mb-1">Belum Ada Transaksi Pesanan</h5>
            <p class="text-gray-500 text-xs mb-6 leading-relaxed">
                Anda belum pernah memesan atau menjual barang secara daring di BJBO Sedagho.
            </p>
            <a href="{{ route('user.dashboard') }}" class="bjbo-btn-primary text-xs py-2 px-4">
                Mulai Belanja Sekarang
            </a>
        </div>
    @endif
</main>

<!-- Komponen Mobile Bottom Nav (Khusus Handphone & Tablet Kecil) -->
@include('components.mobile-bottom-nav')

@include('components.footer')

</body>
</html>
