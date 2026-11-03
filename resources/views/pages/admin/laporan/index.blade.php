@extends('layouts.admin.main')

@section('title', 'Manajemen Laporan & Aduan - Admin BJBO')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-bjbo-border">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Laporan & Aduan Komunitas</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Pusat moderasi laporan pelanggaran postingan produk dan aduan chat tidak pantas antar-warga.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs font-mono text-red-600 bg-red-50 border border-red-200 px-3 py-1.5 rounded-xl font-bold shadow-2xs">
                Total Aduan: {{ count($laporans) + (isset($chatLaporans) ? count($chatLaporans) : 0) }}
            </span>
        </div>
    </div>

    <!-- Tab Selector -->
    <div class="flex items-center gap-2 border-b border-bjbo-border pb-2 text-xs font-semibold">
        <button type="button" onclick="switchReportTab('postingan')" id="tab-btn-postingan" class="px-4 py-2 rounded-xl bg-bjbo-primary text-white transition flex items-center gap-2 cursor-pointer shadow-xs">
            <i class="bi bi-box-seam"></i>
            <span>Laporan Postingan ({{ count($laporans) }})</span>
        </button>
        <button type="button" onclick="switchReportTab('chat')" id="tab-btn-chat" class="px-4 py-2 rounded-xl bg-white border border-bjbo-border text-gray-700 hover:bg-gray-50 transition flex items-center gap-2 cursor-pointer">
            <i class="bi bi-chat-dots-fill text-purple-600"></i>
            <span>Aduan Chat Tidak Pantas ({{ isset($chatLaporans) ? count($chatLaporans) : 0 }})</span>
        </button>
    </div>

    <!-- Section 1: Laporan Postingan -->
    <div id="section-postingan" class="bg-white border border-bjbo-border rounded-2xl shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-bjbo-surface-subtle border-b border-bjbo-border text-gray-500 font-mono uppercase tracking-wider text-[11px]">
                        <th class="py-3.5 px-4 font-semibold">Pelapor</th>
                        <th class="py-3.5 px-4 font-semibold">Produk yang Dilaporkan</th>
                        <th class="py-3.5 px-4 font-semibold">Alasan Pelanggaran</th>
                        <th class="py-3.5 px-4 font-semibold">Keterangan</th>
                        <th class="py-3.5 px-4 font-semibold text-right">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bjbo-border text-gray-700">
                    @forelse($laporans as $lap)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="py-3.5 px-4">
                                <p class="font-bold text-gray-900 text-xs">{{ $lap->user->name ?? 'Warga' }}</p>
                                <span class="text-[10px] text-gray-400 font-mono">ID: #{{ $lap->user_id }}</span>
                            </td>

                            <td class="py-3.5 px-4">
                                @if($lap->postingan)
                                    <div class="flex items-center gap-2.5">
                                        <img src="{{ $lap->postingan->image_url }}" alt="{{ $lap->postingan->name }}" class="w-10 h-10 rounded-lg object-cover border border-bjbo-border shrink-0">
                                        <div class="min-w-0 max-w-xs">
                                            <a href="{{ route('user.detail.postingan', $lap->postingan->id) }}" target="_blank" class="font-bold text-bjbo-primary hover:underline truncate block">
                                                {{ $lap->postingan->name }}
                                            </a>
                                            <span class="text-[10px] text-gray-400 font-mono">{{ $lap->postingan->formatted_price }}</span>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400 italic">Produk telah dihapus</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-bold bg-red-50 text-red-700 border border-red-200">
                                    <i class="bi bi-exclamation-triangle-fill"></i> {{ $lap->alasan }}
                                </span>
                            </td>

                            <td class="py-3.5 px-4 text-gray-600 max-w-xs">
                                {{ $lap->keterangan ?? '-' }}
                            </td>

                            <td class="py-3.5 px-4 font-mono text-gray-400 text-right whitespace-nowrap">
                                {{ $lap->created_at ? $lap->created_at->format('d M Y H:i') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400">Belum ada laporan pelanggaran postingan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 2: Laporan Chat Tidak Pantas -->
    <div id="section-chat" class="bg-white border border-bjbo-border rounded-2xl shadow-2xs overflow-hidden hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-bjbo-surface-subtle border-b border-bjbo-border text-gray-500 font-mono uppercase tracking-wider text-[11px]">
                        <th class="py-3.5 px-4 font-semibold">Pelapor</th>
                        <th class="py-3.5 px-4 font-semibold">Pengguna yang Dilaporkan</th>
                        <th class="py-3.5 px-4 font-semibold">Isi Pesan Chat (E2E Decrypted)</th>
                        <th class="py-3.5 px-4 font-semibold">Kategori Pelanggaran</th>
                        <th class="py-3.5 px-4 font-semibold text-right">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bjbo-border text-gray-700">
                    @if(isset($chatLaporans) && count($chatLaporans) > 0)
                        @foreach($chatLaporans as $cl)
                            <tr class="hover:bg-gray-50/80 transition">
                                <td class="py-3.5 px-4">
                                    <p class="font-bold text-gray-900 text-xs">{{ $cl->reporter->name ?? 'Warga' }}</p>
                                    <span class="text-[10px] text-gray-400 font-mono">{{ $cl->reporter->email ?? '-' }}</span>
                                </td>

                                <td class="py-3.5 px-4">
                                    <p class="font-bold text-red-700 text-xs">{{ $cl->reportedUser->name ?? 'Terlapor' }}</p>
                                    <span class="text-[10px] text-gray-400 font-mono">{{ $cl->reportedUser->email ?? '-' }}</span>
                                </td>

                                <td class="py-3.5 px-4 max-w-sm">
                                    <div class="p-2.5 rounded-lg bg-gray-50 border border-gray-200 font-mono text-[11px] text-gray-800">
                                        "{{ $cl->message->decrypted_message ?? '-' }}"
                                    </div>
                                    @if($cl->keterangan)
                                        <p class="text-[10px] text-gray-500 mt-1 italic">Catatan: {{ $cl->keterangan }}</p>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        <i class="bi bi-shield-slash-fill text-amber-600"></i> {{ $cl->alasan }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4 font-mono text-gray-400 text-right whitespace-nowrap">
                                    {{ $cl->created_at ? $cl->created_at->format('d M Y H:i') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400">Belum ada aduan chat tidak pantas.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function switchReportTab(tab) {
        const secPost = document.getElementById('section-postingan');
        const secChat = document.getElementById('section-chat');
        const btnPost = document.getElementById('tab-btn-postingan');
        const btnChat = document.getElementById('tab-btn-chat');

        if (tab === 'postingan') {
            secPost.classList.remove('hidden');
            secChat.classList.add('hidden');
            btnPost.className = 'px-4 py-2 rounded-xl bg-bjbo-primary text-white transition flex items-center gap-2 cursor-pointer shadow-xs';
            btnChat.className = 'px-4 py-2 rounded-xl bg-white border border-bjbo-border text-gray-700 hover:bg-gray-50 transition flex items-center gap-2 cursor-pointer';
        } else {
            secPost.classList.add('hidden');
            secChat.classList.remove('hidden');
            btnChat.className = 'px-4 py-2 rounded-xl bg-bjbo-primary text-white transition flex items-center gap-2 cursor-pointer shadow-xs';
            btnPost.className = 'px-4 py-2 rounded-xl bg-white border border-bjbo-border text-gray-700 hover:bg-gray-50 transition flex items-center gap-2 cursor-pointer';
        }
    }
</script>
@endsection
