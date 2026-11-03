@extends('layouts.admin.main')

@section('title', 'Dashboard Administrator - BJBO Sedagho')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-bjbo-border">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Ikhtisar Dashboard</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Pemantauan data pasar, moderasi barang terlarang, dan keamanan komunitas.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.postings') }}" class="bjbo-btn-primary px-3.5 py-2 text-xs font-semibold rounded-xl inline-flex items-center gap-1.5 shadow-2xs">
                <i class="bi bi-box-seam"></i> Kelola Postingan
            </a>
            <a href="{{ route('admin.laporans') }}" class="px-3.5 py-2 text-xs font-semibold rounded-xl bg-white border border-bjbo-border hover:bg-gray-50 text-gray-700 inline-flex items-center gap-1.5 shadow-2xs">
                <i class="bi bi-shield-exclamation text-red-500"></i> Aduan Komunitas
            </a>
        </div>
    </div>

    <!-- Stat Cards Grid (6 Metrik) -->
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3 sm:gap-4">
        <!-- Pengguna -->
        <div class="bg-white border border-bjbo-border rounded-2xl p-4 shadow-2xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-500">Warga Terdaftar</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-bjbo-primary flex items-center justify-center">
                    <i class="bi bi-people-fill text-sm"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-gray-900 font-mono">{{ $users }}</div>
            <span class="text-[11px] text-gray-400 mt-1 block">Akun aktif</span>
        </div>

        <!-- Postingan Aktif -->
        <div class="bg-white border border-bjbo-border rounded-2xl p-4 shadow-2xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-500">Postingan Aktif</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i class="bi bi-check-circle-fill text-sm"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-emerald-600 font-mono">{{ $activePostings }}</div>
            <span class="text-[11px] text-gray-400 mt-1 block">Tayang di pasar</span>
        </div>

        <!-- Auto-Blocked (Terlarang) -->
        <div class="bg-white border border-bjbo-border rounded-2xl p-4 shadow-2xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-red-600">Auto-Blocked</span>
                <div class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center">
                    <i class="bi bi-slash-circle-fill text-sm"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-red-600 font-mono">{{ $blockedPostings }}</div>
            <span class="text-[11px] text-red-500 mt-1 block">Barang terlarang</span>
        </div>

        <!-- Total Laporan Postingan -->
        <div class="bg-white border border-bjbo-border rounded-2xl p-4 shadow-2xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-500">Laporan Postingan</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i class="bi bi-flag-fill text-sm"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-gray-900 font-mono">{{ $laporans }}</div>
            <span class="text-[11px] text-gray-400 mt-1 block">Dari warga sedagho</span>
        </div>

        <!-- Total Laporan Chat -->
        <div class="bg-white border border-bjbo-border rounded-2xl p-4 shadow-2xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-500">Laporan Chat</span>
                <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center">
                    <i class="bi bi-chat-square-dots-fill text-sm"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-gray-900 font-mono">{{ $chatReports }}</div>
            <span class="text-[11px] text-gray-400 mt-1 block">Aduan obrolan</span>
        </div>

        <!-- Total Transaksi -->
        <div class="bg-white border border-bjbo-border rounded-2xl p-4 shadow-2xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-500">Total Transaksi</span>
                <div class="w-8 h-8 rounded-lg bg-orange-50 text-bjbo-accent flex items-center justify-center">
                    <i class="bi bi-receipt text-sm"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-gray-900 font-mono">{{ $transactions }}</div>
            <span class="text-[11px] text-gray-400 mt-1 block">Pesanan terdaftar</span>
        </div>
    </div>

    <!-- Two-Column Section: Postingan Terbaru & Laporan Terbaru -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Kolom Kiri: Postingan Terbaru (8 Kolom) -->
        <div class="lg:col-span-8 bg-white border border-bjbo-border rounded-2xl p-5 shadow-2xs">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-bjbo-border">
                <h2 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                    <i class="bi bi-box-seam text-bjbo-primary"></i> Postingan Terbaru
                </h2>
                <a href="{{ route('admin.postings') }}" class="text-xs font-semibold text-bjbo-primary hover:underline">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="divide-y divide-bjbo-border overflow-hidden">
                @forelse($recentPostings as $item)
                    <div class="py-3 flex items-center justify-between gap-3 group">
                        <div class="flex items-center gap-3 min-w-0">
                            <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-11 h-11 rounded-xl object-cover border border-bjbo-border shrink-0">
                            <div class="min-w-0">
                                <h3 class="text-xs font-bold text-gray-900 truncate group-hover:text-bjbo-primary transition">
                                    {{ $item->name }}
                                </h3>
                                <div class="flex items-center gap-2 mt-0.5 text-[11px] text-gray-500">
                                    <span class="font-mono font-semibold text-bjbo-accent">{{ $item->formatted_price }}</span>
                                    <span>•</span>
                                    <span>Penjual: {{ $item->username ?? ($item->user->name ?? 'Warga') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            @if($item->is_auto_blocked)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700 border border-red-200" title="{{ $item->block_reason }}">
                                    <i class="bi bi-slash-circle mr-1"></i> Auto-Blocked
                                </span>
                            @elseif($item->status === 'disetujui')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i class="bi bi-check-circle mr-1"></i> Aktif
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    Menunggu
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 py-6 text-center">Belum ada postingan barang.</p>
                @endforelse
            </div>
        </div>

        <!-- Kolom Kanan: Laporan Komunitas Terbaru (4 Kolom) -->
        <div class="lg:col-span-4 bg-white border border-bjbo-border rounded-2xl p-5 shadow-2xs">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-bjbo-border">
                <h2 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                    <i class="bi bi-shield-exclamation text-red-500"></i> Laporan Terbaru
                </h2>
                <a href="{{ route('admin.laporans') }}" class="text-xs font-semibold text-bjbo-primary hover:underline">
                    Lihat Semua
                </a>
            </div>

            <div class="space-y-3">
                @forelse($recentLaporans as $lap)
                    <div class="p-3 rounded-xl bg-bjbo-surface-subtle border border-bjbo-border text-xs">
                        <div class="flex items-center justify-between gap-1 mb-1">
                            <span class="font-bold text-gray-900 truncate">{{ $lap->user->name ?? 'Warga' }}</span>
                            <span class="text-[10px] text-gray-400 font-mono">{{ $lap->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-[11px] text-red-700 font-semibold mb-1">
                            <i class="bi bi-exclamation-circle-fill mr-1"></i> {{ $lap->alasan }}
                        </p>
                        @if($lap->postingan)
                            <p class="text-[11px] text-gray-500 truncate">Produk: {{ $lap->postingan->name }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-xs text-gray-400 py-6 text-center">Tidak ada laporan pelanggaran baru.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
