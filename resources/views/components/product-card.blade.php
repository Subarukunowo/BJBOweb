@props(['item', 'isAuth' => false])

@php
    $isSold = $item->is_sold;
@endphp

<div class="bg-white border border-zinc-200 rounded-xl overflow-hidden flex flex-col shadow-xs hover:shadow-md hover:-translate-y-1 transition duration-150 group relative" data-postingan-card="{{ $item->id }}">
    <!-- Gambar Produk Persegi Aspek Rasio 1:1 Bersih (Image 2 Standard) -->
    <div class="relative w-full aspect-square bg-zinc-100 overflow-hidden">
        <!-- Badge Atas Kiri: Diskon/Kondisi & Kategori -->
        <div class="absolute top-2 left-2 z-10 flex flex-col gap-1 items-start">
            <span class="bg-red-600 text-white text-[10px] font-extrabold px-1.5 py-0.5 rounded shadow-xs uppercase tracking-wider">
                {{ $item->kondisi ?? 'Bekas' }}
            </span>
            <span class="bg-black/60 backdrop-blur-xs text-white text-[9px] font-medium px-1.5 py-0.2 rounded shadow-xs">
                {{ $item->category }}
            </span>
        </div>

        @if($isAuth && Auth::check())
            <form action="{{ route('user.favorit.toggle', $item->id) }}" method="POST" class="absolute top-2 right-2 z-10 m-0" data-favorite-form data-postingan-id="{{ $item->id }}">
                @csrf
                @php
                    $isFav = $item->isFavoritedBy(Auth::user());
                @endphp
                <button type="submit" class="w-7 h-7 rounded-full bg-white/90 hover:bg-white text-zinc-700 shadow-xs flex items-center justify-center transition active:scale-90" title="{{ $isFav ? 'Hapus dari favorit' : 'Tambah ke favorit' }}">
                    <i class="bi {{ $isFav ? 'bi-heart-fill text-red-500' : 'bi-heart text-zinc-600' }} text-xs"></i>
                </button>
            </form>
        @endif

        @if($isSold)
            <div class="absolute inset-0 z-10 bg-black/60 flex items-center justify-center sold-badge-overlay" data-sold-overlay="{{ $item->id }}">
                <span class="text-white text-[11px] font-extrabold tracking-wider bg-black/80 px-2.5 py-1 rounded-full border border-white/20">
                    SUDAH TERJUAL
                </span>
            </div>
        @else
            <div class="absolute inset-0 z-10 bg-black/60 items-center justify-center sold-badge-overlay hidden" data-sold-overlay="{{ $item->id }}">
                <span class="text-white text-[11px] font-extrabold tracking-wider bg-black/80 px-2.5 py-1 rounded-full border border-white/20">
                    SUDAH TERJUAL
                </span>
            </div>
        @endif

        <img src="{{ $item->image_url }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-200 {{ $isSold ? 'grayscale' : '' }}" alt="{{ $item->name }}" loading="lazy" data-product-img="{{ $item->id }}">
    </div>

    <!-- Rincian Produk Bergaya Shadcn UI -->
    <div class="p-2.5 sm:p-3 flex flex-col flex-grow">
        <!-- Judul Produk -->
        <h3 class="font-medium text-xs sm:text-[13px] text-zinc-900 line-clamp-2 mb-1 leading-snug group-hover:text-blue-700 transition" title="{{ $item->name }}">
            {{ $item->name }}
        </h3>

        <!-- Nominal Harga Kontras Tabular Merah (Sesuai Gambar 2) -->
        <div class="flex items-baseline gap-1 mb-1">
            <span class="font-mono text-sm sm:text-base font-extrabold text-red-600">
                {{ $item->formatted_price }}
            </span>
        </div>

        <!-- Tag Benefit Hemat Ongkir BJPay -->
        <div class="mb-1.5">
            <span class="inline-flex items-center gap-0.5 text-[9px] font-bold text-amber-700 bg-amber-50 border border-amber-200/60 px-1 py-0.2 rounded">
                Hemat Rp5.000 via BJPay
            </span>
        </div>

        <!-- Rating Bintang & Jumlah Terjual / Tayang -->
        <div class="flex items-center gap-1 text-[10px] text-zinc-500 mb-1">
            @if($item->rating_score)
                <span class="inline-flex items-center gap-0.5 font-bold text-amber-600">
                    <i class="bi bi-star-fill text-[9px] text-amber-400"></i> {{ $item->rating_score }}
                </span>
                <span>&bull;</span>
            @endif
            <span>{{ $item->views_count ?? 0 }} tayang</span>
        </div>

        <!-- Lokasi & Nama Toko Terverifikasi (Centang Ungu Sesuai Gambar 2) -->
        <div class="flex items-center gap-1 text-[10px] text-zinc-500 mb-2 truncate">
            <i class="bi bi-patch-check-fill text-indigo-600 text-[11px] flex-shrink-0"></i>
            <span class="truncate font-medium text-zinc-700">{{ $item->user->store->nama_toko ?? ($item->username ?? 'Warga Sedagho') }}</span>
        </div>

        <!-- Tombol Aksi Bawah Kartu -->
        <div class="mt-auto pt-2 border-t border-zinc-100 flex items-center justify-between gap-1.5">
            @if($isAuth)
                <a href="{{ route('user.detail.postingan', $item->id) }}" class="inline-flex items-center justify-center py-1 px-2 text-[11px] font-semibold rounded-md border border-zinc-200 bg-white hover:bg-zinc-50 text-zinc-700 transition flex-1 text-center shadow-xs anim-press">
                    Detail
                </a>
                @if(!$isSold && Auth::id() !== $item->user_id)
                    <form action="{{ route('user.cart.add', $item->id) }}" method="POST" class="inline m-0">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center p-1.5 rounded-md border border-zinc-200 bg-white hover:bg-blue-50 text-zinc-700 hover:text-bjbo-primary transition shadow-xs anim-press" title="Tambah ke Keranjang">
                            <i class="bi bi-cart-plus text-xs"></i>
                        </button>
                    </form>
                    <a href="{{ route('user.pesanan.checkout', $item->id) }}" class="inline-flex items-center justify-center py-1 px-2 text-[11px] font-bold rounded-md bg-zinc-900 hover:bg-zinc-800 text-white transition flex-1 text-center shadow-xs anim-press" title="Beli sekarang">
                        Beli
                    </a>
                @endif
            @else
                <a href="{{ route('layout') }}" class="inline-flex items-center justify-center py-1 px-2 text-[11px] font-semibold rounded-md border border-zinc-200 bg-white hover:bg-zinc-50 text-zinc-700 transition flex-1 text-center shadow-xs anim-press">
                    Detail
                </a>
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center py-1 px-2 text-[11px] font-bold rounded-md bg-zinc-900 hover:bg-zinc-800 text-white transition flex-1 text-center shadow-xs anim-press">
                    Beli
                </a>
            @endif
        </div>
    </div>
</div>
