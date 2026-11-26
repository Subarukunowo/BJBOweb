<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Digital & Invoice - {{ $invoice->invoice_number }}</title>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" integrity="sha384-QuGBSgV5Im3DzL2z+8Ko9/hqNy/N0O7zwvXAtfd1MvPKWa/UbeLV65cfm4BV5Wgq" crossorigin="anonymous">
    <!-- Vite + Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            body {
                background: white !important;
                color: black !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .print-container {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 1.5rem !important;
            }
        }
    </style>
</head>
<body class="bg-zinc-50 text-zinc-900 font-sans min-h-screen py-6 px-3 sm:px-6">

<div class="max-w-2xl mx-auto">
    <!-- Tombol Navigasi Atas (Disembunyikan saat print) -->
    <div class="no-print mb-4 flex items-center justify-between gap-2">
        <a href="{{ route('user.pesanan.detail', $pesanan->id) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-600 hover:text-zinc-900 transition">
            <i class="bi bi-arrow-left"></i> Kembali ke Rincian Pesanan
        </a>
        <div class="flex items-center gap-2">
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-md border border-zinc-300 bg-white hover:bg-zinc-100 text-zinc-800 shadow-xs transition">
                <i class="bi bi-printer"></i> Cetak Struk
            </button>
            <a href="{{ route('user.pesanan.track', $pesanan->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-md bg-zinc-900 hover:bg-zinc-800 text-white shadow-xs transition">
                <i class="bi bi-geo-alt"></i> Lacak Kurir
            </a>
        </div>
    </div>

    <!-- Kontainer Kartu Struk Digital & Invoice Resmi (Shadcn Style) -->
    <div class="print-container bg-white border border-zinc-200 rounded-2xl p-6 sm:p-8 shadow-sm">
        <!-- Header Struk -->
        <div class="flex items-start justify-between border-b border-zinc-200 pb-6 mb-6 gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <img src="{{ asset('images/logo.png') }}" alt="BJBO Logo" class="h-10 w-auto object-contain">
                    <span class="text-xs font-mono font-bold px-2 py-0.5 rounded bg-zinc-100 text-zinc-700 border border-zinc-200">
                        OFFICIAL RECEIPT
                    </span>
                </div>
                <p class="text-xs text-zinc-500">Pasar Daring Komunitas Sedagho Bengkalis</p>
                <p class="text-[11px] font-mono text-zinc-400 mt-0.5">Token: {{ substr($invoice->receipt_token, 0, 16) }}...</p>
            </div>
            <div class="text-right">
                <h1 class="text-base sm:text-lg font-bold font-mono text-zinc-900">{{ $invoice->invoice_number }}</h1>
                <p class="text-xs text-zinc-500 mt-0.5">Diterbitkan: {{ $invoice->created_at ? $invoice->created_at->format('d M Y, H:i') : now()->format('d M Y, H:i') }} WIB</p>
                <div class="mt-2">
                    @if($invoice->status === 'lunas' || $pesanan->status === 'paid')
                        <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-full">
                            <i class="bi bi-check-circle-fill"></i> LUNAS
                        </span>
                    @elseif($invoice->status === 'batal' || $pesanan->status === 'cancelled')
                        <span class="inline-flex items-center gap-1 text-xs font-bold text-red-700 bg-red-50 border border-red-200 px-2.5 py-1 rounded-full">
                            <i class="bi bi-x-circle-fill"></i> DIBATALKAN
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-xs font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2.5 py-1 rounded-full">
                            <i class="bi bi-clock-history"></i> MENUNGGU PEMBAYARAN
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Info Pembeli, Penjual & Metode Pembayaran -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 border-b border-zinc-200 pb-6 mb-6 text-xs">
            <div>
                <p class="text-zinc-400 uppercase font-bold text-[10px] tracking-wider mb-1">Diterbitkan Untuk</p>
                <p class="font-bold text-zinc-900">{{ $invoice->user->name ?? 'Pembeli' }}</p>
                <p class="text-zinc-500">{{ $invoice->user->email ?? '-' }}</p>
                <p class="text-zinc-500 mt-1">{{ $pesanan->destination_address ?? 'Bengkalis, Riau' }}</p>
            </div>
            <div>
                <p class="text-zinc-400 uppercase font-bold text-[10px] tracking-wider mb-1">Penyedia / Penjual</p>
                <p class="font-bold text-zinc-900">{{ $invoice->seller->store->nama_toko ?? ($invoice->seller->name ?? 'Penjual Sedagho') }}</p>
                <p class="text-zinc-500">{{ $invoice->seller->email ?? '-' }}</p>
                <p class="text-zinc-500 mt-1">{{ $pesanan->origin_address ?? 'Bengkalis Kota' }}</p>
            </div>
            <div>
                <p class="text-zinc-400 uppercase font-bold text-[10px] tracking-wider mb-1">Metode & Status</p>
                <div class="font-semibold text-zinc-800">
                    @if($invoice->metode_pembayaran === 'bjpay')
                        <span class="inline-flex items-center gap-1 text-blue-700">
                            <i class="bi bi-wallet2"></i> BJPay Digital Wallet
                        </span>
                    @elseif($invoice->metode_pembayaran === 'cod')
                        <span class="inline-flex items-center gap-1 text-amber-700">
                            <i class="bi bi-cash-stack"></i> COD (Bayar di Tempat)
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-zinc-700">
                            <i class="bi bi-qr-code"></i> Transfer QRIS / Bank
                        </span>
                    @endif
                </div>
                @if($invoice->paid_at)
                    <p class="text-[11px] text-zinc-500 mt-1">Lunas pada: {{ $invoice->paid_at->format('d M Y, H:i') }} WIB</p>
                @endif
                <p class="text-[11px] text-zinc-500 mt-0.5">Kurir: {{ $pesanan->kurir_name ?? 'Kurir Lokal' }}</p>
                @if($pesanan->nomor_resi)
                    <p class="text-[11px] font-mono text-zinc-600">Resi: {{ $pesanan->nomor_resi }}</p>
                @endif
            </div>
        </div>

        <!-- Tabel Rincian Barang (Shadcn Style) -->
        <div class="border-b border-zinc-200 pb-6 mb-6">
            <h3 class="text-xs uppercase font-bold text-zinc-400 tracking-wider mb-3">Item Pesanan</h3>
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="border-b border-zinc-200 text-zinc-500">
                        <th class="py-2 font-medium">Barang</th>
                        <th class="py-2 text-center font-medium">Kondisi</th>
                        <th class="py-2 text-right font-medium">Jumlah</th>
                        <th class="py-2 text-right font-medium">Harga</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    <tr>
                        <td class="py-3 pr-2">
                            <p class="font-bold text-zinc-900 text-sm">{{ $pesanan->postingan->name ?? 'Produk Komunitas' }}</p>
                            <p class="text-[11px] text-zinc-500">Kategori: {{ $pesanan->postingan->category ?? 'Umum' }}</p>
                            @if($invoice->catatan)
                                <p class="text-[11px] text-zinc-400 italic mt-0.5">Catatan: {{ $invoice->catatan }}</p>
                            @endif
                        </td>
                        <td class="py-3 text-center">
                            <span class="inline-block px-2 py-0.5 rounded text-[11px] bg-zinc-100 text-zinc-700 font-medium">
                                {{ $pesanan->postingan->kondisi ?? 'Bekas' }}
                            </span>
                        </td>
                        <td class="py-3 text-right font-mono">1</td>
                        <td class="py-3 text-right font-mono font-bold text-zinc-900">{{ $invoice->formatted_subtotal }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Rincian Biaya & Diskon (Tabular Numbers) -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 border-b border-zinc-200 pb-6 mb-6">
            <!-- Barcode & Verifikasi Struk -->
            <div class="w-full sm:w-1/2">
                <div class="p-3 bg-zinc-50 border border-zinc-200 rounded-xl flex items-center gap-3">
                    <div class="w-12 h-12 bg-white border border-zinc-300 rounded flex items-center justify-center text-zinc-800 text-2xl flex-shrink-0">
                        <i class="bi bi-qr-code"></i>
                    </div>
                    <div class="min-w-0 flex-1 text-[11px]">
                        <p class="font-bold text-zinc-800">Verifikasi Struk Digital</p>
                        <p class="text-zinc-500 truncate font-mono">{{ $invoice->receipt_token }}</p>
                        <p class="text-[10px] text-emerald-600 font-semibold mt-0.5">Sah & terdaftar di basis data BJBO Sedagho</p>
                    </div>
                </div>
            </div>

            <!-- Kalkulasi Biaya -->
            <div class="w-full sm:w-1/2 space-y-2 text-xs">
                <div class="flex justify-between text-zinc-600">
                    <span>Subtotal Barang:</span>
                    <span class="font-mono">{{ $invoice->formatted_subtotal }}</span>
                </div>
                <div class="flex justify-between text-zinc-600">
                    <span>Biaya Pengiriman (Ongkir):</span>
                    <span class="font-mono">{{ $invoice->formatted_ongkir }}</span>
                </div>
                @if($invoice->diskon_voucher > 0)
                    <div class="flex justify-between text-emerald-600 font-medium">
                        <span>Diskon Voucher Promo:</span>
                        <span class="font-mono">-Rp {{ number_format($invoice->diskon_voucher, 0, ',', '.') }}</span>
                    </div>
                @endif
                @if($invoice->diskon_ongkir > 0)
                    <div class="flex justify-between text-blue-600 font-medium">
                        <span>Diskon Ongkir BJPay:</span>
                        <span class="font-mono">-Rp {{ number_format($invoice->diskon_ongkir, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-base font-extrabold text-zinc-900 border-t border-zinc-200 pt-2">
                    <span>Total Pembayaran:</span>
                    <span class="font-mono text-bjbo-primary">{{ $invoice->formatted_total }}</span>
                </div>
            </div>
        </div>

        <!-- Footer Struk -->
        <div class="text-center text-zinc-400 text-[11px] leading-relaxed">
            <p>Terima kasih telah bertransaksi di BJBO Sedagho. Simpan struk ini sebagai bukti pembayaran yang sah.</p>
            <p class="font-mono mt-1">&copy; {{ date('Y') }} PT Subaru Technologi Bangsa - BJBO Sedagho Bengkalis</p>
        </div>
    </div>
</div>

</body>
</html>

