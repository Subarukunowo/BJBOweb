@extends('layouts.admin.main')

@section('title', 'Pencairan Dana Rekber (2x24 Jam Kerja) - BJBO Sedagho')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-bjbo-border">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Pencairan Dana Rekber Penjual</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Kelola dan proses transfer dana hasil transaksi escrow penjual (SLA: 2x24 jam kerja).</p>
        </div>
    </div>

    <!-- Table Pencairan -->
    <div class="bg-white border border-bjbo-border rounded-2xl overflow-hidden shadow-2xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-50/70 border-b border-bjbo-border text-gray-600 font-semibold uppercase tracking-wider text-[11px]">
                        <th class="p-3.5">No. Referensi</th>
                        <th class="p-3.5">Penjual</th>
                        <th class="p-3.5">Rekening Tujuan</th>
                        <th class="p-3.5">Nominal Penarikan</th>
                        <th class="p-3.5">Target SLA (2x24 Jam Kerja)</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5 text-right">Proses Perbankan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bjbo-border">
                    @forelse($withdrawals as $wd)
                        @php
                            $statusBadge = match($wd->status) {
                                'selesai' => 'bg-green-100 text-green-800 border-green-200',
                                'ditolak' => 'bg-red-100 text-red-800 border-red-200',
                                'diproses' => 'bg-blue-100 text-blue-800 border-blue-200',
                                default => 'bg-amber-100 text-amber-800 border-amber-200',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="p-3.5 font-mono font-bold text-gray-800">
                                {{ $wd->reference_number }}
                                <div class="text-[10px] text-gray-400 font-sans mt-0.5">{{ $wd->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                            <td class="p-3.5">
                                <span class="font-bold text-gray-900 block">{{ $wd->user->name ?? '-' }}</span>
                                <span class="text-gray-400 text-[11px]">{{ $wd->user->email ?? '-' }}</span>
                            </td>
                            <td class="p-3.5">
                                <span class="font-semibold text-gray-800 block">{{ $wd->bankAccount->bank_name ?? '-' }}</span>
                                <span class="font-mono text-gray-600">{{ $wd->bankAccount->account_number ?? '-' }}</span>
                                <div class="text-[10px] text-gray-400">a.n {{ $wd->bankAccount->account_name ?? '-' }}</div>
                            </td>
                            <td class="p-3.5 font-extrabold text-gray-900 text-sm">
                                Rp{{ number_format($wd->amount, 0, ',', '.') }}
                            </td>
                            <td class="p-3.5 text-gray-600">
                                <div>{{ $wd->estimated_completion_at ? $wd->estimated_completion_at->format('d M Y H:i') : '-' }}</div>
                                @if($wd->estimated_completion_at && now()->gt($wd->estimated_completion_at) && $wd->status === 'menunggu_proses')
                                    <span class="text-[10px] text-red-600 font-bold block mt-0.5">Mendekati Batas SLA!</span>
                                @endif
                            </td>
                            <td class="p-3.5">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $statusBadge }}">
                                    {{ strtoupper(str_replace('_', ' ', $wd->status)) }}
                                </span>
                            </td>
                            <td class="p-3.5 text-right">
                                @if($wd->status === 'menunggu_proses' || $wd->status === 'diproses')
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Tombol Proses -->
                                        @if($wd->status === 'menunggu_proses')
                                            <form action="{{ route('admin.pencairan.process', $wd->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="diproses">
                                                <button type="submit" class="px-2.5 py-1 text-[11px] font-bold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition" title="Proses Transfer">
                                                    Proses
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Tombol Selesai Transfer -->
                                        <form action="{{ route('admin.pencairan.process', $wd->id) }}" method="POST" onsubmit="return confirm('Konfirmasi bahwa dana telah berhasil ditransfer ke rekening?')">
                                            @csrf
                                            <input type="hidden" name="status" value="selesai">
                                            <button type="submit" class="px-2.5 py-1 text-[11px] font-bold bg-green-600 hover:bg-green-700 text-white rounded-lg transition" title="Selesaikan">
                                                <i class="bi bi-check2"></i> Selesai
                                            </button>
                                        </form>

                                        <!-- Tombol Tolak -->
                                        <button type="button" onclick="openRejectWdModal({{ $wd->id }})" class="px-2.5 py-1 text-[11px] font-bold bg-red-100 text-red-700 hover:bg-red-200 rounded-lg transition" title="Tolak">
                                            Tolak
                                        </button>
                                    </div>
                                @else
                                    <span class="text-[11px] text-gray-400">Selesai Ditinjau</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-500">
                                Tidak ada permohonan pencairan dana.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-bjbo-border">
            {{ $withdrawals->links() }}
        </div>
    </div>
</div>

<!-- Modal Tolak Pencairan -->
<div id="rejectWdModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-xl relative animate-fade-in">
        <h3 class="text-sm font-bold text-gray-900 mb-2">Tolak Permohonan Pencairan Dana</h3>
        <p class="text-xs text-gray-500 mb-4">Saldo akan otomatis dikembalikan ke dompet Rekber penjual.</p>

        <form id="rejectWdForm" method="POST" class="space-y-3">
            @csrf
            <input type="hidden" name="status" value="ditolak">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Alasan Penolakan:</label>
                <textarea name="catatan" required rows="2" placeholder="Nama pemilik rekening tidak cocok dengan identitas..." class="w-full text-xs p-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('rejectWdModal').classList.add('hidden')" class="bjbo-btn-outline text-xs py-1.5 px-3">Batal</button>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-xs py-1.5 px-4 font-bold rounded-lg">Kirim Penolakan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRejectWdModal(id) {
        const modal = document.getElementById('rejectWdModal');
        const form = document.getElementById('rejectWdForm');
        form.action = `/admin/pencairan/${id}/proses`;
        modal.classList.remove('hidden');
    }
</script>
@endsection
