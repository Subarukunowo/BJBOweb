<!-- Header Navigasi Pengguna Terdaftar -->
<header class="bg-white border-b border-bjbo-border sticky top-0 z-50 shadow-xs py-2.5 sm:py-3">
    <div class="container mx-auto px-3 sm:px-4">
        <div class="flex items-center justify-between gap-2 sm:gap-4">
            <!-- Logo Brand -->
            <a href="{{ route('user.dashboard') }}" class="flex items-center text-decoration-none flex-shrink-0">
                <img src="{{ asset('images/logo.png') }}" alt="BJBO Logo" class="h-8 sm:h-10 w-auto object-contain">
            </a>

            <!-- Form Pencarian Publik (Tablet & Laptop / Desktop: md:flex) -->
            <form action="{{ route('postingan.cari') }}" method="GET" class="hidden md:flex relative flex-grow max-w-lg mx-2 lg:mx-4">
                <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" name="keyword" class="w-full h-10 pl-11 pr-4 text-xs sm:text-sm bg-bjbo-surface-subtle border border-bjbo-border-strong rounded-full focus:bg-white focus:border-bjbo-primary focus:ring-2 focus:ring-bjbo-primary/20 focus:outline-none transition" placeholder="Cari barang atau penjual..." value="{{ request('keyword') }}">
            </form>

            <!-- Aksi Pengguna -->
            <div class="flex items-center gap-1 sm:gap-2 flex-shrink-0">
                <!-- Tombol Notifikasi Real-time Reverb -->
                @php
                    $unreadNotifCount = \App\Models\AppNotification::where('user_id', Auth::id())->where('is_read', false)->count();
                @endphp
                <div class="relative">
                    <a href="{{ route('user.notifikasi.index') }}" class="p-1.5 sm:p-2 text-gray-600 hover:text-bjbo-primary hover:bg-gray-100 rounded-full transition relative block" title="Notifikasi">
                        <i class="bi bi-bell text-base"></i>
                        <span id="notifBadge" class="{{ $unreadNotifCount > 0 ? '' : 'hidden' }} absolute top-0.5 right-0.5 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.2 rounded-full min-w-[16px] text-center leading-tight">
                            {{ $unreadNotifCount > 99 ? '99+' : $unreadNotifCount }}
                        </span>
                    </a>
                </div>

                <!-- Tombol Cepat Wishlist -->
                <a href="{{ route('user.wishlist.index') }}" class="p-1.5 sm:p-2 text-gray-600 hover:text-pink-500 hover:bg-gray-100 rounded-full transition" title="Wishlist Produk">
                    <i class="bi bi-bookmark-heart text-base"></i>
                </a>

                <!-- Tombol Cepat Favorit -->
                <a href="{{ route('user.favorit') }}" class="p-1.5 sm:p-2 text-gray-600 hover:text-red-500 hover:bg-gray-100 rounded-full transition" title="Barang Disukai">
                    <i class="bi bi-heart text-base"></i>
                </a>

                <!-- Tombol Cepat Chat Masuk -->
                <a href="{{ route('user.chat.index') }}" class="p-1.5 sm:p-2 text-gray-600 hover:text-bjbo-primary hover:bg-gray-100 rounded-full transition" title="Kotak Masuk Chat">
                    <i class="bi bi-chat-dots text-base"></i>
                </a>

                <!-- Tombol Cepat Pesanan -->
                <a href="{{ route('user.pesanan.index') }}" class="p-1.5 sm:p-2 text-gray-600 hover:text-bjbo-primary hover:bg-gray-100 rounded-full transition" title="Pesanan & Transaksi">
                    <i class="bi bi-bag-check text-base"></i>
                </a>

                <!-- Tombol Kelola Toko (Hanya tampil jika user memiliki toko yang aktif) -->
                @if(Auth::check() && Auth::user()->hasActiveStore())
                    <a href="{{ route('user.profil') }}" class="bjbo-btn-primary text-xs sm:text-sm py-1.5 px-3 font-bold shadow-xs hidden sm:inline-flex items-center gap-1.5">
                        <i class="bi bi-shop"></i> <span>Kelola Toko</span>
                    </a>
                @endif

                <!-- Dropdown Profil Pengguna (Pure Tailwind & Vanilla JS) -->
                <div class="relative inline-block text-left" id="userMenuDropdownContainer">
                    <button type="button" class="flex items-center gap-1.5 py-1 px-1.5 sm:px-3 border border-bjbo-border-strong rounded-full hover:bg-bjbo-surface-subtle transition focus:outline-none" id="userMenuBtn">
                        <img src="{{ Auth::user()->profile_picture_url }}" alt="{{ Auth::user()->name }}" class="w-6 h-6 sm:w-7 sm:h-7 rounded-full object-cover border border-bjbo-border">
                        <span class="text-xs sm:text-sm font-semibold text-gray-800 hidden md:inline truncate max-w-[120px]">{{ Auth::user()->name }}</span>
                        <i class="bi bi-chevron-down text-[10px] text-gray-500"></i>
                    </button>

                <div class="hidden absolute right-0 mt-2 w-60 bg-white rounded-xl shadow-lg border border-bjbo-border p-2 z-50" id="userMenuDropdown">
                    <div class="flex items-center gap-2.5 px-2 py-2 border-b border-bjbo-border mb-1.5">
                        <img src="{{ Auth::user()->profile_picture_url }}" alt="{{ Auth::user()->name }}" class="w-9 h-9 rounded-full object-cover border border-bjbo-border">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                            <p class="text-[11px] text-gray-500 truncate">{{ Auth::user()->email }}</p>
                        </div>
                    </div>

                    <a class="flex items-center gap-2 py-2 px-3 text-xs font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition" href="{{ route('user.profil.index') }}">
                        <i class="bi bi-person-circle text-bjbo-primary"></i> Profil Saya
                    </a>

                    @if(Auth::user()->hasActiveStore())
                        <a class="flex items-center gap-2 py-2 px-3 text-xs font-semibold text-blue-700 bg-blue-50/70 rounded-lg hover:bg-blue-100 transition" href="{{ route('user.profil') }}">
                            <i class="bi bi-shop text-blue-600"></i> Kelola Toko Saya
                        </a>
                    @elseif(Auth::user()->store && Auth::user()->store->status === 'menunggu_verifikasi')
                        <a class="flex items-center justify-between py-2 px-3 text-xs font-semibold text-amber-800 bg-amber-50/80 rounded-lg hover:bg-amber-100 transition" href="{{ route('user.toko.daftar') }}">
                            <span class="flex items-center gap-2"><i class="bi bi-hourglass-split text-amber-600"></i> Status Toko</span>
                            <span class="text-[10px] bg-amber-200 text-amber-900 px-1.5 py-0.5 rounded font-bold">Menunggu</span>
                        </a>
                    @else
                        <a class="flex items-center gap-2 py-2 px-3 text-xs font-bold text-amber-800 bg-amber-50/90 border border-amber-200 rounded-lg hover:bg-amber-100 transition shadow-xs" href="{{ route('user.toko.daftar') }}">
                            <i class="bi bi-shop-window text-amber-600"></i> Buka / Daftar Toko
                        </a>
                    @endif

                    <a class="flex items-center gap-2 py-2 px-3 text-xs font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition" href="{{ route('user.rekber.index') }}">
                        <i class="bi bi-wallet2 text-emerald-600"></i> Dompet Rekber & Tarik Dana
                    </a>

                    <a class="flex items-center gap-2 py-2 px-3 text-xs font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition" href="{{ route('user.notifikasi.index') }}">
                        <i class="bi bi-bell text-blue-600"></i> Notifikasi
                    </a>

                    <a class="flex items-center gap-2 py-2 px-3 text-xs font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition" href="{{ route('user.chat.index') }}">
                        <i class="bi bi-chat-dots text-emerald-600"></i> Kotak Masuk Chat
                    </a>

                    <a class="flex items-center gap-2 py-2 px-3 text-xs font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition" href="{{ route('user.wishlist.index') }}">
                        <i class="bi bi-bookmark-heart text-pink-500"></i> Wishlist Saya
                    </a>

                    <a class="flex items-center gap-2 py-2 px-3 text-xs font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition" href="{{ route('user.pesanan.index') }}">
                        <i class="bi bi-receipt text-bjbo-primary"></i> Riwayat Pesanan
                    </a>

                    <a class="flex items-center gap-2 py-2 px-3 text-xs font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition" href="{{ route('about') }}">
                        <i class="bi bi-info-circle text-gray-500"></i> Tentang BJBO
                    </a>

                    <div class="border-t border-bjbo-border my-1"></div>

                    <a class="flex items-center gap-2 py-2 px-3 text-xs font-medium text-red-600 rounded-lg hover:bg-red-50 transition" href="{{ route('user.logout') }}">
                        <i class="bi bi-box-arrow-right"></i> Keluar
                    </a>
                </div>
            </div>
        </div>

        <!-- Form Pencarian Khusus Handphone (< md) -->
        <div class="md:hidden pt-2">
            <form action="{{ route('postingan.cari') }}" method="GET" class="relative w-full">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" name="keyword" class="w-full h-8 pl-9 pr-3 text-xs bg-bjbo-surface-subtle border border-bjbo-border-strong rounded-full focus:bg-white focus:border-bjbo-primary focus:outline-none transition" placeholder="Cari barang atau penjual..." value="{{ request('keyword') }}">
            </form>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.getElementById('userMenuBtn');
        const menu = document.getElementById('userMenuDropdown');
        if (btn && menu) {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                menu.classList.toggle('hidden');
            });
            document.addEventListener('click', function (e) {
                if (!menu.contains(e.target) && !btn.contains(e.target)) {
                    menu.classList.add('hidden');
                }
            });
        }
    });
</script>
