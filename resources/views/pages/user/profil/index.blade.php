<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Center & Kelola Etalase Produk - BJBO Sedagho</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" integrity="sha384-QuGBSgV5Im3DzL2z+8Ko9/hqNy/N0O7zwvXAtfd1MvPKWa/UbeLV65cfm4BV5Wgq" crossorigin="anonymous">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bjbo-bg text-gray-900 font-sans min-h-screen flex flex-col">

@include('sweetalert::alert')

<!-- Header Navigasi -->
@include('components.navbar-user')

<main class="container mx-auto px-3 sm:px-4 my-4 sm:my-6 flex-grow max-w-6xl pb-20 lg:pb-0">
    <!-- Header Toko & Penjual Komunitas (Tokopedia Seller Center Style) -->
    <div class="bg-white border border-bjbo-border rounded-2xl p-6 mb-6 shadow-sm relative overflow-hidden">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-5">
            <div class="flex items-center gap-4">
                <div class="relative group flex-shrink-0">
                    <img src="{{ Auth::user()->profile_picture_url }}" alt="{{ Auth::user()->name }}" class="w-16 h-16 sm:w-20 sm:h-20 rounded-full object-cover border-2 border-bjbo-primary shadow-sm">
                    <button type="button" onclick="toggleProfileModal(true)" class="absolute -bottom-1 -right-1 w-7 h-7 rounded-full bg-bjbo-primary text-white flex items-center justify-center text-xs shadow hover:bg-bjbo-primary-hover transition" title="Ganti Foto Profil">
                        <i class="bi bi-camera-fill"></i>
                    </button>
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">{{ Auth::user()->name }}</h1>
                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-green-700 bg-green-50 border border-green-200 px-2 py-0.5 rounded-full">
                            <i class="bi bi-patch-check-fill"></i> Penjual Sedagho Terverifikasi
                        </span>
                    </div>
                    <div class="text-xs text-gray-500 flex flex-wrap items-center gap-3">
                        <span><i class="bi bi-envelope"></i> {{ Auth::user()->email }}</span>
                        <span>&bull;</span>
                        <span><i class="bi bi-shop"></i> Etalase Warga Bengkalis</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                <button type="button" onclick="toggleProfileModal(true)" class="bjbo-btn-outline text-xs py-2 px-3 flex-1 sm:flex-initial">
                    <i class="bi bi-person-gear text-bjbo-primary"></i> Pengaturan Akun
                </button>
                <a href="{{ route('user.wishlist.index') }}" class="bjbo-btn-outline text-xs py-2 px-3 flex-1 sm:flex-initial text-pink-600 border-pink-200 hover:bg-pink-50">
                    <i class="bi bi-bookmark-heart"></i> Wishlist ({{ $wishlistsCount }})
                </a>
                <a href="{{ route('user.pesanan.index') }}" class="bjbo-btn-outline text-xs py-2 px-3 flex-1 sm:flex-initial">
                    <i class="bi bi-receipt text-bjbo-primary"></i> Riwayat Pesanan
                </a>
                @if($user->hasActiveStore())
                    <a href="{{ route('user.postingan.create') }}" class="bjbo-btn-accent text-xs py-2 px-4 font-bold shadow flex-1 sm:flex-initial text-center">
                        <i class="bi bi-plus-circle-fill"></i> + Tambah Produk Baru
                    </a>
                @elseif($store && $store->status === 'menunggu_verifikasi')
                    <a href="{{ route('user.toko.daftar') }}" class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-bold rounded-lg bg-blue-50 text-blue-800 border border-blue-200 flex-1 sm:flex-initial text-center hover:bg-blue-100 transition">
                        <i class="bi bi-hourglass-split"></i> Toko Sedang Diverifikasi
                    </a>
                @else
                    <a href="{{ route('user.toko.daftar') }}" class="bjbo-btn-accent text-xs py-2 px-4 font-bold shadow flex-1 sm:flex-initial text-center">
                        <i class="bi bi-shop-window"></i> Buka Toko untuk Jual Barang
                    </a>
                @endif
            </div>
        </div>

        <!-- Section Status Toko & Dompet Rekber (Standar Modern E-Commerce) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6 pt-6 border-t border-bjbo-border">
            <!-- Kartu Status Toko -->
            <div class="p-4 rounded-xl border {{ $store ? 'bg-purple-50/50 border-purple-200' : 'bg-gradient-to-r from-amber-50 to-orange-50 border-amber-200' }} flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl {{ $store ? 'bg-purple-100 text-purple-700' : 'bg-amber-100 text-amber-700' }} flex items-center justify-center text-lg flex-shrink-0">
                        <i class="bi {{ $store ? ($store->isOfficial() ? 'bi-patch-check-fill' : 'bi-shop') : 'bi-shop-window' }}"></i>
                    </div>
                    <div>
                        @if($store)
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs font-bold text-gray-900">{{ $store->store_name }}</span>
                                <span class="text-[10px] font-bold uppercase px-1.5 py-0.2 rounded {{ $store->isOfficial() ? 'bg-purple-600 text-white' : 'bg-blue-600 text-white' }}">
                                    {{ $store->isOfficial() ? 'Official' : 'Perorangan' }}
                                </span>
                            </div>
                            <span class="text-[11px] text-gray-500 block">Status: <strong class="capitalize text-gray-800">{{ str_replace('_', ' ', $store->status) }}</strong></span>
                        @else
                            <span class="text-xs font-bold text-gray-900 block">Buka Toko di BJBO</span>
                            <span class="text-[11px] text-gray-500">Pilihan: Toko Perorangan (KTP) / Official Store (NIB, NPWP & HAKI)</span>
                        @endif
                    </div>
                </div>

                <a href="{{ route('user.toko.daftar') }}" class="text-xs font-bold py-1.5 px-3 rounded-lg shadow-xs transition {{ $store ? 'bg-white border border-purple-200 text-purple-700 hover:bg-purple-50' : 'bg-amber-600 hover:bg-amber-700 text-white' }} flex-shrink-0">
                    {{ $store ? 'Kelola Toko' : 'Buka Toko Sekarang' }} &rsaquo;
                </a>
            </div>

            <!-- Kartu Dompet Rekber -->
            <div class="p-4 rounded-xl border bg-emerald-50/50 border-emerald-200 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg flex-shrink-0">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div>
                        <span class="text-[11px] text-gray-500 block">Saldo Escrow Rekber</span>
                        <div class="text-base font-extrabold text-emerald-800">Rp{{ number_format($saldoRekber, 0, ',', '.') }}</div>
                        <span class="text-[10px] text-gray-500">Pencairan: 2x24 jam kerja &bull; {{ $bankAccounts->count() }} Bank Terdaftar</span>
                    </div>
                </div>

                <a href="{{ route('user.rekber.index') }}" class="text-xs font-bold py-1.5 px-3 rounded-lg shadow-xs transition bg-emerald-700 hover:bg-emerald-800 text-white flex-shrink-0">
                    Tarik Dana &rsaquo;
                </a>
            </div>
        </div>

        <!-- 4 Kartu Metrik Seller Insights (Tokopedia Style) -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2.5 sm:gap-3 pt-5 sm:pt-6 mt-5 sm:mt-6 border-t border-bjbo-border">
            <div class="bg-blue-50/60 border border-blue-100 rounded-xl p-2.5 sm:p-3.5 flex items-center gap-2.5 sm:gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-100 text-bjbo-primary flex items-center justify-center text-lg flex-shrink-0">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div>
                    <span class="text-[11px] font-semibold text-gray-500 block">Total Produk</span>
                    <span class="text-lg font-extrabold text-gray-900 leading-tight">{{ $metrics['total_products'] }}</span>
                </div>
            </div>

            <div class="bg-green-50/60 border border-green-100 rounded-xl p-3.5 flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-green-100 text-green-700 flex items-center justify-center text-lg flex-shrink-0">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    <span class="text-[11px] font-semibold text-gray-500 block">Produk Siap Jual</span>
                    <span class="text-lg font-extrabold text-green-700 leading-tight">{{ $metrics['active_products'] }}</span>
                </div>
            </div>

            <div class="bg-amber-50/60 border border-amber-100 rounded-xl p-3.5 flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-lg flex-shrink-0">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div>
                    <span class="text-[11px] font-semibold text-gray-500 block">Menunggu Kurasi</span>
                    <span class="text-lg font-extrabold text-amber-700 leading-tight">{{ $metrics['pending_products'] }}</span>
                </div>
            </div>

            <div class="bg-purple-50/60 border border-purple-100 rounded-xl p-3.5 flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center text-lg flex-shrink-0">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div>
                    <span class="text-[11px] font-semibold text-gray-500 block">Nilai Etalase Aktif</span>
                    <span class="text-sm sm:text-base font-extrabold text-purple-800 leading-tight">Rp{{ number_format($metrics['total_catalog_value'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Kelola Etalase Produk Tokopedia Style -->
    <div class="bg-white border border-bjbo-border rounded-2xl p-5 mb-8 shadow-sm">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-4 border-b border-bjbo-border mb-4">
            <div>
                <h2 class="text-base font-bold text-gray-900 mb-0.5">Daftar Produk di Etalase</h2>
                <p class="text-xs text-gray-500">Kelola ketersediaan, ubah rincian spesifikasi, atau tandai produk yang telah terjual.</p>
            </div>

            <!-- Tab Filter Status Produk -->
            <div class="flex items-center gap-1.5 p-1 bg-gray-100 rounded-xl text-xs font-semibold overflow-x-auto w-full sm:w-auto">
                <button type="button" onclick="filterSellerProducts('all')" class="seller-tab-btn active px-3 py-1.5 rounded-lg transition bg-white text-bjbo-primary shadow-xs" data-target="all">
                    Semua ({{ $postingan->count() }})
                </button>
                <button type="button" onclick="filterSellerProducts('active')" class="seller-tab-btn px-3 py-1.5 rounded-lg transition text-gray-600 hover:text-gray-900" data-target="active">
                    Aktif ({{ $metrics['active_products'] }})
                </button>
                <button type="button" onclick="filterSellerProducts('sold')" class="seller-tab-btn px-3 py-1.5 rounded-lg transition text-gray-600 hover:text-gray-900" data-target="sold">
                    Terjual ({{ $metrics['sold_products'] }})
                </button>
            </div>
        </div>

        @if($postingan->count() > 0)
            <div class="space-y-3" id="sellerProductList">
                @foreach($postingan as $item)
                    @php
                        $isSold = $item->is_sold;
                        $isApproved = $item->status === 'disetujui';
                        $statusClass = $isSold ? 'product-sold' : ($isApproved ? 'product-active' : 'product-pending');
                    @endphp
                    <div class="seller-product-item {{ $statusClass }} border border-bjbo-border rounded-xl p-4 hover:border-bjbo-primary/40 transition flex flex-col md:flex-row items-start md:items-center justify-between gap-4 {{ $isSold ? 'bg-gray-50/70 opacity-80' : 'bg-white' }}" data-seller-item="{{ $item->id }}">
                        <div class="flex items-start sm:items-center gap-4 flex-grow min-w-0">
                            <div class="relative w-20 h-20 rounded-xl overflow-hidden border border-bjbo-border flex-shrink-0 bg-gray-100">
                                <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                                <span class="absolute inset-0 bg-black/60 flex items-center justify-center text-white text-[11px] font-bold seller-sold-overlay {{ $isSold ? '' : 'hidden' }}" data-seller-overlay="{{ $item->id }}">TERJUAL</span>
                            </div>

                            <div class="min-w-0 flex-grow">
                                <div class="flex flex-wrap items-center gap-1.5 mb-1">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-bjbo-primary bg-blue-50 border border-blue-200 px-2 py-0.5 rounded">
                                        {{ $item->category }}
                                    </span>
                                    <span class="text-[10px] font-semibold text-gray-600 bg-gray-100 border border-gray-200 px-2 py-0.5 rounded">
                                        {{ $item->kondisi ?? 'Bekas' }}
                                    </span>
                                    <span class="seller-status-badge-container" data-seller-badge="{{ $item->id }}">
                                        @if($isSold)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-gray-600 bg-gray-200 px-2 py-0.5 rounded">
                                                <i class="bi bi-bag-check-fill"></i> Sudah Terjual
                                            </span>
                                        @elseif($isApproved)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-green-700 bg-green-50 border border-green-200 px-2 py-0.5 rounded">
                                                <i class="bi bi-check-circle-fill"></i> Tayang Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded">
                                                <i class="bi bi-clock-history"></i> Menunggu Kurasi
                                            </span>
                                        @endif
                                    </span>
                                </div>

                                <h3 class="font-bold text-sm text-gray-900 truncate mb-1" title="{{ $item->name }}">
                                    {{ $item->name }}
                                </h3>

                                <div class="flex flex-wrap items-center gap-3 text-xs">
                                    <span class="bjbo-price text-sm">{{ $item->formatted_price }}</span>
                                    <span class="text-gray-400">&bull;</span>
                                    <span class="text-gray-500"><i class="bi bi-geo-alt"></i> {{ $item->lokasi }}</span>
                                    <span class="text-gray-400">&bull;</span>
                                    <span class="text-gray-500"><i class="bi bi-eye"></i> {{ $item->views_count }} tayang</span>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Aksi Penjual ala Tokopedia -->
                        <div class="flex items-center gap-2 w-full md:w-auto pt-3 md:pt-0 border-t md:border-t-0 border-bjbo-border justify-end flex-shrink-0">
                            <!-- Toggle Status Terjual -->
                            <form action="{{ route('user.profil.toggleSold', $item->id) }}" method="POST" class="m-0" data-toggle-sold-form data-postingan-id="{{ $item->id }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-lg border transition {{ $isSold ? 'bg-green-50 text-green-700 border-green-200 hover:bg-green-100' : 'bg-gray-50 text-gray-700 border-gray-300 hover:bg-gray-100' }}" title="{{ $isSold ? 'Kembalikan ke status aktif' : 'Tandai sebagai sudah terjual' }}" data-toggle-sold-btn="{{ $item->id }}">
                                    <i class="bi {{ $isSold ? 'bi-arrow-counterclockwise' : 'bi-check-all' }}"></i>
                                    <span>{{ $isSold ? 'Aktifkan' : 'Tandai Terjual' }}</span>
                                </button>
                            </form>

                            <a href="{{ route('user.detail.postingan', $item->id) }}" class="bjbo-btn-outline text-xs py-1.5 px-2.5" title="Lihat di etalase pembeli">
                                <i class="bi bi-eye"></i> Detail
                            </a>

                            <a href="{{ route('user.profil.edit', $item->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 font-semibold text-xs rounded-lg bg-blue-50 text-bjbo-primary border border-blue-200 hover:bg-blue-100 transition" title="Ubah spesifikasi produk">
                                <i class="bi bi-pencil"></i> Ubah
                            </a>

                            <form action="{{ route('user.profil.delete', $item->id) }}" method="POST" onsubmit="return confirm('Hapus produk ini dari etalase Anda secara permanen?');" class="m-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-xs rounded-lg bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 transition" title="Hapus produk">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-16 max-w-md mx-auto p-6">
                <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-blue-50 text-bjbo-primary flex items-center justify-center text-3xl border border-blue-100">
                    <i class="bi bi-shop-window"></i>
                </div>
                <h3 class="font-bold text-gray-800 text-base mb-1">Etalase Toko Anda Masih Kosong</h3>
                <p class="text-gray-500 text-xs mb-6 leading-relaxed">
                    Mulai tawarkan barang bekas berkualitas atau produk baru Anda kepada seluruh warga Sedagho di Kabupaten Bengkalis.
                </p>
                @if($user->hasActiveStore())
                    <a href="{{ route('user.postingan.create') }}" class="bjbo-btn-accent text-xs py-2.5 px-5 font-bold shadow">
                        <i class="bi bi-plus-circle-fill"></i> Tambah Produk Pertama
                    </a>
                @elseif($store && $store->status === 'menunggu_verifikasi')
                    <a href="{{ route('user.toko.daftar') }}" class="inline-flex items-center gap-1.5 px-5 py-2.5 text-xs font-bold rounded-lg bg-blue-100 text-blue-900 border border-blue-300 hover:bg-blue-200 transition">
                        <i class="bi bi-hourglass-split"></i> Lihat Status Verifikasi Toko
                    </a>
                @else
                    <a href="{{ route('user.toko.daftar') }}" class="bjbo-btn-accent text-xs py-2.5 px-5 font-bold shadow">
                        <i class="bi bi-shop-window"></i> Buka Toko untuk Mulai Jual Barang
                    </a>
                @endif
            </div>
        @endif
    </div>

    <!-- Modal Edit Profil & Foto Akun -->
    <div id="profileEditModal" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl border border-bjbo-border shadow-xl max-w-lg w-full p-6 relative max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3 border-b border-bjbo-border mb-4">
                <h3 class="text-base font-bold text-gray-900">Perbarui Profil & Foto Akun</h3>
                <button type="button" onclick="toggleProfileModal(false)" class="text-gray-400 hover:text-gray-600 text-lg">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form action="{{ route('user.profil.updateAccount') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <!-- Foto Profil & Preview -->
                <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-xl border border-bjbo-border">
                    <img id="avatarPreviewImg" src="{{ Auth::user()->profile_picture_url }}" alt="{{ Auth::user()->name }}" class="w-16 h-16 rounded-full object-cover border-2 border-bjbo-primary">
                    <div class="flex-grow min-w-0">
                        <label for="avatarFileInput" class="block text-xs font-bold text-gray-700 mb-1">Foto Profil Baru</label>
                        <input type="file" name="profile_picture" id="avatarFileInput" accept="image/png,image/jpeg,image/jpg,image/webp" class="text-xs text-gray-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-bjbo-primary hover:file:bg-blue-100 cursor-pointer">
                        <p class="text-[10px] text-gray-400 mt-1">Format: JPG, PNG, WEBP (Maksimal 2MB)</p>
                    </div>
                </div>

                <!-- Input Nama -->
                <div>
                    <label for="name" class="block text-xs font-bold text-gray-700 mb-1">Nama Toko / Nama Lengkap</label>
                    <input type="text" id="name" name="name" value="{{ old('name', Auth::user()->name) }}" required class="w-full px-3 py-2 text-xs sm:text-sm border border-bjbo-border-strong rounded-lg focus:outline-none focus:ring-2 focus:ring-bjbo-primary/20 focus:border-bjbo-primary">
                    @error('name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Input Email -->
                <div>
                    <label for="email" class="block text-xs font-bold text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', Auth::user()->email) }}" required class="w-full px-3 py-2 text-xs sm:text-sm border border-bjbo-border-strong rounded-lg focus:outline-none focus:ring-2 focus:ring-bjbo-primary/20 focus:border-bjbo-primary">
                    @error('email')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Input Password Baru -->
                <div>
                    <label for="password" class="block text-xs font-bold text-gray-700 mb-1">Kata Sandi Baru (Opsional)</label>
                    <input type="password" id="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah kata sandi" class="w-full px-3 py-2 text-xs sm:text-sm border border-bjbo-border-strong rounded-lg focus:outline-none focus:ring-2 focus:ring-bjbo-primary/20 focus:border-bjbo-primary">
                    <p class="text-[10px] text-gray-400 mt-1">Minimal 6 karakter jika ingin mengganti sandi.</p>
                    @error('password')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tombol Aksi -->
                <div class="flex items-center justify-between pt-3 border-t border-bjbo-border">
                    @if(Auth::user()->profile_picture && !str_starts_with(Auth::user()->profile_picture, 'http'))
                        <button type="button" onclick="document.getElementById('deleteAvatarForm').submit();" class="text-xs text-red-600 hover:text-red-700 font-medium">
                            <i class="bi bi-trash"></i> Hapus Foto
                        </button>
                    @else
                        <div></div>
                    @endif

                    <div class="flex items-center gap-2">
                        <button type="button" onclick="toggleProfileModal(false)" class="px-3 py-1.5 rounded-lg border border-gray-300 text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button type="submit" class="bjbo-btn-accent text-xs py-1.5 px-4 font-bold shadow">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>

            @if(Auth::user()->profile_picture && !str_starts_with(Auth::user()->profile_picture, 'http'))
                <form id="deleteAvatarForm" action="{{ route('user.profil.removeAvatar') }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            @endif
        </div>
    </div>
</main>

<!-- Komponen Mobile Bottom Nav (Khusus Handphone & Tablet Kecil) -->
@include('components.mobile-bottom-nav')

@include('components.footer')

<script>
    function toggleProfileModal(show) {
        const modal = document.getElementById('profileEditModal');
        if (modal) {
            if (show) {
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
            }
        }
    }

    function filterSellerProducts(type) {
        const buttons = document.querySelectorAll('.seller-tab-btn');
        buttons.forEach(btn => {
            if (btn.dataset.target === type) {
                btn.classList.add('bg-white', 'text-bjbo-primary', 'shadow-xs');
                btn.classList.remove('text-gray-600');
            } else {
                btn.classList.remove('bg-white', 'text-bjbo-primary', 'shadow-xs');
                btn.classList.add('text-gray-600');
            }
        });

        const items = document.querySelectorAll('.seller-product-item');
        items.forEach(item => {
            if (type === 'all') {
                item.style.display = 'flex';
            } else if (type === 'active') {
                item.style.display = item.classList.contains('product-active') ? 'flex' : 'none';
            } else if (type === 'sold') {
                item.style.display = item.classList.contains('product-sold') ? 'flex' : 'none';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const fileInput = document.getElementById('avatarFileInput');
        const previewImg = document.getElementById('avatarPreviewImg');

        if (fileInput && previewImg) {
            fileInput.addEventListener('change', function () {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        previewImg.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    });
</script>

</body>
</html>
