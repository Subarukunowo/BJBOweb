@php
    $currentRoute = Route::currentRouteName();
@endphp

<!-- Mobile Backdrop -->
<div id="admin-sidebar-backdrop" class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs z-40 hidden lg:hidden" onclick="toggleAdminSidebar()"></div>

<!-- Sidebar Aside -->
<aside id="admin-sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-bjbo-primary text-white flex flex-col justify-between transform -translate-x-full lg:translate-x-0 lg:static lg:z-auto transition-transform duration-200 ease-in-out shrink-0 shadow-lg lg:shadow-none">
    <div>
        <!-- Brand Header -->
        <div class="h-16 flex items-center justify-between px-6 border-b border-blue-900/60 bg-blue-950/40">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="BJBO Logo" class="h-8 w-auto object-contain brightness-0 invert">
                <div>
                    <span class="text-sm font-bold tracking-tight text-white block">BJBO Panel</span>
                    <span class="text-[10px] text-blue-300 font-mono tracking-wider uppercase">Admin Sedagho</span>
                </div>
            </a>
            <button type="button" onclick="toggleAdminSidebar()" class="lg:hidden text-blue-200 hover:text-white p-1">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>

        <!-- Navigation Menu -->
        <div class="p-4 space-y-1">
            <div class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-blue-300/70 font-mono">
                Menu Utama
            </div>

            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ $currentRoute === 'admin.dashboard' ? 'bg-white/15 text-white shadow-xs' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                <i class="bi bi-grid-1x2-fill text-sm text-amber-400"></i>
                <span>Dashboard</span>
            </a>

            <!-- Pengguna -->
            <a href="{{ route('admin.users') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ $currentRoute === 'admin.users' ? 'bg-white/15 text-white shadow-xs' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                <i class="bi bi-people-fill text-sm text-sky-400"></i>
                <span>Manajemen Pengguna</span>
            </a>

            <!-- Postingan & Auto-Block -->
            <a href="{{ route('admin.postings') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ $currentRoute === 'admin.postings' ? 'bg-white/15 text-white shadow-xs' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                <i class="bi bi-box-seam-fill text-sm text-emerald-400"></i>
                <span>Postingan & Kurasi</span>
            </a>

            <!-- Laporan Warga & Chat -->
            <a href="{{ route('admin.laporans') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ $currentRoute === 'admin.laporans' ? 'bg-white/15 text-white shadow-xs' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                <i class="bi bi-shield-exclamation text-sm text-red-400"></i>
                <span>Laporan & Aduan Chat</span>
            </a>

            <!-- Ulasan Produk -->
            <a href="{{ route('admin.ulasans') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ $currentRoute === 'admin.ulasans' ? 'bg-white/15 text-white shadow-xs' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                <i class="bi bi-star-fill text-sm text-amber-400"></i>
                <span>Ulasan & Testimoni</span>
            </a>

            <!-- Iklan, Promo & Diskon -->
            <a href="{{ route('admin.promo.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ str_starts_with($currentRoute ?? '', 'admin.promo') ? 'bg-white/15 text-white shadow-xs' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                <i class="bi bi-tag-fill text-sm text-pink-400"></i>
                <span>Iklan & Promo Diskon</span>
            </a>

            <!-- Pencairan Dana Rekber (2x24 Jam Kerja) -->
            <a href="{{ route('admin.pencairan.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ str_starts_with($currentRoute ?? '', 'admin.pencairan') ? 'bg-white/15 text-white shadow-xs' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                <i class="bi bi-cash-stack text-sm text-emerald-400"></i>
                <span>Pencairan Rekber</span>
            </a>

            <!-- Verifikasi Toko (Perorangan & Official Store) -->
            <a href="{{ route('admin.toko.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ str_starts_with($currentRoute ?? '', 'admin.toko') ? 'bg-white/15 text-white shadow-xs' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                <i class="bi bi-shop text-sm text-purple-400"></i>
                <span>Verifikasi Toko</span>
            </a>

            <div class="pt-4 px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-blue-300/70 font-mono">
                Navigasi Cepat
            </div>

            <!-- Tinjau Pasar Publik -->
            <a href="{{ route('layout') }}" target="_blank"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-blue-100 hover:bg-white/10 hover:text-white transition">
                <i class="bi bi-shop text-sm text-orange-400"></i>
                <span>Lihat Pasar Publik</span>
                <i class="bi bi-box-arrow-up-right text-[10px] ml-auto text-blue-300"></i>
            </a>
        </div>
    </div>

    <!-- Bottom User / Logout Section -->
    <div class="p-4 border-t border-blue-900/60 bg-blue-950/30">
        <div class="flex items-center gap-3 px-2 py-2 mb-2">
            <div class="w-9 h-9 rounded-full bg-white/10 border border-white/20 flex items-center justify-center font-bold text-amber-300 text-sm">
                A
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-bold text-white truncate">Admin Sedagho</p>
                <p class="text-[10px] text-blue-300 font-mono">admin@bjbo.local</p>
            </div>
        </div>

        <a href="{{ route('admin.logout') }}" class="w-full flex items-center justify-center gap-2 py-2 px-3 rounded-lg text-xs font-bold bg-red-600/20 hover:bg-red-600 text-red-200 hover:text-white transition border border-red-500/30">
            <i class="bi bi-box-arrow-right"></i>
            <span>Keluar Akun Admin</span>
        </a>
    </div>
</aside>