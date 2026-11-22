<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - BJBO</title>
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Vite + Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-zinc-50 text-zinc-900 font-sans min-h-screen flex flex-col antialiased">

@include('sweetalert::alert')

<!-- Navbar Pengguna -->
@include('components.navbar-user')

<!-- Konten Utama -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex-grow w-full">
    <!-- Breadcrumb & Judul Halaman -->
    <div class="mb-6">
        <nav class="flex items-center gap-2 text-xs text-zinc-500 mb-2 font-medium">
            <a href="{{ route('user.dashboard') }}" class="hover:text-bjbo-primary transition">Beranda</a>
            <span>/</span>
            <span class="text-zinc-800">Keranjang Belanja</span>
        </nav>
        <div class="flex items-center justify-between">
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900 flex items-center gap-2">
                <i class="bi bi-cart3 text-bjbo-primary"></i> Keranjang Belanja
            </h1>
            <span class="text-xs sm:text-sm font-medium text-zinc-500 font-mono">
                {{ $totalItem }} barang
            </span>
        </div>
    </div>

    @if($cartItems->isEmpty())
        <!-- Keadaan Kosong (Empty State) -->
        <div class="bg-white rounded-2xl border border-zinc-200 p-8 sm:p-12 text-center shadow-xs max-w-lg mx-auto my-8">
            <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 border border-blue-100">
                <i class="bi bi-cart-x text-3xl"></i>
            </div>
            <h3 class="text-base sm:text-lg font-bold text-zinc-900 mb-1">Keranjang Belanja Masih Kosong</h3>
            <p class="text-xs sm:text-sm text-zinc-500 mb-6 max-w-sm mx-auto">
                Yuk jelajahi etalase barang warga sedagho dan masukkan produk incaran Anda ke keranjang.
            </p>
            <a href="{{ route('user.dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold bg-bjbo-primary text-white hover:bg-blue-700 transition shadow-xs">
                <i class="bi bi-bag"></i> Mulai Belanja Sekarang
            </a>
        </div>
    @else
        <!-- Grid Keranjang Belanja & Ringkasan -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- Kolom Daftar Barang (8 Col) -->
            <div class="lg:col-span-8 space-y-3">
                @foreach($cartItems as $item)
                    @php
                        $postingan = $item->postingan;
                    @endphp
                    @if($postingan)
                        <div class="bg-white rounded-xl border border-zinc-200 p-4 shadow-xs hover:border-zinc-300 transition flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <!-- Info Produk & Gambar -->
                            <div class="flex items-center gap-3.5 min-w-0 flex-1">
                                <a href="{{ route('user.detail.postingan', $postingan->id) }}" class="flex-shrink-0 group">
                                    <img src="{{ $postingan->image_url }}" alt="{{ $postingan->name }}" class="w-16 h-16 sm:w-20 sm:h-20 rounded-lg object-cover border border-zinc-100 group-hover:opacity-90 transition">
                                </a>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="inline-block text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-zinc-100 text-zinc-700">
                                            {{ $postingan->category }}
                                        </span>
                                        <span class="text-[11px] text-zinc-500 truncate flex items-center gap-1">
                                            <i class="bi bi-shop text-zinc-400"></i> {{ $postingan->user->name ?? 'Penjual' }}
                                        </span>
                                    </div>
                                    <a href="{{ route('user.detail.postingan', $postingan->id) }}" class="block text-xs sm:text-sm font-bold text-zinc-900 hover:text-bjbo-primary transition truncate">
                                        {{ $postingan->name }}
                                    </a>
                                    <div class="mt-1 flex items-baseline gap-2">
                                        <span class="text-xs sm:text-sm font-bold font-mono text-red-600">
                                            {{ $postingan->formatted_price }}
                                        </span>
                                        <span class="text-[11px] text-zinc-400 font-medium">/ unit</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Kontrol Kuantitas & Subtotal -->
                            <div class="flex items-center justify-between sm:justify-end gap-4 w-full sm:w-auto pt-3 sm:pt-0 border-t sm:border-t-0 border-zinc-100">
                                <!-- Stepper Kuantitas -->
                                <div class="flex items-center border border-zinc-200 rounded-lg overflow-hidden bg-zinc-50">
                                    <form action="{{ route('user.cart.update', $item->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="quantity" value="{{ max(1, $item->quantity - 1) }}">
                                        <button type="submit" class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center text-zinc-600 hover:bg-zinc-200 transition text-xs font-bold" {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                            -
                                        </button>
                                    </form>
                                    <span class="w-8 sm:w-9 text-center text-xs font-bold font-mono text-zinc-800 bg-white py-1">
                                        {{ $item->quantity }}
                                    </span>
                                    <form action="{{ route('user.cart.update', $item->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="quantity" value="{{ min(99, $item->quantity + 1) }}">
                                        <button type="submit" class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center text-zinc-600 hover:bg-zinc-200 transition text-xs font-bold">
                                            +
                                        </button>
                                    </form>
                                </div>

                                <!-- Subtotal Nilai -->
                                <div class="text-right min-w-[90px]">
                                    <p class="text-[10px] text-zinc-400 font-medium">Subtotal</p>
                                    <p class="text-xs sm:text-sm font-bold font-mono text-zinc-900">
                                        {{ $item->formatted_subtotal }}
                                    </p>
                                </div>

                                <!-- Aksi Checkout Langsung & Hapus -->
                                <div class="flex items-center gap-1.5">
                                    <a href="{{ route('user.pesanan.checkout', $postingan->id) }}" class="p-1.5 sm:px-2.5 sm:py-1.5 rounded-lg text-xs font-semibold bg-bjbo-primary text-white hover:bg-blue-700 transition shadow-xs flex items-center gap-1" title="Checkout Barang Ini">
                                        <i class="bi bi-lightning-charge"></i> <span class="hidden sm:inline">Beli</span>
                                    </a>
                                    <form action="{{ route('user.cart.remove', $item->id) }}" method="POST" onsubmit="return confirm('Hapus barang ini dari keranjang?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-zinc-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus dari Keranjang">
                                            <i class="bi bi-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <!-- Kolom Ringkasan Belanja (4 Col) -->
            <div class="lg:col-span-4">
                <div class="bg-white rounded-2xl border border-zinc-200 p-5 shadow-xs sticky top-24">
                    <h3 class="text-sm font-bold text-zinc-900 mb-4 pb-3 border-b border-zinc-100 flex items-center gap-2">
                        <i class="bi bi-receipt text-bjbo-primary"></i> Ringkasan Belanja
                    </h3>

                    <div class="space-y-2.5 mb-5 text-xs sm:text-sm">
                        <div class="flex justify-between text-zinc-600">
                            <span>Total Item</span>
                            <span class="font-mono font-medium">{{ $totalItem }} barang</span>
                        </div>
                        <div class="flex justify-between text-zinc-600">
                            <span>Estimasi Potongan BJPay</span>
                            <span class="font-mono font-bold text-emerald-600">- Rp 5.000</span>
                        </div>
                        <div class="pt-3 border-t border-zinc-100 flex justify-between items-baseline">
                            <span class="font-bold text-zinc-900 text-sm">Total Belanja</span>
                            <span class="font-mono font-extrabold text-base sm:text-lg text-red-600">
                                Rp {{ number_format($totalBelanja, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <div class="bg-blue-50/60 rounded-xl p-3 border border-blue-100/80 mb-5">
                        <p class="text-[11px] text-blue-900 flex items-start gap-1.5 font-medium leading-relaxed">
                            <i class="bi bi-shield-check text-blue-600 mt-0.5 text-xs"></i>
                            <span>Transaksi dilindungi Rekber Escrow BJBO dan diskon ongkir otomatis dengan saldo BJPay.</span>
                        </p>
                    </div>

                    <p class="text-[11px] text-zinc-400 text-center">
                        Pilih tombol "Beli" pada barang di samping untuk memilih kurir dan menyelesaikan pesanan.
                    </p>
                </div>
            </div>
        </div>
    @endif
</main>

<!-- Footer -->
@include('components.footer')

</body>
</html>

