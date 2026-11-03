@php
    $currentRoute = Route::currentRouteName();
    $isUser = Auth::check();
@endphp

<!-- Mobile Bottom Navigation (Khusus Handphone & Tablet Kecil) -->
<nav class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-bjbo-border shadow-lg px-2 py-1 flex items-center justify-around safe-area-bottom">
    @if($isUser)
        <!-- Beranda User -->
        <a href="{{ route('user.dashboard') }}" class="flex flex-col items-center justify-center py-1 px-1.5 min-w-[52px] text-[10px] font-medium transition {{ $currentRoute === 'user.dashboard' ? 'text-bjbo-primary font-bold' : 'text-gray-500 hover:text-gray-900' }}">
            <i class="bi {{ $currentRoute === 'user.dashboard' ? 'bi-house-door-fill text-lg text-bjbo-primary' : 'bi-house-door text-lg' }}"></i>
            <span class="truncate">Beranda</span>
        </a>

        <!-- Chat Sedagho -->
        <a href="{{ route('user.chat.index') }}" class="flex flex-col items-center justify-center py-1 px-1.5 min-w-[52px] text-[10px] font-medium transition {{ str_starts_with($currentRoute ?? '', 'user.chat') ? 'text-bjbo-primary font-bold' : 'text-gray-500 hover:text-gray-900' }}">
            <i class="bi {{ str_starts_with($currentRoute ?? '', 'user.chat') ? 'bi-chat-dots-fill text-lg text-bjbo-primary' : 'bi-chat-dots text-lg' }}"></i>
            <span class="truncate">Chat</span>
        </a>

        <!-- Floating Elevated Tombol Toko -->
        @if(Auth::user()->hasActiveStore())
            <a href="{{ route('user.profil') }}" class="flex flex-col items-center justify-center -mt-5 group" title="Kelola Toko Saya">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-bjbo-primary text-white flex items-center justify-center shadow-lg group-active:scale-95 transition-transform duration-150 border-2 border-white">
                    <i class="bi bi-shop text-lg sm:text-xl font-bold"></i>
                </div>
                <span class="text-[10px] font-bold text-bjbo-primary mt-0.5">Toko</span>
            </a>
        @else
            <a href="{{ route('user.toko.daftar') }}" class="flex flex-col items-center justify-center -mt-5 group" title="Buka Toko di BJBO">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-gradient-to-r from-amber-500 to-amber-600 text-white flex items-center justify-center shadow-lg group-active:scale-95 transition-transform duration-150 border-2 border-white">
                    <i class="bi bi-shop-window text-lg sm:text-xl font-bold"></i>
                </div>
                <span class="text-[10px] font-bold text-amber-700 mt-0.5">Buka Toko</span>
            </a>
        @endif

        <!-- Pesanan -->
        <a href="{{ route('user.pesanan.index') }}" class="flex flex-col items-center justify-center py-1 px-1.5 min-w-[52px] text-[10px] font-medium transition {{ str_starts_with($currentRoute ?? '', 'user.pesanan') ? 'text-bjbo-primary font-bold' : 'text-gray-500 hover:text-gray-900' }}">
            <i class="bi {{ str_starts_with($currentRoute ?? '', 'user.pesanan') ? 'bi-bag-check-fill text-lg text-bjbo-primary' : 'bi-bag-check text-lg' }}"></i>
            <span class="truncate">Pesanan</span>
        </a>

        <!-- Seller Center / Profil -->
        <a href="{{ route('user.profil') }}" class="flex flex-col items-center justify-center py-1 px-1.5 min-w-[52px] text-[10px] font-medium transition {{ $currentRoute === 'user.profil' ? 'text-bjbo-primary font-bold' : 'text-gray-500 hover:text-gray-900' }}">
            <i class="bi {{ $currentRoute === 'user.profil' ? 'bi-person-fill text-lg text-bjbo-primary' : 'bi-person text-lg' }}"></i>
            <span class="truncate">Akun</span>
        </a>
    @else
        <!-- Tamu: Beranda -->
        <a href="{{ route('layout') }}" class="flex flex-col items-center justify-center py-1 px-1.5 min-w-[52px] text-[10px] font-medium transition {{ $currentRoute === 'layout' ? 'text-bjbo-primary font-bold' : 'text-gray-500 hover:text-gray-900' }}">
            <i class="bi {{ $currentRoute === 'layout' ? 'bi-house-door-fill text-lg text-bjbo-primary' : 'bi-house-door text-lg' }}"></i>
            <span class="truncate">Beranda</span>
        </a>

        <!-- Tentang BJBO -->
        <a href="{{ route('about') }}" class="flex flex-col items-center justify-center py-1 px-1.5 min-w-[52px] text-[10px] font-medium transition {{ $currentRoute === 'about' ? 'text-bjbo-primary font-bold' : 'text-gray-500 hover:text-gray-900' }}">
            <i class="bi {{ $currentRoute === 'about' ? 'bi-info-circle-fill text-lg text-bjbo-primary' : 'bi-info-circle text-lg' }}"></i>
            <span class="truncate">Tentang</span>
        </a>

        <!-- Floating Elevated Tombol Jual (Tamu) -->
        <a href="{{ route('login') }}" class="flex flex-col items-center justify-center -mt-5 group" title="Mulai Jual Barang">
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-bjbo-accent text-white flex items-center justify-center shadow-lg group-active:scale-95 transition-transform duration-150 border-2 border-white">
                <i class="bi bi-camera text-base sm:text-lg"></i>
            </div>
            <span class="text-[10px] font-bold text-bjbo-accent mt-0.5">Jual</span>
        </a>

        <!-- Masuk -->
        <a href="{{ route('login') }}" class="flex flex-col items-center justify-center py-1 px-1.5 min-w-[52px] text-[10px] font-medium transition {{ $currentRoute === 'login' ? 'text-bjbo-primary font-bold' : 'text-gray-500 hover:text-gray-900' }}">
            <i class="bi {{ $currentRoute === 'login' ? 'bi-box-arrow-in-right text-lg text-bjbo-primary' : 'bi-box-arrow-in-right text-lg' }}"></i>
            <span class="truncate">Masuk</span>
        </a>

        <!-- Daftar -->
        <a href="{{ route('register') }}" class="flex flex-col items-center justify-center py-1 px-1.5 min-w-[52px] text-[10px] font-medium transition {{ $currentRoute === 'register' ? 'text-bjbo-primary font-bold' : 'text-gray-500 hover:text-gray-900' }}">
            <i class="bi {{ $currentRoute === 'register' ? 'bi-person-plus-fill text-lg text-bjbo-primary' : 'bi-person-plus text-lg' }}"></i>
            <span class="truncate">Daftar</span>
        </a>
    @endif
</nav>
