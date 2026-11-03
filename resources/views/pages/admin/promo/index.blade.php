@extends('layouts.admin.main')

@section('title', 'Manajemen Iklan, Promo & Diskon - BJBO Sedagho')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-bjbo-border">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Manajemen Iklan, Promo & Diskon</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Kelola banner promosi pasar dan kupon diskon untuk pembeli.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.promo.create') }}" class="bjbo-btn-primary px-3.5 py-2 text-xs font-semibold rounded-xl inline-flex items-center gap-1.5 shadow-2xs">
                <i class="bi bi-plus-circle-fill"></i> Tambah Promo / Iklan Baru
            </a>
        </div>
    </div>

    <!-- Table Promo -->
    <div class="bg-white border border-bjbo-border rounded-2xl overflow-hidden shadow-2xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-50/70 border-b border-bjbo-border text-gray-600 font-semibold uppercase tracking-wider text-[11px]">
                        <th class="p-3.5">Promo / Banner</th>
                        <th class="p-3.5">Tipe</th>
                        <th class="p-3.5">Kode Kupon</th>
                        <th class="p-3.5">Diskon</th>
                        <th class="p-3.5">Min. Belanja</th>
                        <th class="p-3.5">Periode</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bjbo-border">
                    @forelse($promos as $promo)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="p-3.5">
                                <div class="flex items-center gap-3">
                                    @if($promo->banner_image)
                                        <img src="{{ asset('storage/'.$promo->banner_image) }}" alt="{{ $promo->title }}" class="w-12 h-12 rounded-lg object-cover border border-bjbo-border">
                                    @else
                                        <div class="w-12 h-12 rounded-lg bg-pink-50 text-pink-600 flex items-center justify-center font-bold text-sm">
                                            <i class="bi bi-tag"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <span class="font-bold text-gray-900 block text-xs">{{ $promo->title }}</span>
                                        <span class="text-[11px] text-gray-400">{{ Str::limit($promo->description, 35) }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-3.5">
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-gray-100 text-gray-700">
                                    {{ str_replace('_', ' ', $promo->type) }}
                                </span>
                            </td>
                            <td class="p-3.5 font-mono font-bold text-bjbo-primary">
                                {{ $promo->code ?? '-' }}
                            </td>
                            <td class="p-3.5 font-semibold text-emerald-600">
                                @if($promo->discount_percentage)
                                    {{ $promo->discount_percentage }}%
                                @elseif($promo->discount_amount)
                                    Rp{{ number_format($promo->discount_amount, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="p-3.5 text-gray-700">
                                Rp{{ number_format($promo->min_purchase, 0, ',', '.') }}
                            </td>
                            <td class="p-3.5 text-[11px] text-gray-500">
                                <div>Mulai: {{ $promo->start_date ? $promo->start_date->format('d/m/Y') : 'Selalu' }}</div>
                                <div>Selesai: {{ $promo->end_date ? $promo->end_date->format('d/m/Y') : 'Tanpa batas' }}</div>
                            </td>
                            <td class="p-3.5">
                                <form action="{{ route('admin.promo.toggle', $promo->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded-full text-[10px] font-bold cursor-pointer transition
                                        {{ $promo->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                                        title="Klik untuk ubah status">
                                        {{ $promo->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </form>
                            </td>
                            <td class="p-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.promo.edit', $promo->id) }}" class="p-1.5 text-gray-600 hover:text-bjbo-primary hover:bg-gray-100 rounded-lg transition" title="Edit">
                                        <i class="bi bi-pencil-square text-sm"></i>
                                    </a>
                                    <form action="{{ route('admin.promo.destroy', $promo->id) }}" method="POST" onsubmit="return confirm('Hapus promo ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                            <i class="bi bi-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-gray-500">
                                Belum ada iklan atau promo diskon yang dibuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-bjbo-border">
            {{ $promos->links() }}
        </div>
    </div>
</div>
@endsection
