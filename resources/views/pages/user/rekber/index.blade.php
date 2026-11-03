<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dompet Rekber Penjual & Pencairan Dana - BJBO Sedagho</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" crossorigin="anonymous">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bjbo-bg text-gray-900 font-sans min-h-screen flex flex-col">

@include('sweetalert::alert')
@include('components.navbar-user')

<main class="container mx-auto px-3 sm:px-4 my-6 flex-grow max-w-5xl">
    <!-- Header Rekber -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-bjbo-border">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-xl flex-shrink-0">
                <i class="bi bi-wallet2"></i>
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Dompet Rekber Penjual</h1>
                <p class="text-xs sm:text-sm text-gray-500">Saldo hasil penjualan aman tersimpan dalam rekening bersama (escrow) BJBO</p>
            </div>
        </div>

        <button type="button" onclick="toggleAddBankModal(true)" class="bjbo-btn-outline text-xs py-2 px-3.5 flex items-center gap-1.5 shadow-xs">
            <i class="bi bi-bank"></i> + Tambah Rekening Bank
        </button>
    </div>

    <!-- Banner Ketentuan 2x24 Jam Kerja -->
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 sm:p-5 mb-6 flex items-start gap-3.5">
        <i class="bi bi-shield-check text-bjbo-primary text-2xl flex-shrink-0 mt-0.5"></i>
        <div>
            <h3 class="text-sm font-bold text-gray-900 mb-1">Ketentuan Pencairan Dana Escrow (2x24 Jam Kerja)</h3>
            <p class="text-xs text-gray-600 leading-relaxed">
                Pencairan dana saldo Rekber hanya dapat dilakukan ke rekening bank yang telah terdaftar. Waktu proses adalah <strong>maksimal 2x24 jam kerja</strong> (tidak termasuk hari Sabtu, Minggu, dan libur nasional). Sistem kami telah siap terintegrasi ke perbankan otomatis.
            </p>
        </div>
    </div>

    <!-- Saldo Card & Tarik Dana Form -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Kartu Saldo -->
        <div class="bg-gradient-to-br from-blue-900 to-indigo-950 text-white rounded-2xl p-6 shadow-md flex flex-col justify-between">
            <div>
                <span class="text-xs font-semibold text-blue-200 uppercase tracking-wider block mb-1">Saldo Rekber Tersedia</span>
                <div class="text-3xl font-extrabold text-white mb-2">Rp{{ number_format($balance, 0, ',', '.') }}</div>
                <p class="text-[11px] text-blue-200">Dana dapat ditarik kapan saja ke rekening bank aktif Anda.</p>
            </div>

            <div class="pt-6 border-t border-blue-800/80 mt-6 flex items-center justify-between text-xs text-blue-200">
                <span>Status Escrow</span>
                <span class="font-bold text-emerald-400 flex items-center gap-1"><i class="bi bi-lock-fill"></i> Terlindungi</span>
            </div>
        </div>

        <!-- Formulir Tarik Dana -->
        <div class="lg:col-span-2 bg-white border border-bjbo-border rounded-2xl p-6 shadow-sm">
            <h2 class="text-base font-bold text-gray-900 mb-4 pb-2 border-b border-bjbo-border flex items-center gap-2">
                <i class="bi bi-cash-coin text-emerald-600"></i> Ajukan Penarikan Dana (Withdrawal)
            </h2>

            @if($bankAccounts->isEmpty())
                <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl text-center">
                    <p class="text-xs text-amber-800 mb-2">Anda belum memiliki rekening bank terdaftar untuk pencairan dana.</p>
                    <button type="button" onclick="toggleAddBankModal(true)" class="bjbo-btn-primary text-xs py-2 px-4 font-bold">
                        Daftarkan Rekening Sekarang
                    </button>
                </div>
            @else
                <form action="{{ route('user.rekber.tarikDana') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Pilih Rekening Bank Tujuan <span class="text-red-500">*</span></label>
                        <select name="bank_account_id" required class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:border-bjbo-primary focus:outline-none">
                            @foreach($bankAccounts as $bank)
                                <option value="{{ $bank->id }}">
                                    {{ $bank->bank_name }} - {{ $bank->account_number }} (a.n {{ $bank->account_name }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Nominal Penarikan (Rp) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-500">Rp</span>
                            <input type="number" name="amount" required min="10000" max="{{ $balance }}" placeholder="Minimal Rp10.000" class="w-full text-xs pl-9 pr-3 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:border-bjbo-primary focus:outline-none">
                        </div>
                        <p class="text-[11px] text-gray-500 mt-1">Maksimal penarikan: Rp{{ number_format($balance, 0, ',', '.') }}</p>
                    </div>

                    <div class="pt-2">
                        <button type="submit" {{ $balance < 10000 ? 'disabled' : '' }} class="bjbo-btn-primary text-xs py-2.5 px-6 font-bold shadow disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="bi bi-arrow-up-right-circle"></i> Tarik Dana Sekarang
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    <!-- Riwayat Pencairan Dana -->
    <div class="bg-white border border-bjbo-border rounded-2xl p-6 shadow-sm mb-10">
        <h3 class="text-base font-bold text-gray-900 mb-4 pb-2 border-b border-bjbo-border flex items-center gap-2">
            <i class="bi bi-clock-history text-bjbo-primary"></i> Riwayat Permintaan Pencairan Dana
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 border-b border-bjbo-border">
                        <th class="p-3">No. Referensi</th>
                        <th class="p-3">Rekening Bank</th>
                        <th class="p-3">Nominal</th>
                        <th class="p-3">Estimasi Selesai (2x24 Jam)</th>
                        <th class="p-3">Status</th>
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
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 font-mono font-bold text-gray-800">{{ $wd->reference_number }}</td>
                            <td class="p-3">
                                <span class="font-semibold text-gray-800 block">{{ $wd->bankAccount->bank_name ?? '-' }}</span>
                                <span class="text-gray-500 text-[11px]">{{ $wd->bankAccount->account_number ?? '-' }} ({{ $wd->bankAccount->account_name ?? '-' }})</span>
                            </td>
                            <td class="p-3 font-bold text-gray-900">Rp{{ number_format($wd->amount, 0, ',', '.') }}</td>
                            <td class="p-3 text-gray-600">
                                {{ $wd->estimated_completion_at ? $wd->estimated_completion_at->format('d M Y H:i') : '-' }}
                            </td>
                            <td class="p-3">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $statusBadge }}">
                                    {{ strtoupper(str_replace('_', ' ', $wd->status)) }}
                                </span>
                                @if($wd->catatan_admin)
                                    <p class="text-[10px] text-gray-500 mt-0.5">{{ $wd->catatan_admin }}</p>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-500">Belum ada riwayat penarikan dana.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $withdrawals->links() }}
        </div>
    </div>
</main>

<!-- Modal Tambah Rekening Bank -->
<div id="addBankModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl relative animate-fade-in">
        <button type="button" onclick="toggleAddBankModal(false)" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
        <h3 class="text-base font-bold text-gray-900 mb-1 flex items-center gap-2">
            <i class="bi bi-bank text-bjbo-primary"></i> Daftarkan Rekening Bank
        </h3>
        <p class="text-xs text-gray-500 mb-4">Gunakan rekening atas nama yang sama untuk mempercepat proses pencairan dana 2x24 jam kerja.</p>

        <form action="{{ route('user.rekber.addBank') }}" method="POST" class="space-y-3.5">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Bank <span class="text-red-500">*</span></label>
                <select name="bank_name" required class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:border-bjbo-primary focus:outline-none">
                    <option value="Bank Riau Kepri Syariah">Bank Riau Kepri Syariah</option>
                    <option value="BCA">BCA (Bank Central Asia)</option>
                    <option value="Mandiri">Bank Mandiri</option>
                    <option value="BRI">BRI (Bank Rakyat Indonesia)</option>
                    <option value="BNI">BNI (Bank Negara Indonesia)</option>
                    <option value="BSI">BSI (Bank Syariah Indonesia)</option>
                    <option value="CIMB Niaga">CIMB Niaga</option>
                    <option value="Permata">Bank Permata</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor Rekening <span class="text-red-500">*</span></label>
                <input type="text" name="account_number" required placeholder="Contoh: 1234567890" class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:border-bjbo-primary focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Pemilik Rekening Sesuai Buku Tabungan <span class="text-red-500">*</span></label>
                <input type="text" name="account_name" required placeholder="Contoh: AHMAD SUBARI" class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:border-bjbo-primary focus:outline-none uppercase">
            </div>

            <div class="pt-3 flex gap-2 justify-end">
                <button type="button" onclick="toggleAddBankModal(false)" class="bjbo-btn-outline text-xs py-2 px-4">Batal</button>
                <button type="submit" class="bjbo-btn-primary text-xs py-2 px-5 font-bold shadow">Simpan Rekening</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleAddBankModal(show) {
        const modal = document.getElementById('addBankModal');
        if (modal) {
            if (show) {
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
            }
        }
    }
</script>

@include('components.footer')
</body>
</html>
