<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan #{{ $pesanan->order_number }} - BJBO Sedagho</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Midtrans Snap JS (In-Page Popup Tanpa Redirect) -->
    <script src="{{ $isProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ $clientKey }}"></script>
</head>
<body class="bg-bjbo-bg text-gray-900 font-sans min-h-screen flex flex-col">

@include('sweetalert::alert')

<!-- Header Navigasi -->
@include('components.navbar-user')

<main class="container mx-auto px-4 my-6 flex-grow max-w-3xl">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <a href="{{ route('user.pesanan.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-bjbo-primary transition mb-1">
                <i class="bi bi-arrow-left"></i> Semua Pesanan
            </a>
            <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 flex items-center gap-2">
                Pesanan #{{ $pesanan->order_number }}
            </h1>
        </div>

        <div>
            @if($pesanan->status === 'paid')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-200">
                    <i class="bi bi-check-circle-fill text-green-600"></i> LUNAS TERBAYAR
                </span>
            @elseif($pesanan->status === 'pending')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                    <i class="bi bi-clock-history text-amber-600"></i> MENUNGGU PEMBAYARAN
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800 border border-gray-200">
                    {{ strtoupper($pesanan->status) }}
                </span>
            @endif
        </div>
    </div>

    <!-- Kotak Faktur Pesanan -->
    <div class="bg-white border border-bjbo-border rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
        <!-- Info Waktu & Transaksi -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pb-4 border-b border-bjbo-border text-xs">
            <div>
                <span class="text-gray-400 block">Nomor Pesanan</span>
                <span class="font-mono font-bold text-gray-800">{{ $pesanan->order_number }}</span>
            </div>
            <div>
                <span class="text-gray-400 block">Waktu Dibuat</span>
                <span class="font-medium text-gray-800">{{ $pesanan->created_at->format('d M Y, H:i') }}</span>
            </div>
            <div>
                <span class="text-gray-400 block">Metode Pembayaran</span>
                <span class="font-bold text-bjbo-primary">{{ strtoupper($pesanan->payment_type ?? 'QRIS / SNAP') }}</span>
            </div>
            <div>
                <span class="text-gray-400 block">Status Pembayaran</span>
                <span class="font-bold {{ $pesanan->status === 'paid' ? 'text-green-600' : 'text-amber-600' }}">
                    {{ $pesanan->status === 'paid' ? 'Berhasil Terverifikasi' : 'Belum Dibayar' }}
                </span>
            </div>
        </div>

        <!-- Rincian Produk -->
        <div>
            <h2 class="text-xs font-bold text-gray-800 uppercase tracking-wider mb-3">Rincian Barang</h2>
            <div class="flex items-center gap-4 p-4 rounded-xl bg-gray-50 border border-bjbo-border">
                <img src="{{ $pesanan->postingan->image_url }}" alt="{{ $pesanan->postingan->name }}" class="w-16 h-16 rounded-lg object-cover border border-bjbo-border flex-shrink-0">
                <div class="flex-grow">
                    <h3 class="font-bold text-sm text-gray-900">{{ $pesanan->postingan->name }}</h3>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Kategori: {{ $pesanan->postingan->category }} &bull; Lokasi: {{ $pesanan->postingan->lokasi }}
                    </p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Penjual: <strong>{{ $pesanan->penjual->name ?? 'Warga BJBO' }}</strong>
                    </p>
                </div>
                <div class="text-right">
                    <span class="text-xs text-gray-400 block">1 barang</span>
                    <span class="bjbo-price text-sm">Rp{{ number_format($pesanan->total_price, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        @if($pesanan->catatan)
            <div class="p-3.5 rounded-xl bg-gray-50 border border-bjbo-border text-xs">
                <span class="font-bold text-gray-700 block mb-1">Catatan Pembeli:</span>
                <p class="text-gray-600">{{ $pesanan->catatan }}</p>
            </div>
        @endif

        <!-- Rincian Total Bayar -->
        <div class="pt-2 border-t border-bjbo-border space-y-2 text-xs">
            <div class="flex justify-between text-gray-600">
                <span>Subtotal Barang</span>
                <span class="font-medium text-gray-800">Rp{{ number_format($pesanan->total_price, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-gray-600">
                <span>Biaya Layanan Gerbang Pembayaran</span>
                <span class="font-bold text-green-600">Gratis (Rp0)</span>
            </div>
            <div class="flex justify-between items-center text-sm font-bold text-gray-900 pt-2 border-t border-bjbo-border">
                <span>Total Tagihan</span>
                <span class="bjbo-price text-xl">Rp{{ number_format($pesanan->total_price, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Tombol Aksi Pembayaran & Transaksi -->
        <div class="pt-4 border-t border-bjbo-border">
            @if($pesanan->status === 'pending')
                <div class="space-y-3">
                    <div class="p-3.5 rounded-xl bg-blue-50/70 border border-blue-200 text-xs text-blue-900">
                        <p class="font-semibold mb-1 flex items-center gap-1.5">
                            <i class="bi bi-info-circle-fill text-blue-600"></i> Pembayaran In-Page Tanpa Redirect
                        </p>
                        <p class="leading-relaxed">
                            Pilih tombol di bawah. Jendela pembayaran Midtrans Snap akan muncul langsung di atas layar tanpa meninggalkan halaman BJBO. Anda dapat memilih QRIS, GoPay, atau Transfer Bank.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Tombol Snap Midtrans Popup -->
                        <button type="button" id="pay-snap-btn" class="bjbo-btn-accent w-full py-3 text-sm font-bold shadow hover:shadow-md">
                            <i class="bi bi-credit-card"></i> Bayar via Midtrans Snap Popup
                        </button>

                        <!-- Tombol QRIS Cepat Gratis / Simulasi Sandbox -->
                        <button type="button" id="open-qris-modal-btn" class="inline-flex items-center justify-center gap-2 px-4 py-3 font-bold text-sm rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition shadow">
                            <i class="bi bi-qr-code-scan"></i> Bayar Cepat QRIS (Gratis)
                        </button>
                    </div>
                </div>
            @else
                <!-- Pesanan Sudah Lunas -->
                <div class="p-4 rounded-xl bg-green-50 border border-green-200 text-xs text-green-800 space-y-3">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-check-circle-fill text-green-600 text-lg"></i>
                        <div>
                            <p class="font-bold text-sm">Pembayaran Berhasil Diverifikasi!</p>
                            <p class="text-green-700">Terima kasih atas transaksi Anda di BJBO Sedagho. Silakan hubungi penjual untuk pengambilan atau pengiriman barang.</p>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-green-200 flex flex-wrap items-center gap-3">
                        <a href="{{ route('user.chat.room', [$pesanan->penjual_id, $pesanan->postingan_id]) }}" class="bjbo-btn-primary text-xs py-2 px-4 flex items-center gap-1.5">
                            <i class="bi bi-chat-dots-fill"></i> Chat Penjual di BJBO
                        </a>
                        <a href="{{ route('user.detail.postingan', $pesanan->postingan_id) }}" class="bjbo-btn-outline text-xs py-2 px-4">
                            Lihat Halaman Barang
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</main>

<!-- In-Page QRIS Modal (Pure Tailwind & Vanilla JS, Gratis & Instant) -->
<div id="qrisModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-bjbo-border max-w-sm w-full overflow-hidden text-center p-6">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-mono font-bold text-gray-500 uppercase">Standar QRIS Nasional</span>
            <button type="button" id="close-qris-modal-btn" class="text-gray-400 hover:text-gray-700 text-sm">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="my-2">
            <img src="{{ asset('images/bjbo.png') }}" alt="BJBO QRIS" class="h-8 mx-auto object-contain mb-1">
            <h4 class="font-bold text-sm text-gray-900">QRIS Pasar BJBO Sedagho</h4>
            <p class="text-[11px] text-gray-500">NMID: ID1020261027891 &bull; A.N. PT SUBARU TECH</p>
        </div>

        <!-- Render Simulasi Gambar QR Code -->
        <div class="my-4 p-4 bg-gray-50 border border-bjbo-border rounded-xl inline-block shadow-inner">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=BJBO-ORDER-{{ $pesanan->order_number }}-TOTAL-{{ $pesanan->total_price }}" alt="Kode QRIS" class="w-44 h-44 mx-auto rounded">
        </div>

        <div class="mb-4">
            <span class="text-xs text-gray-500 block">Jumlah Pembayaran Tepat</span>
            <span class="bjbo-price text-xl font-bold">Rp{{ number_format($pesanan->total_price, 0, ',', '.') }}</span>
        </div>

        <div class="p-2.5 rounded-lg bg-amber-50 border border-amber-200 text-[11px] text-amber-800 text-left mb-4">
            <i class="bi bi-info-circle-fill text-amber-600"></i> Mode Sandbox Gratis: Anda dapat menguji alur verifikasi QRIS ini langsung tanpa memotong saldo nyata.
        </div>

        <form action="{{ route('user.pesanan.simulate-qris', $pesanan->id) }}" method="POST">
            @csrf
            <button type="submit" class="w-full py-2.5 px-4 font-bold text-xs rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition shadow">
                <i class="bi bi-check2-circle"></i> Konfirmasi Pembayaran Berhasil (Simulasi)
            </button>
        </form>
    </div>
</div>

@include('components.footer')

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Logika Midtrans Snap Popup (In-Page Popup Tanpa Redirect)
        const snapBtn = document.getElementById('pay-snap-btn');
        if (snapBtn) {
            snapBtn.addEventListener('click', function () {
                const snapToken = '{{ $pesanan->snap_token }}';

                // Jika token simulasi atau credentials kosong, langsung buka modal QRIS
                if (!snapToken || snapToken.startsWith('SIMULASI-QRIS') || typeof window.snap === 'undefined') {
                    openQrisModal();
                    return;
                }

                // Panggil Midtrans Snap Popup resmi
                window.snap.pay(snapToken, {
                    onSuccess: function (result) {
                        submitPaymentFinish(result.payment_type || 'qris', result.transaction_id || '');
                    },
                    onPending: function (result) {
                        alert('Menunggu penyelesaian pembayaran QRIS Anda.');
                    },
                    onError: function (result) {
                        alert('Pembayaran gagal atau dibatalkan.');
                    },
                    onClose: function () {
                        console.log('Jendela Snap Midtrans ditutup.');
                    }
                });
            });
        }

        // Modal QRIS Gratis / Sandbox
        const qrisModal = document.getElementById('qrisModal');
        const openQrisBtn = document.getElementById('open-qris-modal-btn');
        const closeQrisBtn = document.getElementById('close-qris-modal-btn');

        function openQrisModal() {
            if (qrisModal) {
                qrisModal.classList.remove('hidden');
                qrisModal.classList.add('flex');
            }
        }

        function closeQrisModal() {
            if (qrisModal) {
                qrisModal.classList.add('hidden');
                qrisModal.classList.remove('flex');
            }
        }

        if (openQrisBtn) {
            openQrisBtn.addEventListener('click', openQrisModal);
        }

        if (closeQrisBtn) {
            closeQrisBtn.addEventListener('click', closeQrisModal);
        }

        if (qrisModal) {
            qrisModal.addEventListener('click', (e) => {
                if (e.target === qrisModal) {
                    closeQrisModal();
                }
            });
        }

        // Helper kirim form konfirmasi finish
        function submitPaymentFinish(paymentType, transactionId) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("user.pesanan.finish", $pesanan->id) }}';

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);

            const pt = document.createElement('input');
            pt.type = 'hidden';
            pt.name = 'payment_type';
            pt.value = paymentType;
            form.appendChild(pt);

            const tid = document.createElement('input');
            tid.type = 'hidden';
            tid.name = 'transaction_id';
            tid.value = transactionId;
            form.appendChild(tid);

            document.body.appendChild(form);
            form.submit();
        }
    });
</script>

</body>
</html>
