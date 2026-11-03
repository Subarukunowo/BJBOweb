@extends('layouts.admin.main')

@section('title', 'Manajemen Ulasan Produk - Admin BJBO')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-bjbo-border">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Manajemen Ulasan & Rating</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Ulasan, testimoni bintang, dan kepuasan pembeli terhadap barang dagangan warga.</p>
        </div>
        <div class="text-xs font-mono text-gray-500 bg-white border border-bjbo-border px-3 py-1.5 rounded-xl shadow-2xs">
            Total: <span class="font-bold text-gray-900">{{ count($ulasans) }}</span> Ulasan
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white border border-bjbo-border rounded-2xl shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-bjbo-surface-subtle border-b border-bjbo-border text-gray-500 font-mono uppercase tracking-wider text-[11px]">
                        <th class="py-3.5 px-4 font-semibold">Pembeli</th>
                        <th class="py-3.5 px-4 font-semibold">Produk</th>
                        <th class="py-3.5 px-4 font-semibold">Penilaian Bintang</th>
                        <th class="py-3.5 px-4 font-semibold">Komentar Ulasan</th>
                        <th class="py-3.5 px-4 font-semibold text-right">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bjbo-border text-gray-700">
                    @forelse($ulasans as $ulasan)
                        <tr class="hover:bg-gray-50/80 transition">
                            <!-- Pembeli -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2.5">
                                    <img src="{{ $ulasan->user->profile_picture_url ?? asset('images/bjbo.png') }}"
                                         alt="{{ $ulasan->user->name ?? 'Warga' }}"
                                         class="w-8 h-8 rounded-full object-cover border border-bjbo-border shrink-0">
                                    <div class="min-w-0">
                                        <p class="font-bold text-gray-900 text-xs truncate">{{ $ulasan->user->name ?? 'Warga Sedagho' }}</p>
                                        <span class="text-[10px] text-gray-400 font-mono">{{ $ulasan->user->email ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Produk -->
                            <td class="py-3.5 px-4">
                                @if($ulasan->postingan)
                                    <a href="{{ route('user.detail.postingan', $ulasan->postingan->id) }}" target="_blank" class="font-semibold text-bjbo-primary hover:underline block max-w-xs truncate">
                                        {{ $ulasan->postingan->name }}
                                    </a>
                                    <span class="text-[10px] font-mono text-bjbo-accent font-bold">{{ $ulasan->postingan->formatted_price }}</span>
                                @else
                                    <span class="text-gray-400 italic">Produk telah dihapus</span>
                                @endif
                            </td>

                            <!-- Rating Bintang -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-1.5">
                                    <div class="flex items-center text-amber-400 text-xs">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi {{ $i <= $ulasan->rating ? 'bi-star-fill' : 'bi-star text-gray-300' }}"></i>
                                        @endfor
                                    </div>
                                    <span class="font-mono font-bold text-gray-800 text-xs ml-1">{{ $ulasan->rating }}.0</span>
                                </div>
                            </td>

                            <!-- Komentar -->
                            <td class="py-3.5 px-4 max-w-md">
                                <p class="text-xs text-gray-700 leading-relaxed bg-gray-50 p-2 rounded-lg border border-gray-100">
                                    "{{ $ulasan->komentar }}"
                                </p>
                            </td>

                            <!-- Tanggal -->
                            <td class="py-3.5 px-4 font-mono text-gray-400 text-right whitespace-nowrap">
                                {{ $ulasan->created_at ? $ulasan->created_at->format('d M Y H:i') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400">Belum ada ulasan dari pembeli.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
