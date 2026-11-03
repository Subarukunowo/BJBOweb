<!-- Header Navigasi Tamu / Pengunjung -->
<header class="bg-white border-b border-bjbo-border sticky top-0 z-50 shadow-xs py-2.5 sm:py-3">
    <div class="container mx-auto px-3 sm:px-4">
        <div class="flex items-center justify-between gap-2 sm:gap-4">
            <!-- Logo Brand -->
            <a href="{{ route('layout') }}" class="flex items-center text-decoration-none flex-shrink-0">
                <img src="{{ asset('images/logo.png') }}" alt="BJBO Logo" class="h-8 sm:h-10 w-auto object-contain">
            </a>

            <!-- Form Pencarian Publik (Tablet & Laptop / Desktop: md:flex) -->
            <form action="{{ route('postingan.cari') }}" method="GET" class="hidden md:flex relative flex-grow max-w-lg mx-2 lg:mx-4">
                <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" name="keyword" class="w-full h-10 pl-11 pr-4 text-xs sm:text-sm bg-bjbo-surface-subtle border border-bjbo-border-strong rounded-full focus:bg-white focus:border-bjbo-primary focus:ring-2 focus:ring-bjbo-primary/20 focus:outline-none transition" placeholder="Cari barang bekas atau baru di sekitar Anda..." value="{{ request('keyword') }}">
            </form>

            <!-- Tombol Aksi Pengguna -->
            <div class="flex items-center gap-1.5 sm:gap-2 flex-shrink-0">
                <a href="{{ route('login') }}" class="bjbo-btn-outline text-xs py-1.5 px-2.5 sm:py-2 sm:px-4">
                    <i class="bi bi-box-arrow-in-right"></i> <span class="hidden xs:inline">Masuk</span>
                </a>
                <a href="{{ route('register') }}" class="bjbo-btn-primary text-xs py-1.5 px-2.5 sm:py-2 sm:px-4 hidden sm:inline-flex">
                    Daftar
                </a>
                <a href="{{ route('login') }}" class="bjbo-btn-accent text-xs py-1.5 px-2.5 sm:py-2 sm:px-4 font-bold">
                    <i class="bi bi-plus-circle"></i> Jual
                </a>
            </div>
        </div>

        <!-- Form Pencarian Khusus Handphone (< md) -->
        <div class="md:hidden pt-2">
            <form action="{{ route('postingan.cari') }}" method="GET" class="relative w-full">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" name="keyword" class="w-full h-8 pl-9 pr-3 text-xs bg-bjbo-surface-subtle border border-bjbo-border-strong rounded-full focus:bg-white focus:border-bjbo-primary focus:outline-none transition" placeholder="Cari barang atau kebutuhan..." value="{{ request('keyword') }}">
            </form>
        </div>
    </div>
</header>
