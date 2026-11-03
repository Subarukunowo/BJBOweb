@props(['activeCategory' => null, 'baseRoute' => 'layout'])

<div class="flex gap-2 overflow-x-auto py-2 scrollbar-none">
    <a href="{{ route($baseRoute) }}" class="flex-shrink-0 inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-semibold border transition {{ !$activeCategory ? 'bg-bjbo-primary text-white border-bjbo-primary' : 'bg-white text-gray-700 border-bjbo-border hover:bg-bjbo-primary-light hover:text-bjbo-primary' }}">
        <i class="bi bi-grid-fill"></i> Semua
    </a>
    <a href="{{ route('postingan.kategori', 'Elektronik') }}" class="flex-shrink-0 inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-semibold border transition {{ $activeCategory === 'Elektronik' ? 'bg-bjbo-primary text-white border-bjbo-primary' : 'bg-white text-gray-700 border-bjbo-border hover:bg-bjbo-primary-light hover:text-bjbo-primary' }}">
        <i class="bi bi-tv"></i> Elektronik
    </a>
    <a href="{{ route('postingan.kategori', 'Fashion') }}" class="flex-shrink-0 inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-semibold border transition {{ $activeCategory === 'Fashion' ? 'bg-bjbo-primary text-white border-bjbo-primary' : 'bg-white text-gray-700 border-bjbo-border hover:bg-bjbo-primary-light hover:text-bjbo-primary' }}">
        <i class="bi bi-bag"></i> Fashion
    </a>
    <a href="{{ route('postingan.kategori', 'Otomotif') }}" class="flex-shrink-0 inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-semibold border transition {{ $activeCategory === 'Otomotif' ? 'bg-bjbo-primary text-white border-bjbo-primary' : 'bg-white text-gray-700 border-bjbo-border hover:bg-bjbo-primary-light hover:text-bjbo-primary' }}">
        <i class="bi bi-car-front"></i> Otomotif
    </a>
    <a href="{{ route('postingan.kategori', 'Perabot') }}" class="flex-shrink-0 inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-semibold border transition {{ $activeCategory === 'Perabot' ? 'bg-bjbo-primary text-white border-bjbo-primary' : 'bg-white text-gray-700 border-bjbo-border hover:bg-bjbo-primary-light hover:text-bjbo-primary' }}">
        <i class="bi bi-house-door"></i> Perabot
    </a>
    <a href="{{ route('postingan.kategori', 'Lainnya') }}" class="flex-shrink-0 inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-semibold border transition {{ $activeCategory === 'Lainnya' ? 'bg-bjbo-primary text-white border-bjbo-primary' : 'bg-white text-gray-700 border-bjbo-border hover:bg-bjbo-primary-light hover:text-bjbo-primary' }}">
        <i class="bi bi-box-seam"></i> Lainnya
    </a>
</div>
