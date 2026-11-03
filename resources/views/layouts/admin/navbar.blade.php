<!-- Top Header Navbar Admin -->
<header class="h-16 bg-white border-b border-bjbo-border px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-4 sticky top-0 z-30 shadow-2xs">
    <!-- Left: Hamburger button & Page Context -->
    <div class="flex items-center gap-3">
        <button type="button" onclick="toggleAdminSidebar()" class="lg:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100 transition focus:outline-none">
            <i class="bi bi-list text-xl"></i>
        </button>

        <div class="flex items-center gap-2 text-xs">
            <span class="font-bold text-gray-800">Panel Administrator</span>
            <span class="text-gray-300">/</span>
            <span class="text-gray-500 font-mono">Bengkalis Sedagho</span>
        </div>
    </div>

    <!-- Right: Quick Status & Admin Profile -->
    <div class="flex items-center gap-3">
        <!-- Live System Badge -->
        <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Sistem Operasional</span>
        </span>

        <!-- Pasar Link Button -->
        <a href="{{ route('layout') }}" target="_blank" class="hidden md:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-700 hover:bg-gray-100 transition border border-bjbo-border">
            <i class="bi bi-shop text-xs text-bjbo-primary"></i>
            <span>Ke Pasar</span>
        </a>

        <!-- Admin Profile -->
        <div class="flex items-center gap-2 pl-2 border-l border-bjbo-border">
            <div class="w-8 h-8 rounded-full bg-bjbo-primary text-white flex items-center justify-center font-bold text-xs shadow-2xs">
                A
            </div>
            <div class="hidden sm:block text-left">
                <span class="text-xs font-bold text-gray-900 block leading-tight">Admin BJBO</span>
                <span class="text-[10px] text-gray-400 font-mono">Super Administrator</span>
            </div>
            <a href="{{ route('admin.logout') }}" class="p-1.5 text-gray-400 hover:text-red-600 transition ml-1" title="Logout">
                <i class="bi bi-box-arrow-right text-base"></i>
            </a>
        </div>
    </div>
</header>