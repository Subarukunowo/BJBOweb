@extends('layouts.admin.main')

@section('title', 'Verifikasi & Kurasi Toko - BJBO Sedagho')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-bjbo-border">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Verifikasi Pendaftaran Toko</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Kurasi kelengkapan berkas KTP, NIB, NPWP, dan Sertifikat HAKI mitra penjual.</p>
        </div>
    </div>

    <!-- Table Toko -->
    <div class="bg-white border border-bjbo-border rounded-2xl overflow-hidden shadow-2xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-50/70 border-b border-bjbo-border text-gray-600 font-semibold uppercase tracking-wider text-[11px]">
                        <th class="p-3.5">Nama Toko</th>
                        <th class="p-3.5">Tipe Toko</th>
                        <th class="p-3.5">Pemilik & Kontak</th>
                        <th class="p-3.5">Persyaratan Terlampir</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5 text-right">Aksi Kurasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bjbo-border">
                    @forelse($stores as $st)
                        @php
                            $statusBadge = match($st->status) {
                                'aktif' => 'bg-green-100 text-green-800 border-green-200',
                                'ditolak' => 'bg-red-100 text-red-800 border-red-200',
                                'dinonaktifkan' => 'bg-gray-100 text-gray-800 border-gray-200',
                                default => 'bg-amber-100 text-amber-800 border-amber-200',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="p-3.5">
                                <span class="font-bold text-gray-900 block text-xs">{{ $st->store_name }}</span>
                                <span class="text-[11px] text-gray-400 font-mono">{{ $st->store_slug }}</span>
                            </td>
                            <td class="p-3.5">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                    {{ $st->store_type === 'official' ? 'bg-purple-100 text-purple-800 border border-purple-200' : 'bg-blue-100 text-blue-800 border border-blue-200' }}">
                                    {{ $st->store_type === 'official' ? 'Official Store' : 'Perorangan' }}
                                </span>
                            </td>
                            <td class="p-3.5">
                                <span class="font-bold text-gray-800 block">{{ $st->user->name ?? '-' }}</span>
                                <span class="text-[11px] text-gray-500">{{ $st->phone_number }} &bull; {{ $st->email }}</span>
                            </td>
                            <td class="p-3.5 text-[11px]">
                                @if($st->store_type === 'official')
                                    <div class="space-y-0.5">
                                        <div><strong>NIB:</strong> {{ $st->nib_number ?? '-' }}</div>
                                        <div><strong>NPWP:</strong> {{ $st->npwp_company ?? '-' }}</div>
                                        <div><strong>HAKI:</strong> {{ $st->haki_certificate_number ?? '-' }}</div>
                                    </div>
                                @else
                                    <div class="space-y-0.5">
                                        <div><strong>NIK KTP:</strong> {{ $st->ktp_number ?? '-' }}</div>
                                        <span class="text-emerald-700 font-semibold"><i class="bi bi-file-earmark-check"></i> Foto KTP Terunggah</span>
                                    </div>
                                @endif
                            </td>
                            <td class="p-3.5">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $statusBadge }}">
                                    {{ strtoupper(str_replace('_', ' ', $st->status)) }}
                                </span>
                            </td>
                            <td class="p-3.5 text-right">
                                @if($st->status === 'menunggu_verifikasi')
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Setujui -->
                                        <form action="{{ route('admin.toko.moderate', $st->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="aktif">
                                            <button type="submit" class="px-2.5 py-1 text-[11px] font-bold bg-green-600 hover:bg-green-700 text-white rounded-lg transition" title="Setujui Toko">
                                                <i class="bi bi-check-lg"></i> Setujui
                                            </button>
                                        </form>

                                        <!-- Tolak -->
                                        <form action="{{ route('admin.toko.moderate', $st->id) }}" method="POST" onsubmit="return confirm('Tolak pendaftaran toko ini?')">
                                            @csrf
                                            <input type="hidden" name="status" value="ditolak">
                                            <input type="hidden" name="reason" value="Dokumen identitas atau berkas legalitas belum memenuhi syarat.">
                                            <button type="submit" class="px-2.5 py-1 text-[11px] font-bold bg-red-100 text-red-700 hover:bg-red-200 rounded-lg transition" title="Tolak">
                                                Tolak
                                            </button>
                                        </form>
                                    </div>
                                @elseif($st->status === 'aktif')
                                    <form action="{{ route('admin.toko.moderate', $st->id) }}" method="POST" onsubmit="return confirm('Nonaktifkan toko ini?')">
                                        @csrf
                                        <input type="hidden" name="status" value="dinonaktifkan">
                                        <button type="submit" class="px-2 py-1 text-[10px] text-gray-500 hover:text-red-600 rounded border border-gray-200 hover:border-red-300">
                                            Nonaktifkan
                                        </button>
                                    </form>
                                @else
                                    <span class="text-[11px] text-gray-400">Arsip</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-500">
                                Belum ada pendaftaran toko baru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-bjbo-border">
            {{ $stores->links() }}
        </div>
    </div>
</div>
@endsection
