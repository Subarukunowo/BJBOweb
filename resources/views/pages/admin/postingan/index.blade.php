@extends('layouts.admin.main')

@section('title', 'Manajemen Postingan & Kurasi - Admin BJBO')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-bjbo-border">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Manajemen Postingan & Kurasi</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                Sistem auto-block otomatis memfilter barang terlarang (narkoba, senjata, perdagangan manusia). Postingan bersih otomatis disetujui.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs font-mono text-gray-500 bg-white border border-bjbo-border px-3 py-1.5 rounded-xl shadow-2xs">
                Total: <span class="font-bold text-gray-900">{{ count($postingans) }}</span> Produk
            </span>
        </div>
    </div>

    <!-- Filter Status Chips -->
    <div class="flex items-center gap-2 overflow-x-auto text-xs pb-1">
        <button type="button" onclick="filterTable('all')" class="filter-chip px-3 py-1.5 rounded-lg font-semibold bg-bjbo-primary text-white transition cursor-pointer" data-filter="all">
            Semua ({{ count($postingans) }})
        </button>
        <button type="button" onclick="filterTable('blocked')" class="filter-chip px-3 py-1.5 rounded-lg font-semibold bg-white border border-bjbo-border hover:bg-gray-50 text-red-600 transition cursor-pointer" data-filter="blocked">
            <i class="bi bi-slash-circle mr-1"></i> Auto-Blocked ({{ $postingans->where('is_auto_blocked', true)->count() }})
        </button>
        <button type="button" onclick="filterTable('active')" class="filter-chip px-3 py-1.5 rounded-lg font-semibold bg-white border border-bjbo-border hover:bg-gray-50 text-emerald-700 transition cursor-pointer" data-filter="active">
            <i class="bi bi-check-circle mr-1"></i> Aktif Disetujui ({{ $postingans->where('status', 'disetujui')->where('is_auto_blocked', false)->count() }})
        </button>
        <button type="button" onclick="filterTable('pending')" class="filter-chip px-3 py-1.5 rounded-lg font-semibold bg-white border border-bjbo-border hover:bg-gray-50 text-amber-700 transition cursor-pointer" data-filter="pending">
            Menunggu Kurasi ({{ $postingans->where('status', '!=', 'disetujui')->where('is_auto_blocked', false)->count() }})
        </button>
    </div>

    <!-- Table Container -->
    <div class="bg-white border border-bjbo-border rounded-2xl shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs" id="postingan-table">
                <thead>
                    <tr class="bg-bjbo-surface-subtle border-b border-bjbo-border text-gray-500 font-mono uppercase tracking-wider text-[11px]">
                        <th class="py-3.5 px-4 font-semibold">Produk</th>
                        <th class="py-3.5 px-4 font-semibold">Kategori & Lokasi</th>
                        <th class="py-3.5 px-4 font-semibold">Harga</th>
                        <th class="py-3.5 px-4 font-semibold">Penjual</th>
                        <th class="py-3.5 px-4 font-semibold">Status Moderasi</th>
                        <th class="py-3.5 px-4 font-semibold text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bjbo-border text-gray-700">
                    @forelse($postingans as $item)
                        @php
                            $rowType = $item->is_auto_blocked ? 'blocked' : ($item->status === 'disetujui' ? 'active' : 'pending');
                        @endphp
                        <tr class="hover:bg-gray-50/80 transition postingan-row" data-type="{{ $rowType }}">
                            <!-- Produk -->
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $item->image_url }}"
                                         alt="{{ $item->name }}"
                                         class="w-12 h-12 rounded-xl object-cover border border-bjbo-border shrink-0 shadow-2xs">
                                    <div class="min-w-0 max-w-xs">
                                        <p class="font-bold text-gray-900 text-xs truncate">{{ $item->name }}</p>
                                        <p class="text-[11px] text-gray-500 truncate mt-0.5">{{ Str::limit($item->description, 50) }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Kategori & Lokasi -->
                            <td class="py-3 px-4">
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-700 mb-1">
                                    {{ $item->category }}
                                </span>
                                <p class="text-[11px] text-gray-400 font-mono truncate">
                                    <i class="bi bi-geo-alt"></i> {{ $item->lokasi }}
                                </p>
                            </td>

                            <!-- Harga -->
                            <td class="py-3 px-4 font-mono font-bold text-bjbo-accent text-xs">
                                {{ $item->formatted_price }}
                            </td>

                            <!-- Penjual -->
                            <td class="py-3 px-4">
                                <span class="font-bold text-gray-900 block truncate">{{ $item->username ?? ($item->user->name ?? 'Warga') }}</span>
                                <span class="text-[10px] text-gray-400 font-mono">ID User: #{{ $item->user_id }}</span>
                            </td>

                            <!-- Status Moderasi -->
                            <td class="py-3 px-4">
                                @if($item->is_auto_blocked)
                                    <div>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700 border border-red-200">
                                            <i class="bi bi-slash-circle-fill"></i> Auto-Blocked
                                        </span>
                                        <p class="text-[10px] text-red-600 mt-1 max-w-[200px] leading-tight">
                                            {{ $item->block_reason }}
                                        </p>
                                    </div>
                                @elseif($item->status === 'disetujui')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="bi bi-check-circle-fill"></i> Disetujui (Tayang)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        <i class="bi bi-clock"></i> Menunggu Kurasi
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5 justify-end">
                                    <!-- Toggle / Unblock Form -->
                                    <form action="{{ route('posting.updateStatus', $item->id) }}" method="POST" class="inline m-0">
                                        @csrf
                                        @if($item->is_auto_blocked)
                                            <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition" title="Buka Blokir & Terbitkan">
                                                <i class="bi bi-check-lg mr-1"></i> Buka Blokir
                                            </button>
                                        @elseif($item->status === 'disetujui')
                                            <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 transition" title="Tarik dari Etalase">
                                                Tarik Postingan
                                            </button>
                                        @else
                                            <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition" title="Setujui Postingan">
                                                Setujui
                                            </button>
                                        @endif
                                    </form>

                                    <!-- Lihat Detail di Tab Baru -->
                                    <a href="{{ route('user.detail.postingan', $item->id) }}" target="_blank" class="p-1.5 rounded-lg text-gray-400 hover:text-bjbo-primary hover:bg-blue-50 transition" title="Lihat Tampilan Produk">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <!-- Hapus Postingan -->
                                    <form action="{{ route('admin.postings') }}" method="POST" class="inline m-0" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="id" value="{{ $item->id }}">
                                        <button type="button" onclick="deletePosting({{ $item->id }})" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition" title="Hapus Produk">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400">Belum ada postingan produk di sistem.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function filterTable(filter) {
        // Update chip active states
        document.querySelectorAll('.filter-chip').forEach(btn => {
            if (btn.dataset.filter === filter) {
                btn.className = 'filter-chip px-3 py-1.5 rounded-lg font-semibold bg-bjbo-primary text-white transition cursor-pointer';
            } else {
                btn.className = 'filter-chip px-3 py-1.5 rounded-lg font-semibold bg-white border border-bjbo-border hover:bg-gray-50 text-gray-700 transition cursor-pointer';
            }
        });

        // Show/hide rows
        document.querySelectorAll('.postingan-row').forEach(row => {
            if (filter === 'all' || row.dataset.type === filter) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function deletePosting(id) {
        if (confirm('Yakin ingin menghapus postingan ini secara permanen?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/posting/delete/${id}`;
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);

            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';
            form.appendChild(method);

            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endsection
