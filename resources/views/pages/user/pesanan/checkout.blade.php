<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Pesanan - BJBO Sedagho</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bjbo-bg text-gray-900 font-sans min-h-screen flex flex-col">

@include('sweetalert::alert')

<!-- Header Navigasi -->
@include('components.navbar-user')

<main class="container mx-auto px-3 sm:px-4 my-4 sm:my-6 flex-grow max-w-4xl pb-16 lg:pb-8">
    <!-- Breadcrumb & Judul -->
    <div class="mb-6">
        <a href="{{ route('user.detail.postingan', $postingan->id) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-bjbo-primary transition mb-2">
            <i class="bi bi-arrow-left"></i> Kembali ke Detail Barang
        </a>
        <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
            Checkout Pesanan Online
        </h1>
        <p class="text-gray-500 text-xs mt-0.5">Selesaikan pesanan Anda dengan pembayaran aman via QRIS atau Midtrans Popup.</p>
    </div>

    <form action="{{ route('user.pesanan.store', $postingan->id) }}" method="POST" class="grid grid-cols-1 md:grid-cols-12 gap-6">
        @csrf

        <!-- Rincian Barang & Catatan -->
        <div class="md:col-span-7 space-y-4">
            <!-- Kartu Barang -->
            <div class="bg-white border border-bjbo-border rounded-2xl p-5 shadow-sm">
                <h2 class="text-xs font-bold text-gray-800 uppercase tracking-wider mb-3">Barang yang Dipesan</h2>
                <div class="flex gap-4">
                    <img src="{{ $postingan->image_url }}" alt="{{ $postingan->name }}" class="w-20 h-20 rounded-xl object-cover border border-bjbo-border flex-shrink-0">
                    <div class="flex-grow">
                        <span class="inline-block text-[11px] font-semibold text-bjbo-primary bg-bjbo-primary-light px-2 py-0.5 rounded border border-blue-200 mb-1">
                            {{ $postingan->category }}
                        </span>
                        <h3 class="font-bold text-sm text-gray-900 line-clamp-1">{{ $postingan->name }}</h3>
                        <p class="text-xs text-gray-500 mt-0.5 flex items-center gap-1">
                            <i class="bi bi-geo-alt"></i> {{ $postingan->lokasi }} &bull; Penjual: {{ $postingan->user->name ?? 'Warga' }}
                        </p>
                        <div class="bjbo-price text-base mt-2">
                            Rp{{ number_format($postingan->price, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kartu Pembeli & Catatan -->
            <div class="bg-white border border-bjbo-border rounded-2xl p-5 shadow-sm">
                <h2 class="text-xs font-bold text-gray-800 uppercase tracking-wider mb-3">Informasi Pembeli</h2>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Pembeli</label>
                        <input type="text" class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-bjbo-border-strong rounded-lg text-gray-700 focus:outline-none" value="{{ Auth::user()->name }}" readonly>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Email Notifikasi</label>
                        <input type="email" class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-bjbo-border-strong rounded-lg text-gray-700 focus:outline-none" value="{{ Auth::user()->email }}" readonly>
                    </div>
                    <div>
                        <label for="catatan" class="block text-xs font-semibold text-gray-700 mb-1">Catatan Tambahan untuk Penjual (Opsional)</label>
                        <textarea id="catatan" name="catatan" rows="3" class="w-full px-3.5 py-2 text-sm bg-white border border-bjbo-border-strong rounded-lg focus:border-bjbo-primary focus:ring-2 focus:ring-bjbo-primary/20 focus:outline-none transition" placeholder="Contoh: Titip di pos ronda atau jadwalkan ketemuan sore hari..."></textarea>
                    </div>

                    <!-- Voucher & Kode Promo -->
                    <div class="pt-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Punya Kode Promo / Voucher?</label>
                        <div class="flex gap-2">
                            <input type="text" id="promoInput" name="promo_code" placeholder="Masukkan kode promo (misal: DISKON10)" class="flex-grow px-3 py-2 text-xs uppercase bg-gray-50 border border-bjbo-border-strong rounded-lg focus:bg-white focus:border-bjbo-primary focus:outline-none font-mono">
                            <button type="button" onclick="applyPromoCode()" class="bjbo-btn-primary text-xs py-2 px-4 font-bold flex-shrink-0">
                                Gunakan
                            </button>
                        </div>
                        <p id="promoMessage" class="hidden text-xs mt-1.5"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ringkasan Biaya & Tombol Bayar -->
        <div class="md:col-span-5">
            <div class="bg-white border border-bjbo-border rounded-2xl p-5 shadow-sm sticky top-24">
                <h2 class="text-xs font-bold text-gray-800 uppercase tracking-wider mb-3">Ringkasan Pembayaran</h2>

                <div class="space-y-2.5 text-xs text-gray-600 pb-3 border-b border-bjbo-border">
                    <div class="flex justify-between">
                        <span>Harga Barang</span>
                        <span class="font-medium text-gray-800">Rp{{ number_format($postingan->price, 0, ',', '.') }}</span>
                    </div>
                    <div id="discountRow" class="hidden flex justify-between text-emerald-600 font-semibold">
                        <span>Diskon Promo</span>
                        <span id="discountText">-Rp0</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Biaya Platform BJBO</span>
                        <span class="font-bold text-green-600">Gratis (Rp0)</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Metode Pembayaran</span>
                        <span class="font-medium text-bjbo-primary">QRIS / Midtrans Snap</span>
                    </div>
                </div>

                <div class="flex justify-between items-center py-3 border-b border-bjbo-border">
                    <span class="font-bold text-sm text-gray-900">Total Pembayaran</span>
                    <span id="finalPriceText" class="bjbo-price text-xl">Rp{{ number_format($postingan->price, 0, ',', '.') }}</span>
                </div>

                <div class="my-4 p-3 rounded-xl bg-blue-50/70 border border-blue-200 text-[11px] text-blue-900 flex items-start gap-2">
                    <i class="bi bi-shield-check text-blue-600 text-sm mt-0.5"></i>
                    <span>
                        Pembayaran diproses via <strong>Midtrans Snap Popup</strong> langsung di halaman ini tanpa diarahkan ke web eksternal. Mendukung seluruh aplikasi QRIS (BCA, Mandiri, GoPay, OVO, ShopeePay).
                    </span>
                </div>

                <button type="submit" class="bjbo-btn-accent w-full py-3 text-sm font-bold shadow hover:shadow-md">
                    <i class="bi bi-lock-fill"></i> Buat Pesanan & Bayar
                </button>
            </div>
        </div>
    </form>
</main>

<script>
    const originalPrice = {{ $postingan->price }};

    function applyPromoCode() {
        const code = document.getElementById('promoInput').value.trim();
        const msgEl = document.getElementById('promoMessage');
        const discountRow = document.getElementById('discountRow');
        const discountText = document.getElementById('discountText');
        const finalPriceText = document.getElementById('finalPriceText');

        if (!code) {
            msgEl.className = 'text-xs mt-1.5 text-red-600 block';
            msgEl.innerText = 'Silakan masukkan kode promo.';
            return;
        }

        fetch('{{ route('user.promo.check') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                code: code,
                total_price: originalPrice
            })
        })
        .then(res => res.json())
        .then(data => {
            msgEl.classList.remove('hidden');
            if (data.is_valid) {
                msgEl.className = 'text-xs mt-1.5 text-emerald-600 font-bold block';
                msgEl.innerText = data.message + ` (Hemat Rp${data.discount.toLocaleString('id-ID')})`;

                discountRow.classList.remove('hidden');
                discountText.innerText = `-Rp${data.discount.toLocaleString('id-ID')}`;

                const finalPrice = Math.max(0, originalPrice - data.discount);
                finalPriceText.innerText = `Rp${finalPrice.toLocaleString('id-ID')}`;
            } else {
                msgEl.className = 'text-xs mt-1.5 text-red-600 block';
                msgEl.innerText = data.message;
                discountRow.classList.add('hidden');
                finalPriceText.innerText = `Rp${originalPrice.toLocaleString('id-ID')}`;
            }
        })
        .catch(err => {
            msgEl.className = 'text-xs mt-1.5 text-red-600 block';
            msgEl.innerText = 'Gagal memeriksa kode promo. Silakan coba lagi.';
        });
    }
</script>

@include('components.footer')

</body>
</html>
