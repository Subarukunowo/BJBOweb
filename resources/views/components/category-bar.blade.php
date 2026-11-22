@props(['activeCategory' => null, 'baseRoute' => 'layout'])

<div class="w-full flex items-center justify-start sm:justify-center gap-2 overflow-x-auto py-2.5 px-1 scrollbar-none">
    <a href="{{ route($baseRoute) }}" class="flex-shrink-0 inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-semibold border transition shadow-xs {{ !$activeCategory ? 'bg-emerald-600 text-white border-emerald-600 ring-2 ring-emerald-500/20' : 'bg-white text-zinc-700 border-zinc-200 hover:bg-zinc-50 hover:border-zinc-300' }}">
        <i class="bi bi-stars"></i> Rekomendasi
    </a>
    <a href="{{ route('postingan.kategori', 'Elektronik') }}" class="flex-shrink-0 inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-medium border transition shadow-xs {{ $activeCategory === 'Elektronik' ? 'bg-emerald-600 text-white border-emerald-600 ring-2 ring-emerald-500/20' : 'bg-white text-zinc-700 border-zinc-200 hover:bg-zinc-50 hover:border-zinc-300' }}">
        <i class="bi bi-laptop"></i> Elektronik
    </a>
    <a href="{{ route('postingan.kategori', 'Gadget') }}" class="flex-shrink-0 inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-medium border transition shadow-xs {{ $activeCategory === 'Gadget' ? 'bg-emerald-600 text-white border-emerald-600 ring-2 ring-emerald-500/20' : 'bg-white text-zinc-700 border-zinc-200 hover:bg-zinc-50 hover:border-zinc-300' }}">
        <i class="bi bi-phone"></i> Gadget
    </a>
    <a href="{{ route('postingan.kategori', 'Fashion') }}" class="flex-shrink-0 inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-medium border transition shadow-xs {{ $activeCategory === 'Fashion' ? 'bg-emerald-600 text-white border-emerald-600 ring-2 ring-emerald-500/20' : 'bg-white text-zinc-700 border-zinc-200 hover:bg-zinc-50 hover:border-zinc-300' }}">
        <i class="bi bi-bag"></i> Fashion
    </a>
    <a href="{{ route('postingan.kategori', 'Otomotif') }}" class="flex-shrink-0 inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-medium border transition shadow-xs {{ $activeCategory === 'Otomotif' ? 'bg-emerald-600 text-white border-emerald-600 ring-2 ring-emerald-500/20' : 'bg-white text-zinc-700 border-zinc-200 hover:bg-zinc-50 hover:border-zinc-300' }}">
        <i class="bi bi-car-front"></i> Otomotif
    </a>
    <a href="{{ route('postingan.kategori', 'Perabot') }}" class="flex-shrink-0 inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-medium border transition shadow-xs {{ $activeCategory === 'Perabot' ? 'bg-emerald-600 text-white border-emerald-600 ring-2 ring-emerald-500/20' : 'bg-white text-zinc-700 border-zinc-200 hover:bg-zinc-50 hover:border-zinc-300' }}">
        <i class="bi bi-house-door"></i> Rumah Tangga
    </a>
    <a href="{{ route('postingan.kategori', 'Hobi') }}" class="flex-shrink-0 inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-medium border transition shadow-xs {{ $activeCategory === 'Hobi' ? 'bg-emerald-600 text-white border-emerald-600 ring-2 ring-emerald-500/20' : 'bg-white text-zinc-700 border-zinc-200 hover:bg-zinc-50 hover:border-zinc-300' }}">
        <i class="bi bi-bicycle"></i> Olahraga & Hobi
    </a>
    <a href="{{ route('postingan.kategori', 'Lainnya') }}" class="flex-shrink-0 inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-medium border transition shadow-xs {{ $activeCategory === 'Lainnya' ? 'bg-emerald-600 text-white border-emerald-600 ring-2 ring-emerald-500/20' : 'bg-white text-zinc-700 border-zinc-200 hover:bg-zinc-50 hover:border-zinc-300' }}">
        <i class="bi bi-grid"></i> Lainnya
    </a>
</div>
