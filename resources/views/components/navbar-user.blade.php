<!-- Header Navigasi Pengguna Terdaftar (Tokopedia Style Popovers) -->
<header class="bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 sticky top-0 z-50 shadow-xs py-2.5 sm:py-3 transition-colors">
    <div class="container mx-auto px-3 sm:px-4">
        <div class="flex items-center justify-between gap-2 sm:gap-4">
            <!-- 1. Logo Brand (Diperbesar 80-85% dari navbar) -->
            <a href="{{ route('user.dashboard') }}" class="flex items-center text-decoration-none flex-shrink-0 anim-press">
                <img src="{{ asset('images/logo.png') }}" alt="BJBO Logo" class="h-11 sm:h-12 md:h-14 w-auto object-contain">
            </a>

            <!-- 2. Shortcut Kategori (Tokopedia Style) -->
            <a href="{{ route('layout') }}" class="hidden lg:inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-700 dark:text-zinc-200 hover:text-bjbo-primary px-2.5 py-1.5 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition anim-press flex-shrink-0">
                <span>Kategori</span>
                <i class="bi bi-chevron-down text-[10px]"></i>
            </a>

            <!-- 3. Form Pencarian Publik (Tablet & Laptop / Desktop: md:flex) -->
            <form action="{{ route('postingan.cari') }}" method="GET" class="hidden md:flex relative flex-grow max-w-lg mx-2 lg:mx-4">
                <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400 text-sm"></i>
                <input type="text" name="keyword" class="w-full h-10 pl-11 pr-4 text-xs sm:text-sm bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-300 dark:border-zinc-700 rounded-full focus:bg-white dark:focus:bg-zinc-900 text-zinc-900 dark:text-zinc-100 focus:border-bjbo-primary focus:ring-2 focus:ring-bjbo-primary/20 focus:outline-none transition" placeholder="Cari di BJBO Sedagho..." value="{{ request('keyword') }}">
            </form>

            <!-- 4. Aksi Pengguna Dikelompokkan Rata Kanan (Tokopedia Style) -->
            <div class="ml-auto flex items-center gap-1 sm:gap-2 flex-shrink-0">
                @php
                    $userId = Auth::id();
                    $cartCount = \App\Models\Cart::where('user_id', $userId)->sum('quantity');

                    // Data Notifikasi
                    $unreadNotifCount = \App\Models\AppNotification::where('user_id', $userId)->where('is_read', false)->count();
                    $recentNotifications = \App\Models\AppNotification::where('user_id', $userId)->latest()->take(4)->get();

                    // Data Pesanan Pembelian untuk Tab Transaksi (Sesuai Gambar 3)
                    $orderCounts = [
                        'menunggu_konfirmasi' => \App\Models\Pesanan::where('user_id', $userId)->where('shipping_status', 'menunggu_konfirmasi')->count(),
                        'diproses' => \App\Models\Pesanan::where('user_id', $userId)->where('shipping_status', 'diproses')->count(),
                        'sedang_dikirim' => \App\Models\Pesanan::where('user_id', $userId)->where('shipping_status', 'sedang_dikirim')->count(),
                        'sampai_tujuan' => \App\Models\Pesanan::where('user_id', $userId)->where('shipping_status', 'sampai_tujuan')->count(),
                    ];

                    // Data Chat & Ulasan
                    $unreadChatCount = \App\Models\Message::where('receiver_id', $userId)->where('is_read', false)->count();
                    $recentChats = \App\Models\Message::where(function ($q) use ($userId) {
                        $q->where('receiver_id', $userId)->orWhere('sender_id', $userId);
                    })->with(['sender:id,name', 'receiver:id,name', 'postingan:id,name'])->latest('id')->take(3)->get();

                    $recentUlasans = \App\Models\Ulasan::where('user_id', $userId)
                        ->orWhereHas('postingan', fn ($q) => $q->where('user_id', $userId))
                        ->with(['user:id,name', 'postingan:id,name'])
                        ->latest('id')
                        ->take(3)
                        ->get();
                @endphp

                <!-- GRUP 1: KERANJANG BELANJA (CART) -->
                <div class="relative">
                    <a href="{{ route('user.cart.index') }}" class="p-2 text-zinc-700 dark:text-zinc-200 hover:text-bjbo-primary hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-full transition anim-press relative flex items-center justify-center" title="Keranjang Belanja">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span id="cartBadge" class="{{ $cartCount > 0 ? '' : 'hidden' }} absolute top-0.5 right-0.5 bg-red-600 text-white text-[10px] font-bold px-1.5 py-0.2 rounded-full min-w-[16px] text-center leading-tight shadow-xs">
                            {{ $cartCount > 99 ? '99+' : $cartCount }}
                        </span>
                    </a>
                </div>

                <!-- GRUP 2: NOTIFIKASI DROPDOWN POPOVER (SESUAI GAMBAR 3) -->
                <div class="relative" id="notifDropdownContainer">
                    <button type="button" id="notifToggleBtn" class="p-2 text-zinc-700 dark:text-zinc-200 hover:text-bjbo-primary hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-full transition anim-press relative flex items-center justify-center focus:outline-none" title="Notifikasi Transaksi & Update">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span id="notifBadge" class="{{ $unreadNotifCount > 0 ? '' : 'hidden' }} absolute top-0.5 right-0.5 bg-red-600 text-white text-[10px] font-bold px-1.5 py-0.2 rounded-full min-w-[16px] text-center leading-tight shadow-xs">
                            {{ $unreadNotifCount > 99 ? '99+' : $unreadNotifCount }}
                        </span>
                    </button>

                    <!-- Popover Card Notifikasi (Persis Gambar 3 dengan Animasi CSS) -->
                    <div id="notifPopover" class="popover-dropdown popover-hidden absolute right-0 mt-2 w-80 sm:w-96 bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-800 z-50 overflow-hidden text-zinc-900 dark:text-zinc-100">
                        <!-- Header Popover -->
                        <div class="p-3.5 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                            <h3 class="font-bold text-sm sm:text-base flex items-center gap-1.5">
                                Notifikasi
                            </h3>
                            <a href="{{ route('user.notifikasi.index') }}" class="text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition" title="Pengaturan Notifikasi">
                                <i class="bi bi-gear text-sm"></i>
                            </a>
                        </div>

                        <!-- Tab Header: Transaksi | Update (Gambar 3) -->
                        <div class="flex border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50/60 dark:bg-zinc-800/40 text-xs font-semibold">
                            <button type="button" id="tabTransaksiBtn" class="flex-1 py-2.5 text-center border-b-2 border-emerald-600 text-emerald-700 dark:text-emerald-400 font-bold transition">
                                Transaksi
                            </button>
                            <button type="button" id="tabUpdateBtn" class="flex-1 py-2.5 text-center border-b-2 border-transparent text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 transition">
                                Update
                            </button>
                        </div>

                        <!-- Tab Body 1: Transaksi (Pembelian & Penjualan) -->
                        <div id="tabTransaksiContent" class="p-3.5 space-y-4 max-h-[380px] overflow-y-auto">
                            <!-- Pembelian -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Pembelian</span>
                                    <a href="{{ route('user.pesanan.index') }}" class="text-[11px] font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400">
                                        Lihat Semua
                                    </a>
                                </div>
                                <p class="text-[11px] text-zinc-400 mb-2">Status Pesanan</p>

                                <!-- 4 Status Icons Grid (Sesuai Gambar 3) -->
                                <div class="grid grid-cols-4 gap-1.5 text-center">
                                    <a href="{{ route('user.pesanan.index', ['status' => 'menunggu_konfirmasi']) }}" class="p-2 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition anim-press flex flex-col items-center">
                                        <div class="relative text-zinc-700 dark:text-zinc-300">
                                            <i class="bi bi-clock-history text-lg"></i>
                                            @if($orderCounts['menunggu_konfirmasi'] > 0)
                                                <span class="absolute -top-1 -right-2 bg-emerald-600 text-white text-[9px] font-bold px-1 rounded-full">
                                                    {{ $orderCounts['menunggu_konfirmasi'] }}
                                                </span>
                                            @endif
                                        </div>
                                        <span class="text-[10px] text-zinc-600 dark:text-zinc-400 leading-tight mt-1">Menunggu Konfirmasi</span>
                                    </a>

                                    <a href="{{ route('user.pesanan.index', ['status' => 'diproses']) }}" class="p-2 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition anim-press flex flex-col items-center">
                                        <div class="relative text-zinc-700 dark:text-zinc-300">
                                            <i class="bi bi-arrow-repeat text-lg"></i>
                                            @if($orderCounts['diproses'] > 0)
                                                <span class="absolute -top-1 -right-2 bg-emerald-600 text-white text-[9px] font-bold px-1 rounded-full">
                                                    {{ $orderCounts['diproses'] }}
                                                </span>
                                            @endif
                                        </div>
                                        <span class="text-[10px] text-zinc-600 dark:text-zinc-400 leading-tight mt-1">Pesanan Diproses</span>
                                    </a>

                                    <a href="{{ route('user.pesanan.index', ['status' => 'sedang_dikirim']) }}" class="p-2 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition anim-press flex flex-col items-center">
                                        <div class="relative text-zinc-700 dark:text-zinc-300">
                                            <i class="bi bi-truck text-lg"></i>
                                            @if($orderCounts['sedang_dikirim'] > 0)
                                                <span class="absolute -top-1 -right-2 bg-emerald-600 text-white text-[9px] font-bold px-1 rounded-full">
                                                    {{ $orderCounts['sedang_dikirim'] }}
                                                </span>
                                            @endif
                                        </div>
                                        <span class="text-[10px] text-zinc-600 dark:text-zinc-400 leading-tight mt-1">Sedang Dikirim</span>
                                    </a>

                                    <a href="{{ route('user.pesanan.index', ['status' => 'sampai_tujuan']) }}" class="p-2 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition anim-press flex flex-col items-center">
                                        <div class="relative text-zinc-700 dark:text-zinc-300">
                                            <i class="bi bi-geo-alt text-lg"></i>
                                            @if($orderCounts['sampai_tujuan'] > 0)
                                                <span class="absolute -top-1 -right-2 bg-emerald-600 text-white text-[9px] font-bold px-1 rounded-full">
                                                    {{ $orderCounts['sampai_tujuan'] }}
                                                </span>
                                            @endif
                                        </div>
                                        <span class="text-[10px] text-zinc-600 dark:text-zinc-400 leading-tight mt-1">Sampai Tujuan</span>
                                    </a>
                                </div>
                            </div>

                            <!-- Penjualan (Sesuai Gambar 3) -->
                            <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800">
                                <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 mb-1">Penjualan</h4>
                                <p class="text-[11px] text-zinc-500 leading-relaxed mb-2.5">
                                    Cek pesanan yang masuk dan perkembangan tokomu secara rutin di satu tempat
                                </p>
                                @if(Auth::user()->hasActiveStore())
                                    <a href="{{ route('user.profil') }}" class="w-full inline-flex items-center justify-center py-2 px-3 rounded-xl text-xs font-bold border border-emerald-600 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 transition anim-press">
                                        Masuk ke Dashboard Toko
                                    </a>
                                @else
                                    <a href="{{ route('user.toko.daftar') }}" class="w-full inline-flex items-center justify-center py-2 px-3 rounded-xl text-xs font-bold border border-emerald-600 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 transition anim-press">
                                        Buka Toko BJBO Sedagho
                                    </a>
                                @endif
                            </div>

                            <!-- Untuk Kamu (Sesuai Gambar 3) -->
                            <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800">
                                <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 mb-2">Untuk Kamu</h4>
                                @if($recentNotifications->isNotEmpty())
                                    @php $topNotif = $recentNotifications->first(); @endphp
                                    <div class="p-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-100 dark:border-zinc-800">
                                        <div class="flex items-center gap-1.5 text-[10px] text-emerald-600 dark:text-emerald-400 font-bold mb-1">
                                            <i class="bi bi-bell"></i>
                                            <span>{{ $topNotif->type ?? 'Notifikasi' }}</span>
                                            <span class="text-zinc-400 font-normal">&bull; {{ $topNotif->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-xs font-semibold text-zinc-800 dark:text-zinc-200 line-clamp-1">{{ $topNotif->title }}</p>
                                        <p class="text-[11px] text-zinc-500 line-clamp-2 mt-0.5">{{ $topNotif->message }}</p>
                                    </div>
                                @else
                                    <p class="text-[11px] text-zinc-400">Belum ada pembaruan transaksi terbaru.</p>
                                @endif
                            </div>
                        </div>

                        <!-- Tab Body 2: Update (Daftar Notifikasi) -->
                        <div id="tabUpdateContent" class="p-3.5 space-y-2.5 max-h-[380px] overflow-y-auto hidden">
                            @if($recentNotifications->isEmpty())
                                <div class="text-center py-8 text-zinc-400 text-xs">
                                    <i class="bi bi-bell-slash text-2xl mb-1 block"></i>
                                    <span>Tidak ada notifikasi baru saat ini.</span>
                                </div>
                            @else
                                @foreach($recentNotifications as $notif)
                                    <div class="p-2.5 rounded-xl {{ $notif->is_read ? 'bg-zinc-50 dark:bg-zinc-800/40' : 'bg-blue-50/60 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-900' }} transition">
                                        <div class="flex items-center justify-between text-[10px] text-zinc-400 mb-1">
                                            <span class="font-bold text-bjbo-primary">{{ $notif->type }}</span>
                                            <span>{{ $notif->created_at->diffForHumans() }}</span>
                                        </div>
                                        <h5 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 mb-0.5">{{ $notif->title }}</h5>
                                        <p class="text-[11px] text-zinc-600 dark:text-zinc-400 leading-snug">{{ $notif->message }}</p>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <!-- Footer Popover Notifikasi (Sesuai Gambar 3) -->
                        <div class="p-3 border-t border-zinc-100 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-800/40 flex items-center justify-between text-xs font-semibold">
                            <form action="{{ route('user.notifikasi.readAll') }}" method="POST" class="inline m-0">
                                @csrf
                                <button type="submit" class="text-emerald-700 dark:text-emerald-400 hover:underline text-xs">
                                    Tandai semua dibaca
                                </button>
                            </form>
                            <a href="{{ route('user.notifikasi.index') }}" class="text-emerald-700 dark:text-emerald-400 hover:underline text-xs">
                                Lihat selengkapnya
                            </a>
                        </div>
                    </div>
                </div>

                <!-- GRUP 3: PESAN / CHAT & ULASAN DROPDOWN POPOVER (Disatukan Sesuai Permintaan) -->
                <div class="relative" id="chatDropdownContainer">
                    <button type="button" id="chatToggleBtn" class="p-2 text-zinc-700 dark:text-zinc-200 hover:text-bjbo-primary hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-full transition anim-press relative flex items-center justify-center focus:outline-none" title="Pesan Chat & Ulasan">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <span id="chatBadge" class="{{ $unreadChatCount > 0 ? '' : 'hidden' }} absolute top-0.5 right-0.5 bg-red-600 text-white text-[10px] font-bold px-1.5 py-0.2 rounded-full min-w-[16px] text-center leading-tight shadow-xs">
                            {{ $unreadChatCount > 99 ? '99+' : $unreadChatCount }}
                        </span>
                    </button>

                    <!-- Popover Card Chat & Ulasan (Animasi CSS Scale from 0.97) -->
                    <div id="chatPopover" class="popover-dropdown popover-hidden absolute right-0 mt-2 w-80 sm:w-96 bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-800 z-50 overflow-hidden text-zinc-900 dark:text-zinc-100">
                        <!-- Header Popover -->
                        <div class="p-3.5 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                            <h3 class="font-bold text-sm sm:text-base flex items-center gap-1.5">
                                Kotak Masuk
                            </h3>
                            <a href="{{ route('user.chat.index') }}" class="text-[11px] font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400">
                                Buka Semua
                            </a>
                        </div>

                        <!-- Tab Header: Chat | Ulasan -->
                        <div class="flex border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50/60 dark:bg-zinc-800/40 text-xs font-semibold">
                            <button type="button" id="tabChatBtn" class="flex-1 py-2.5 text-center border-b-2 border-emerald-600 text-emerald-700 dark:text-emerald-400 font-bold transition">
                                Chat
                            </button>
                            <button type="button" id="tabUlasanBtn" class="flex-1 py-2.5 text-center border-b-2 border-transparent text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 transition">
                                Ulasan Produk
                            </button>
                        </div>

                        <!-- Tab Body 1: Chat Tanya Stok -->
                        <div id="tabChatContent" class="p-3.5 space-y-2.5 max-h-[360px] overflow-y-auto">
                            @if($recentChats->isEmpty())
                                <div class="text-center py-8 text-zinc-400 text-xs">
                                    <i class="bi bi-chat-dots text-2xl mb-1 block"></i>
                                    <span>Belum ada obrolan chat. Tanya ketersediaan stok barang pada penjual di etalase.</span>
                                </div>
                            @else
                                @foreach($recentChats as $chat)
                                    @php
                                        $otherUser = ($chat->sender_id === $userId) ? $chat->receiver : $chat->sender;
                                    @endphp
                                    <a href="{{ route('user.chat.room', ['otherUserId' => $otherUser->id ?? 1, 'postinganId' => $chat->postingan_id]) }}" class="block p-2.5 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800/60 border border-transparent hover:border-zinc-200 dark:hover:border-zinc-700 transition anim-press">
                                        <div class="flex items-center justify-between text-xs mb-1">
                                            <span class="font-bold text-zinc-900 dark:text-zinc-100 truncate">{{ $otherUser->name ?? 'Pengguna' }}</span>
                                            <span class="text-[10px] text-zinc-400">{{ $chat->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-[11px] text-zinc-500 line-clamp-1">{{ $chat->decrypted_message }}</p>
                                        @if($chat->postingan)
                                            <span class="inline-block mt-1 text-[10px] text-blue-600 bg-blue-50 dark:bg-blue-950/40 px-1.5 py-0.5 rounded font-medium truncate max-w-full">
                                                Produk: {{ $chat->postingan->name }}
                                            </span>
                                        @endif
                                    </a>
                                @endforeach
                            @endif
                        </div>

                        <!-- Tab Body 2: Ulasan Produk -->
                        <div id="tabUlasanContent" class="p-3.5 space-y-2.5 max-h-[360px] overflow-y-auto hidden">
                            @if($recentUlasans->isEmpty())
                                <div class="text-center py-8 text-zinc-400 text-xs">
                                    <i class="bi bi-star text-2xl mb-1 block"></i>
                                    <span>Belum ada ulasan barang. Belanja barang dan berikan ulasan Anda.</span>
                                </div>
                            @else
                                @foreach($recentUlasans as $ulasan)
                                    <div class="p-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-100 dark:border-zinc-800">
                                        <div class="flex items-center justify-between text-xs mb-1">
                                            <span class="font-bold text-zinc-800 dark:text-zinc-200">{{ $ulasan->user->name ?? 'Pelanggan' }}</span>
                                            <div class="flex items-center text-amber-500 text-[10px]">
                                                @foreach(range(1, 5) as $i)
                                                    <i class="bi {{ $i <= $ulasan->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                                @endforeach
                                            </div>
                                        </div>
                                        <p class="text-[11px] text-zinc-600 dark:text-zinc-300 italic">"{{ $ulasan->komentar }}"</p>
                                        @if($ulasan->postingan)
                                            <span class="text-[10px] text-zinc-400 block mt-1">Pada: {{ $ulasan->postingan->name }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <!-- Footer Popover Chat -->
                        <div class="p-3 border-t border-zinc-100 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-800/40 text-center text-xs font-semibold">
                            <a href="{{ route('user.chat.index') }}" class="text-emerald-700 dark:text-emerald-400 hover:underline">
                                Buka Kotak Masuk Chat & Ulasan &rarr;
                            </a>
                        </div>
                    </div>
                </div>

                <!-- GRUP 4: TOMBOL TOKO (PERSIS SEPERTI GAMBAR 1 TOKOPEDIA) -->
                @if(Auth::check() && Auth::user()->hasActiveStore())
                    <a href="{{ route('user.profil') }}" class="hidden sm:inline-flex items-center gap-1.5 py-1.5 px-3 rounded-full text-xs font-semibold text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition anim-press" title="Kelola Toko Saya">
                        <i class="bi bi-shop text-sm"></i>
                        <span>Toko</span>
                    </a>
                @else
                    <a href="{{ route('user.toko.daftar') }}" class="hidden sm:inline-flex items-center gap-1.5 py-1.5 px-3 rounded-full text-xs font-semibold text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition anim-press" title="Buka / Daftar Toko">
                        <i class="bi bi-shop-window text-sm"></i>
                        <span>Toko</span>
                        <span class="sr-only">Buka / Daftar Toko</span>
                    </a>
                @endif

                <!-- PEMISAH VERTIKAL (DIVIDER) -->
                <div class="h-5 w-px bg-zinc-200 dark:bg-zinc-700 mx-1 hidden sm:block"></div>

                <!-- GRUP 5: PROFIL PENGGUNA RATA KANAN (TOKOPEDIA STYLE DROPDOWN) -->
                <div class="relative inline-block text-left" id="userMenuDropdownContainer">
                    <button type="button" class="flex items-center gap-2 py-1 px-1.5 sm:px-2.5 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 transition anim-press focus:outline-none" id="userMenuBtn">
                        <img src="{{ Auth::user()->profile_picture_url }}" alt="{{ Auth::user()->name }}" class="w-7 h-7 sm:w-8 sm:h-8 rounded-full object-cover border border-zinc-200 dark:border-zinc-700">
                        <span class="text-xs sm:text-sm font-semibold text-zinc-800 dark:text-zinc-200 hidden md:inline truncate max-w-[120px]">{{ Auth::user()->name }}</span>
                        <svg class="w-3.5 h-3.5 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Popover Dropdown Profil -->
                    <div id="userMenuDropdown" class="popover-dropdown popover-hidden absolute right-0 mt-2 w-64 bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-800 p-2 z-50 text-zinc-900 dark:text-zinc-100">
                        <div class="flex items-center gap-2.5 px-3 py-2.5 border-b border-zinc-100 dark:border-zinc-800 mb-1.5">
                            <img src="{{ Auth::user()->profile_picture_url }}" alt="{{ Auth::user()->name }}" class="w-9 h-9 rounded-full object-cover border border-zinc-200 dark:border-zinc-700">
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold text-zinc-900 dark:text-zinc-100 truncate">{{ Auth::user()->name }}</p>
                                <p class="text-[11px] text-zinc-500 truncate">{{ Auth::user()->email }}</p>
                            </div>
                        </div>

                        <!-- Kartu Informasi Saldo (Hanya di Dropdown Profil / Halaman Profil) -->
                        <div class="bg-zinc-50 dark:bg-zinc-800/60 rounded-xl p-2.5 mb-2 border border-zinc-100 dark:border-zinc-800 space-y-1.5">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-zinc-500 text-[11px] flex items-center gap-1"><i class="bi bi-wallet2 text-blue-600"></i> BJPay</span>
                                <a href="{{ route('user.bjpay.index') }}" class="font-mono font-bold text-blue-600 dark:text-blue-400 hover:underline">
                                    {{ Auth::user()->formatted_saldo_bjpay ?? 'Rp0' }}
                                </a>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-zinc-500 text-[11px] flex items-center gap-1"><i class="bi bi-shield-check text-emerald-600"></i> Rekber</span>
                                <a href="{{ route('user.rekber.index') }}" class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">
                                    Kelola Rekber &rsaquo;
                                </a>
                            </div>
                        </div>

                        <!-- Menu Pilihan Profil -->
                        <a class="flex items-center gap-2.5 py-2 px-3 text-xs font-medium text-zinc-700 dark:text-zinc-300 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition anim-press" href="{{ route('user.profil.index') }}">
                            <i class="bi bi-person-circle text-bjbo-primary text-sm"></i> Profil Saya
                        </a>

                        <a class="flex items-center gap-2.5 py-2 px-3 text-xs font-medium text-zinc-700 dark:text-zinc-300 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition anim-press" href="{{ route('user.pesanan.index') }}">
                            <i class="bi bi-bag-check text-blue-600 text-sm"></i> Pesanan & Transaksi
                        </a>

                        <a class="flex items-center gap-2.5 py-2 px-3 text-xs font-medium text-zinc-700 dark:text-zinc-300 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition anim-press" href="{{ route('user.wishlist.index') }}">
                            <i class="bi bi-bookmark-heart text-pink-500 text-sm"></i> Wishlist Saya
                        </a>

                        <a class="flex items-center gap-2.5 py-2 px-3 text-xs font-medium text-zinc-700 dark:text-zinc-300 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition anim-press" href="{{ route('user.favorit') }}">
                            <i class="bi bi-heart text-red-500 text-sm"></i> Barang Disukai (Favorit)
                        </a>

                        @if(Auth::user()->hasActiveStore())
                            <a class="flex items-center gap-2.5 py-2 px-3 text-xs font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 rounded-lg hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition anim-press" href="{{ route('user.profil') }}">
                                <i class="bi bi-shop text-emerald-600 text-sm"></i> Dashboard Toko Saya
                            </a>
                        @else
                            <a class="flex items-center gap-2.5 py-2 px-3 text-xs font-bold text-amber-800 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-lg hover:bg-amber-100 transition shadow-xs anim-press" href="{{ route('user.toko.daftar') }}">
                                <i class="bi bi-shop-window text-amber-600 text-sm"></i> Buka Toko BJBO
                            </a>
                        @endif

                        <div class="border-t border-zinc-100 dark:border-zinc-800 my-1"></div>

                        <a class="flex items-center gap-2.5 py-2 px-3 text-xs font-medium text-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-950/30 transition anim-press" href="{{ route('user.logout') }}">
                            <i class="bi bi-box-arrow-right text-sm"></i> Keluar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Pencarian Khusus Mobile (< md) -->
        <div class="md:hidden pt-2">
            <form action="{{ route('postingan.cari') }}" method="GET" class="relative w-full">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-zinc-400 text-xs"></i>
                <input type="text" name="keyword" class="w-full h-8 pl-9 pr-3 text-xs bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-full focus:bg-white dark:focus:bg-zinc-900 text-zinc-900 dark:text-zinc-100 focus:border-bjbo-primary focus:outline-none transition" placeholder="Cari di BJBO Sedagho..." value="{{ request('keyword') }}">
            </form>
        </div>
    </div>
</header>

<!-- JavaScript Interaktif Popovers & Tab Switching Bebas Looping -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const notifBtn = document.getElementById('notifToggleBtn');
    const notifPopover = document.getElementById('notifPopover');
    const chatBtn = document.getElementById('chatToggleBtn');
    const chatPopover = document.getElementById('chatPopover');
    const userBtn = document.getElementById('userMenuBtn');
    const userMenu = document.getElementById('userMenuDropdown');

    const popovers = [
        { btn: notifBtn, menu: notifPopover },
        { btn: chatBtn, menu: chatPopover },
        { btn: userBtn, menu: userMenu }
    ];

    const closeAllPopovers = function () {
        popovers.map(function (item) {
            if (item.menu) {
                item.menu.classList.remove('popover-visible');
                item.menu.classList.add('popover-hidden');
            }
            return item;
        });
    };

    const togglePopover = function (targetMenu, targetBtn) {
        if (!targetMenu) return;
        const isOpen = targetMenu.classList.contains('popover-visible');
        closeAllPopovers();
        if (!isOpen) {
            targetMenu.classList.remove('popover-hidden');
            targetMenu.classList.add('popover-visible');
        }
    };

    if (notifBtn && notifPopover) {
        notifBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            togglePopover(notifPopover, notifBtn);
        });
    }

    if (chatBtn && chatPopover) {
        chatBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            togglePopover(chatPopover, chatBtn);
        });
    }

    if (userBtn && userMenu) {
        userBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            togglePopover(userMenu, userBtn);
        });
    }

    // Tutup saat klik di luar popover
    document.addEventListener('click', function (e) {
        const clickedInside = popovers.some(function (item) {
            return (item.menu && item.menu.contains(e.target)) || (item.btn && item.btn.contains(e.target));
        });
        if (!clickedInside) {
            closeAllPopovers();
        }
    });

    // Tab Switching Notifikasi: Transaksi | Update (Gambar 3)
    const tabTransaksiBtn = document.getElementById('tabTransaksiBtn');
    const tabUpdateBtn = document.getElementById('tabUpdateBtn');
    const tabTransaksiContent = document.getElementById('tabTransaksiContent');
    const tabUpdateContent = document.getElementById('tabUpdateContent');

    if (tabTransaksiBtn && tabUpdateBtn) {
        tabTransaksiBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            tabTransaksiBtn.className = 'flex-1 py-2.5 text-center border-b-2 border-emerald-600 text-emerald-700 dark:text-emerald-400 font-bold transition';
            tabUpdateBtn.className = 'flex-1 py-2.5 text-center border-b-2 border-transparent text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 transition';
            tabTransaksiContent.classList.remove('hidden');
            tabUpdateContent.classList.add('hidden');
        });

        tabUpdateBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            tabUpdateBtn.className = 'flex-1 py-2.5 text-center border-b-2 border-emerald-600 text-emerald-700 dark:text-emerald-400 font-bold transition';
            tabTransaksiBtn.className = 'flex-1 py-2.5 text-center border-b-2 border-transparent text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 transition';
            tabUpdateContent.classList.remove('hidden');
            tabTransaksiContent.classList.add('hidden');
        });
    }

    // Tab Switching Chat: Chat | Ulasan
    const tabChatBtn = document.getElementById('tabChatBtn');
    const tabUlasanBtn = document.getElementById('tabUlasanBtn');
    const tabChatContent = document.getElementById('tabChatContent');
    const tabUlasanContent = document.getElementById('tabUlasanContent');

    if (tabChatBtn && tabUlasanBtn) {
        tabChatBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            tabChatBtn.className = 'flex-1 py-2.5 text-center border-b-2 border-emerald-600 text-emerald-700 dark:text-emerald-400 font-bold transition';
            tabUlasanBtn.className = 'flex-1 py-2.5 text-center border-b-2 border-transparent text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 transition';
            tabChatContent.classList.remove('hidden');
            tabUlasanContent.classList.add('hidden');
        });

        tabUlasanBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            tabUlasanBtn.className = 'flex-1 py-2.5 text-center border-b-2 border-emerald-600 text-emerald-700 dark:text-emerald-400 font-bold transition';
            tabChatBtn.className = 'flex-1 py-2.5 text-center border-b-2 border-transparent text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 transition';
            tabUlasanContent.classList.remove('hidden');
            tabChatContent.classList.add('hidden');
        });
    }
});
</script>
