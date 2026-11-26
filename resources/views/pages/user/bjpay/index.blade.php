<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dompet Digital BJPay - BJBO Sedagho</title>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" integrity="sha384-QuGBSgV5Im3DzL2z+8Ko9/hqNy/N0O7zwvXAtfd1MvPKWa/UbeLV65cfm4BV5Wgq" crossorigin="anonymous">
    <!-- Vite + Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-zinc-50 text-zinc-900 font-sans min-h-screen flex flex-col">

@include('sweetalert::alert')

<!-- Komponen Navbar Pengguna -->
@include('components.navbar-user')

<main class="container max-w-4xl mx-auto px-4 py-6 sm:py-8 flex-grow">
    <!-- Breadcrumb & Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center gap-2 text-xs text-zinc-500 mb-1">
                <a href="{{ route('user.dashboard') }}" class="hover:text-zinc-800">Beranda</a>
                <span>/</span>
                <span class="text-zinc-800 font-medium">Dompet Digital BJPay</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900">Dompet Digital BJPay</h1>
            <p class="text-xs sm:text-sm text-zinc-500">Saldo resmi BJBO untuk kemudahan transaksi cepat dan hemat biaya kirim.</p>
        </div>
        <div>
            <a href="{{ route('user.pesanan.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg border border-zinc-300 bg-white hover:bg-zinc-100 text-zinc-700 shadow-xs transition">
                <i class="bi bi-bag-check"></i> Riwayat Pesanan
            </a>
        </div>
    </div>

    <!-- Grid Kartu Saldo & Promo Benefit (Shadcn Style) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 mb-8">
        <!-- Kartu Saldo BJPay -->
        <div class="md:col-span-2 bg-gradient-to-br from-blue-900 to-indigo-950 text-white rounded-2xl p-6 sm:p-7 shadow-sm relative overflow-hidden flex flex-col justify-between">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-white/10 backdrop-blur-xs flex items-center justify-center text-white">
                            <i class="bi bi-wallet2 text-lg"></i>
                        </div>
                        <span class="text-xs font-bold tracking-wider text-blue-200 uppercase font-mono">BJPay Balance</span>
                    </div>
                    <span class="text-[10px] bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 font-bold px-2.5 py-0.5 rounded-full">
                        Aktif & Terverifikasi
                    </span>
                </div>
                <p class="text-xs text-blue-200 mb-1">Total Saldo Aktif Anda</p>
                <h2 class="text-3xl sm:text-4xl font-extrabold font-mono tracking-tight text-white mb-4">
                    Rp {{ number_format($saldo, 0, ',', '.') }}
                </h2>
            </div>

            <div class="relative z-10 pt-4 border-t border-white/10 flex flex-wrap items-center justify-between gap-3 text-xs text-blue-200">
                <div class="flex items-center gap-2">
                    <i class="bi bi-shield-check text-emerald-400"></i>
                    <span>Tersimpan aman di sistem pembayaran BJBO</span>
                </div>
                <div class="text-[11px] font-mono text-white/70">
                    ID Akun: #{{ str_pad((string)$user->id, 6, '0', STR_PAD_LEFT) }}
                </div>
            </div>

            <!-- Background Decorative Shape -->
            <div class="absolute -right-10 -bottom-10 w-44 h-44 rounded-full bg-white/5 pointer-events-none"></div>
        </div>

        <!-- Kartu Keuntungan BJPay -->
        <div class="bg-white border border-zinc-200 rounded-2xl p-6 shadow-xs flex flex-col justify-between">
            <div>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center text-xl mb-3">
                    <i class="bi bi-truck"></i>
                </div>
                <h3 class="font-bold text-sm text-zinc-900 mb-1.5">Diskon Ongkir Khusus</h3>
                <p class="text-xs text-zinc-500 leading-relaxed">
                    Setiap pembayaran menggunakan BJPay otomatis mendapatkan potongan biaya kirim sebesar <strong class="text-zinc-800">Rp 5.000</strong> langsung pada saat checkout.
                </p>
            </div>
            <div class="pt-4 mt-4 border-t border-zinc-100 text-[11px] text-zinc-400">
                <i class="bi bi-info-circle mr-1"></i> Berlaku untuk seluruh kurir lokal di wilayah Bengkalis.
            </div>
        </div>
    </div>

    <!-- Form Top Up Saldo BJPay (Shadcn UI Form Style) -->
    <div class="bg-white border border-zinc-200 rounded-2xl p-6 sm:p-7 shadow-xs mb-8">
        <div class="border-b border-zinc-200 pb-4 mb-5">
            <h3 class="font-bold text-base text-zinc-900 mb-1">Isi Saldo (Top Up) BJPay</h3>
            <p class="text-xs text-zinc-500">Pilih nominal cepat atau masukkan jumlah saldo yang ingin Anda tambahkan.</p>
        </div>

        <form action="{{ route('user.bjpay.topup') }}" method="POST">
            @csrf
            <!-- Tombol Nominal Cepat -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 mb-4">
                <button type="button" onclick="setNominal(20000)" class="quick-btn p-3 text-center border border-zinc-200 rounded-xl hover:border-zinc-900 hover:bg-zinc-50 transition text-xs font-semibold text-zinc-800">
                    Rp 20.000
                </button>
                <button type="button" onclick="setNominal(50000)" class="quick-btn p-3 text-center border border-zinc-200 rounded-xl hover:border-zinc-900 hover:bg-zinc-50 transition text-xs font-semibold text-zinc-800">
                    Rp 50.000
                </button>
                <button type="button" onclick="setNominal(100000)" class="quick-btn p-3 text-center border border-zinc-200 rounded-xl hover:border-zinc-900 hover:bg-zinc-50 transition text-xs font-semibold text-zinc-800">
                    Rp 100.000
                </button>
                <button type="button" onclick="setNominal(200000)" class="quick-btn p-3 text-center border border-zinc-200 rounded-xl hover:border-zinc-900 hover:bg-zinc-50 transition text-xs font-semibold text-zinc-800">
                    Rp 200.000
                </button>
            </div>

            <!-- Input Manual Nominal -->
            <div class="mb-5">
                <label for="nominal" class="block text-xs font-bold text-zinc-700 mb-1.5">Nominal Saldo (Rp)</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-zinc-400">Rp</span>
                    <input type="number" id="nominal" name="nominal" min="10000" max="10000000" step="5000" class="w-full pl-10 pr-3.5 py-2.5 text-sm font-mono border border-zinc-300 rounded-lg focus:border-zinc-900 focus:ring-1 focus:ring-zinc-900 focus:outline-none transition" placeholder="Minimal 10.000" required>
                </div>
                <p class="text-[11px] text-zinc-400 mt-1">Maksimal top up Rp 10.000.000 per transaksi.</p>
            </div>

            <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-lg bg-zinc-900 hover:bg-zinc-800 text-white font-semibold text-xs shadow-xs transition">
                <i class="bi bi-plus-circle"></i> Proses Top Up Saldo
            </button>
        </form>
    </div>

    <!-- Riwayat Transaksi Menggunakan BJPay -->
    <div class="bg-white border border-zinc-200 rounded-2xl p-6 shadow-xs">
        <h3 class="font-bold text-base text-zinc-900 mb-4">Riwayat Penggunaan BJPay</h3>

        @if($invoices->count() > 0)
            <div class="divide-y divide-zinc-100">
                @foreach($invoices as $inv)
                    <div class="py-3.5 flex items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-700 flex items-center justify-center flex-shrink-0 text-sm">
                                <i class="bi bi-arrow-up-right"></i>
                            </div>
                            <div>
                                <p class="font-bold text-zinc-900">{{ $inv->invoice_number }}</p>
                                <p class="text-[11px] text-zinc-500">
                                    {{ $inv->created_at ? $inv->created_at->format('d M Y, H:i') : '-' }} &bull; Penjual: {{ $inv->seller->name ?? 'Warga Sedagho' }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold font-mono text-zinc-900">-{{ $inv->formatted_total }}</p>
                            <a href="{{ route('user.pesanan.invoice', $inv->pesanan_id) }}" class="text-[11px] text-blue-700 hover:underline">
                                Lihat Struk
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-10 text-zinc-400">
                <i class="bi bi-clock-history text-3xl mb-2 block text-zinc-300"></i>
                <p class="text-xs font-medium">Belum ada riwayat transaksi belanja dengan BJPay.</p>
            </div>
        @endif
    </div>
</main>

@include('components.footer')

<script>
    function setNominal(amount) {
        document.getElementById('nominal').value = amount;
    }
</script>

</body>
</html>

