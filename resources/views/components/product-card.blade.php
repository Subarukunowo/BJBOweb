@props(['item', 'isAuth' => false])

@php
    $isSold = $item->is_sold;
@endphp

<div class="bg-white border border-bjbo-border rounded-2xl overflow-hidden flex flex-col shadow-xs hover:shadow-md hover:-translate-y-1 transition duration-150 group relative" data-postingan-card="{{ $item->id }}">
    <!-- Gambar Produk, Kategori, Kondisi & Tombol Favorit -->
    <div class="relative w-full aspect-[4/3] bg-gray-100 overflow-hidden">
        <div class="absolute top-2 left-2 z-10 flex flex-col gap-1">
            <span class="bg-black/70 backdrop-blur-xs text-white text-[10px] font-bold px-2 py-0.5 rounded shadow-xs">
                {{ $item->category }}
            </span>
            <span class="bg-white/90 backdrop-blur-xs text-bjbo-primary text-[10px] font-bold px-1.5 py-0.5 rounded border border-blue-200 shadow-xs">
                {{ $item->kondisi ?? 'Bekas' }}
            </span>
        </div>

        @if($isAuth && Auth::check())
            <form action="{{ route('user.favorit.toggle', $item->id) }}" method="POST" class="absolute top-2 right-2 z-10 m-0" data-favorite-form data-postingan-id="{{ $item->id }}">
                @csrf
                @php
                    $isFav = $item->isFavoritedBy(Auth::user());
                @endphp
                <button type="submit" class="w-8 h-8 rounded-full bg-white/95 hover:bg-white text-gray-700 shadow-sm flex items-center justify-center transition active:scale-90" title="{{ $isFav ? 'Hapus dari favorit' : 'Tambah ke favorit' }}">
                    <i class="bi {{ $isFav ? 'bi-heart-fill text-red-500' : 'bi-heart text-gray-600' }} text-sm"></i>
                </button>
            </form>
        @endif

        @if($isSold)
            <div class="absolute inset-0 z-10 bg-black/60 flex items-center justify-center sold-badge-overlay" data-sold-overlay="{{ $item->id }}">
                <span class="text-white text-xs font-extrabold tracking-wider bg-black/80 px-3 py-1 rounded-full border border-white/20">
                    SUDAH TERJUAL
                </span>
            </div>
        @else
            <div class="absolute inset-0 z-10 bg-black/60 items-center justify-center sold-badge-overlay hidden" data-sold-overlay="{{ $item->id }}">
                <span class="text-white text-xs font-extrabold tracking-wider bg-black/80 px-3 py-1 rounded-full border border-white/20">
                    SUDAH TERJUAL
                </span>
            </div>
        @endif

        <img src="{{ $item->image_url }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-200 {{ $isSold ? 'grayscale' : '' }}" alt="{{ $item->name }}" loading="lazy" data-product-img="{{ $item->id }}">
    </div>

    <!-- Isi Rincian Kartu Produk Tokopedia Style -->
    <div class="p-2.5 sm:p-3.5 flex flex-col flex-grow">
        <h3 class="font-bold text-xs sm:text-sm text-gray-900 line-clamp-2 mb-1.5 leading-snug group-hover:text-bjbo-primary transition" title="{{ $item->name }}">
            {{ $item->name }}
        </h3>

        <!-- Nominal Harga & Tipe Nego -->
        <div class="flex flex-wrap items-baseline gap-1 sm:gap-1.5 mb-1.5">
            <span class="bjbo-price text-xs sm:text-base font-extrabold text-bjbo-accent">
                {{ $item->formatted_price }}
            </span>
            <span class="text-[9px] sm:text-[10px] font-semibold text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded truncate max-w-[100px]">
                {{ $item->tipe_penawaran ?? 'Bisa Nego' }}
            </span>
        </div>

        <!-- Lokasi & Penjual -->
        <div class="flex items-center gap-1 text-[10px] sm:text-[11px] text-gray-500 mb-1">
            <i class="bi bi-geo-alt text-gray-400 text-[10px] sm:text-xs"></i>
            <span class="truncate">{{ $item->lokasi }}</span>
            <span>&bull;</span>
            <span class="truncate">{{ $item->username ?? 'Warga' }}</span>
        </div>

        <!-- Rating & Tayangan -->
        <div class="flex items-center gap-1.5 text-[10px] sm:text-[11px] text-gray-500 mb-2.5">
            @if($item->rating_score)
                <span class="inline-flex items-center gap-0.5 font-bold text-amber-600">
                    <i class="bi bi-star-fill text-[9px] text-amber-400"></i> {{ $item->rating_score }}
                </span>
            @else
                <span class="inline-flex items-center gap-0.5 text-gray-400 font-medium">
                    <i class="bi bi-star text-[9px] text-gray-300"></i> Baru
                </span>
            @endif
            <span>&bull;</span>
            <span>{{ $item->views_count }} tayang</span>
        </div>

        <!-- Tombol Aksi Bawah Kartu -->
        <div class="mt-auto pt-2 border-t border-bjbo-border flex items-center justify-between gap-1 sm:gap-1.5">
            @if($isAuth)
                <a href="{{ route('user.detail.postingan', $item->id) }}" class="bjbo-btn-outline text-[11px] sm:text-xs py-1.5 px-1.5 sm:px-2.5 flex-1 text-center font-medium">
                    Detail
                </a>
                @if(!$isSold && Auth::id() !== $item->user_id)
                    <a href="{{ route('user.pesanan.checkout', $item->id) }}" class="bjbo-btn-accent text-[11px] sm:text-xs py-1.5 px-1.5 sm:px-2.5 flex-1 text-center font-bold" title="Beli sekarang dengan QRIS atau Midtrans">
                        <i class="bi bi-bag-check"></i> Beli
                    </a>
                @endif
            @else
                <span class="inline-flex items-center gap-1 text-[9px] sm:text-[10px] font-bold text-green-700 bg-green-50 border border-green-200 px-1.5 sm:px-2 py-0.5 rounded truncate">
                    <i class="bi bi-check-circle"></i> Terverifikasi
                </span>
                <a href="{{ route('login') }}" class="bjbo-btn-outline text-[11px] sm:text-xs py-1 px-1.5 sm:px-2.5 flex-shrink-0">
                    Detail <i class="bi bi-arrow-right text-[10px]"></i>
                </a>
            @endif
        </div>
    </div>
</div>
