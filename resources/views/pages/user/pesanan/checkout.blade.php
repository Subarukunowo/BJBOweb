<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Pesanan - BJBO Sedagho</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" integrity="sha384-QuGBSgV5Im3DzL2z+8Ko9/hqNy/N0O7zwvXAtfd1MvPKWa/UbeLV65cfm4BV5Wgq" crossorigin="anonymous">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-zinc-50 text-zinc-900 font-sans min-h-screen flex flex-col">

@include('sweetalert::alert')

<!-- Header Navigasi -->
@include('components.navbar-user')

<main class="container mx-auto px-3 sm:px-4 my-4 sm:my-6 flex-grow max-w-4xl pb-16 lg:pb-8">
    <!-- Breadcrumb & Judul -->
    <div class="mb-6">
        <a href="{{ route('user.detail.postingan', $postingan->id) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-500 hover:text-zinc-900 transition mb-2">
            <i class="bi bi-arrow-left"></i> Kembali ke Detail Barang
        </a>
        <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900">
            Checkout Pesanan Online
        </h1>
        <p class="text-zinc-500 text-xs mt-0.5">Pilih metode pembayaran yang nyaman untuk Anda (Transfer QRIS, COD, atau BJPay hemat ongkir).</p>
    </div>

    <form action="{{ route('user.pesanan.store', $postingan->id) }}" method="POST" class="grid grid-cols-1 md:grid-cols-12 gap-6">
        @csrf

        <!-- Rincian Barang & Catatan -->
        <div class="md:col-span-7 space-y-4">
            <!-- Kartu Barang -->
            <div class="bg-white border border-zinc-200 rounded-2xl p-5 shadow-xs">
                <h2 class="text-xs font-bold text-zinc-800 uppercase tracking-wider mb-3">Barang yang Dipesan</h2>
                <div class="flex gap-4">
                    <img src="{{ $postingan->image_url }}" alt="{{ $postingan->name }}" class="w-20 h-20 rounded-xl object-cover border border-zinc-200 flex-shrink-0">
                    <div class="flex-grow">
                        <span class="inline-block text-[11px] font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200 mb-1">
                            {{ $postingan->category }}
                        </span>
                        <h3 class="font-bold text-sm text-zinc-900 line-clamp-1">{{ $postingan->name }}</h3>
                        <p class="text-xs text-zinc-500 mt-0.5 flex items-center gap-1">
                            <i class="bi bi-geo-alt"></i> {{ $postingan->lokasi }} &bull; Penjual: {{ $postingan->user->name ?? 'Warga Sedagho' }}
                        </p>
                        <div class="font-mono text-base font-bold text-bjbo-primary mt-2">
                            Rp{{ number_format($postingan->price, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pilihan Kurir & Ongkos Kirim -->
            <div class="bg-white border border-zinc-200 rounded-2xl p-5 shadow-xs">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-xs font-bold text-zinc-800 uppercase tracking-wider">Pilih Kurir &amp; Pengiriman</h2>
                    <span class="text-[11px] text-zinc-400">Hemat ongkir dengan BJPay</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    @foreach($couriers as $idx => $courier)
                        <label for="kurir_{{ $courier['code'] }}" class="relative flex items-start p-3 border-2 rounded-xl cursor-pointer transition border-zinc-200 hover:border-blue-400 has-[:checked]:border-bjbo-primary has-[:checked]:bg-blue-50/40">
                            <input id="kurir_{{ $courier['code'] }}" type="radio" name="kurir_code" value="{{ $courier['code'] }}" data-ongkir="{{ $courier['ongkir'] }}" class="mt-0.5 h-4 w-4 text-bjbo-primary focus:ring-blue-500 border-zinc-300" onchange="updateCourier({{ $courier['ongkir'] }}, '{{ $courier['name'] }}')" {{ $idx === 0 ? 'checked' : '' }}>
                            <div class="ml-2.5 flex-grow text-xs">
                                <div class="font-bold text-zinc-900">{{ $courier['name'] }}</div>
                                <div class="text-[11px] text-zinc-500 mt-0.5">{{ $courier['estimasi'] }}</div>
                                <div class="font-mono font-bold text-bjbo-primary mt-1">
                                    {{ $courier['ongkir'] > 0 ? 'Rp'.number_format($courier['ongkir'], 0, ',', '.') : 'Gratis (Rp0)' }}
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Pilihan Metode Pembayaran (Shadcn UI Radio Cards) -->
            <div class="bg-white border border-zinc-200 rounded-2xl p-5 shadow-xs">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-xs font-bold text-zinc-800 uppercase tracking-wider">Metode Pembayaran</h2>
                    <span class="text-[11px] text-zinc-400">Pilih salah satu</span>
                </div>

                <div class="space-y-3">
                    <!-- Opsi 1: BJPay (Diskon Ongkir) -->
                    <label for="metode_bjpay" class="payment-option relative flex items-start p-3.5 border-2 rounded-xl cursor-pointer transition border-zinc-200 hover:border-blue-400 has-[:checked]:border-blue-700 has-[:checked]:bg-blue-50/40">
                        <input id="metode_bjpay" type="radio" name="metode_pembayaran" value="bjpay" class="mt-0.5 h-4 w-4 text-blue-600 focus:ring-blue-500 border-zinc-300" onchange="updatePaymentMethod('bjpay')" checked>
                        <div class="ml-3 flex-grow text-xs">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-bold text-zinc-900 flex items-center gap-1.5">
                                    <i class="bi bi-wallet2 text-blue-600"></i> Saldo BJPay (Dompet Digital)
                                </span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    Hemat Ongkir Rp5.000
                                </span>
                            </div>
                            <p class="text-zinc-500 mt-1">Saldo Anda: <strong class="font-mono text-zinc-800">Rp{{ number_format($saldoBjpay, 0, ',', '.') }}</strong></p>
                            @if($saldoBjpay < ($postingan->price + 5000))
                                <p class="text-[11px] text-amber-700 mt-1">
                                    <i class="bi bi-exclamation-triangle"></i> Saldo BJPay mungkin kurang. <a href="{{ route('user.bjpay.index') }}" target="_blank" class="underline font-semibold">Isi saldo sekarang</a>
                                </p>
                            @endif
                        </div>
                    </label>

                    <!-- Opsi 2: COD (Cash on Delivery) -->
                    <label for="metode_cod" class="payment-option relative flex items-start p-3.5 border-2 rounded-xl cursor-pointer transition border-zinc-200 hover:border-zinc-400 has-[:checked]:border-zinc-900 has-[:checked]:bg-zinc-50">
                        <input id="metode_cod" type="radio" name="metode_pembayaran" value="cod" class="mt-0.5 h-4 w-4 text-zinc-900 focus:ring-zinc-900 border-zinc-300" onchange="updatePaymentMethod('cod')">
                        <div class="ml-3 flex-grow text-xs">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-bold text-zinc-900 flex items-center gap-1.5">
                                    <i class="bi bi-cash-stack text-emerald-600"></i> COD (Bayar di Tempat)
                                </span>
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded bg-zinc-100 text-zinc-700 border border-zinc-200">
                                    Bayar Tunai ke Kurir
                                </span>
                            </div>
                            <p class="text-zinc-500 mt-1">Bayar tunai secara langsung saat kurir mengantarkan barang ke alamat Anda di Bengkalis.</p>
                        </div>
                    </label>

                    <!-- Opsi 3: Transfer QRIS / Snap -->
                    <label for="metode_transfer" class="payment-option relative flex items-start p-3.5 border-2 rounded-xl cursor-pointer transition border-zinc-200 hover:border-zinc-400 has-[:checked]:border-zinc-900 has-[:checked]:bg-zinc-50">
                        <input id="metode_transfer" type="radio" name="metode_pembayaran" value="transfer" class="mt-0.5 h-4 w-4 text-zinc-900 focus:ring-zinc-900 border-zinc-300" onchange="updatePaymentMethod('transfer')">
                        <div class="ml-3 flex-grow text-xs">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-bold text-zinc-900 flex items-center gap-1.5">
                                    <i class="bi bi-qr-code text-zinc-800"></i> Transfer QRIS & Rekber
                                </span>
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded bg-zinc-100 text-zinc-700 border border-zinc-200">
                                    Instan & Otomatis
                                </span>
                            </div>
                            <p class="text-zinc-500 mt-1">Pembayaran online aman via QRIS (BCA, Mandiri, BRI, GoPay, OVO, ShopeePay).</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Kartu Pembeli & Catatan -->
            <div class="bg-white border border-zinc-200 rounded-2xl p-5 shadow-xs">
                <h2 class="text-xs font-bold text-zinc-800 uppercase tracking-wider mb-3">Informasi Pengiriman</h2>
                <div class="space-y-3">
                    <div>
                        <label for="buyer_name" class="block text-xs font-semibold text-zinc-700 mb-1">Nama Pembeli</label>
                        <input id="buyer_name" type="text" class="w-full px-3.5 py-2 text-xs bg-zinc-50 border border-zinc-300 rounded-lg text-zinc-700 focus:outline-none" value="{{ Auth::user()->name }}" readonly>
                    </div>
                    <div>
                        <label for="buyer_email" class="block text-xs font-semibold text-zinc-700 mb-1">Email Notifikasi & Struk</label>
                        <input id="buyer_email" type="email" class="w-full px-3.5 py-2 text-xs bg-zinc-50 border border-zinc-300 rounded-lg text-zinc-700 focus:outline-none" value="{{ Auth::user()->email }}" readonly>
                    </div>
                    <div>
                        <label for="catatan" class="block text-xs font-semibold text-zinc-700 mb-1">Catatan Pengiriman untuk Kurir / Penjual (Opsional)</label>
                        <textarea id="catatan" name="catatan" rows="2" class="w-full px-3.5 py-2 text-xs bg-white border border-zinc-300 rounded-lg focus:border-zinc-900 focus:ring-1 focus:ring-zinc-900 focus:outline-none transition" placeholder="Contoh: Alamat patokan dekat musholla, telepon dulu sebelum antar..."></textarea>
                    </div>

                    <!-- Voucher & Kode Promo -->
                    <div class="pt-2 border-t border-zinc-100">
                        <label for="promoInput" class="block text-xs font-semibold text-zinc-700 mb-1">Kode Promo / Voucher</label>
                        <div class="flex gap-2">
                            <input type="text" id="promoInput" name="promo_code" placeholder="Masukkan kode promo (misal: DISKON10)" class="flex-grow px-3 py-2 text-xs uppercase bg-zinc-50 border border-zinc-300 rounded-lg focus:bg-white focus:border-zinc-900 focus:outline-none font-mono">
                            <button type="button" onclick="applyPromoCode()" class="px-4 py-2 text-xs font-semibold rounded-lg bg-zinc-900 hover:bg-zinc-800 text-white transition flex-shrink-0">
                                Gunakan
                            </button>
                        </div>
                        <p id="promoMessage" class="hidden text-xs mt-1.5"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ringkasan Biaya & Tombol Bayar (Shadcn Style) -->
        <div class="md:col-span-5">
            <div class="bg-white border border-zinc-200 rounded-2xl p-5 shadow-xs sticky top-24">
                <h2 class="text-xs font-bold text-zinc-800 uppercase tracking-wider mb-3">Ringkasan Biaya</h2>

                <div class="space-y-2.5 text-xs text-zinc-600 pb-3 border-b border-zinc-200">
                    <div class="flex justify-between">
                        <span>Harga Barang</span>
                        <span class="font-mono font-medium text-zinc-800">Rp{{ number_format($postingan->price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Biaya Pengiriman (<span id="selectedCourierName">Kurir Lokal Sedagho Bengkalis</span>)</span>
                        <span id="ongkirText" class="font-mono font-medium text-zinc-800">Rp10.000</span>
                    </div>
                    <!-- Diskon Ongkir BJPay -->
                    <div id="bjpayDiscountRow" class="flex justify-between text-blue-700 font-semibold">
                        <span>Diskon Ongkir (BJPay)</span>
                        <span id="bjpayDiscountText" class="font-mono">-Rp5.000</span>
                    </div>
                    <!-- Diskon Promo Voucher -->
                    <div id="discountRow" class="hidden flex justify-between text-emerald-600 font-semibold">
                        <span>Diskon Voucher</span>
                        <span id="discountText" class="font-mono">-Rp0</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Biaya Layanan BJBO</span>
                        <span class="font-bold text-emerald-600">Gratis (Rp0)</span>
                    </div>
                </div>

                <div class="flex justify-between items-center py-3 border-b border-zinc-200">
                    <span class="font-bold text-sm text-zinc-900">Total Pembayaran</span>
                    <span id="finalPriceText" class="font-mono text-xl font-extrabold text-bjbo-primary">Rp{{ number_format($postingan->price + 5000, 0, ',', '.') }}</span>
                </div>

                <div class="my-4 p-3 rounded-xl bg-zinc-50 border border-zinc-200 text-[11px] text-zinc-600 flex items-start gap-2">
                    <i class="bi bi-shield-check text-emerald-600 text-sm mt-0.5"></i>
                    <span>
                        Struk digital dan bukti transaksi otomatis diterbitkan setelah pesanan dikonfirmasi. Dana diamankan dalam sistem Rekber terpercaya BJBO.
                    </span>
                </div>

                <button type="submit" class="w-full py-3 text-xs sm:text-sm font-bold rounded-lg bg-zinc-900 hover:bg-zinc-800 text-white shadow-xs transition flex items-center justify-center gap-1.5">
                    <i class="bi bi-bag-check-fill"></i> Konfirmasi & Buat Pesanan
                </button>
            </div>
        </div>
    </form>
</main>

<script>
    const itemPrice = {{ $postingan->price }};
    let currentOngkir = 10000;
    let selectedPayment = 'bjpay';
    let bjpayShippingDiscount = 5000; // default bjpay is checked
    let voucherDiscount = 0;

    function updateCourier(ongkir, name) {
        currentOngkir = ongkir;
        document.getElementById('ongkirText').innerText = ongkir > 0 ? `Rp${ongkir.toLocaleString('id-ID')}` : 'Gratis (Rp0)';
        document.getElementById('selectedCourierName').innerText = name;
        updatePaymentMethod(selectedPayment);
    }

    function updatePaymentMethod(method) {
        selectedPayment = method;
        if (method === 'bjpay') {
            bjpayShippingDiscount = Math.min(5000, currentOngkir);
            const row = document.getElementById('bjpayDiscountRow');
            row.classList.toggle('hidden', bjpayShippingDiscount === 0);
            document.getElementById('bjpayDiscountText').innerText = `-Rp${bjpayShippingDiscount.toLocaleString('id-ID')}`;
        } else {
            bjpayShippingDiscount = 0;
            document.getElementById('bjpayDiscountRow').classList.add('hidden');
        }
        recalcTotal();
    }

    function recalcTotal() {
        const total = Math.max(0, itemPrice + currentOngkir - bjpayShippingDiscount - voucherDiscount);
        document.getElementById('finalPriceText').innerText = `Rp${total.toLocaleString('id-ID')}`;
    }

    function applyPromoCode() {
        const code = document.getElementById('promoInput').value.trim();
        const msgEl = document.getElementById('promoMessage');
        const discountRow = document.getElementById('discountRow');
        const discountText = document.getElementById('discountText');

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
                total_price: itemPrice
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
                voucherDiscount = data.discount;
            } else {
                msgEl.className = 'text-xs mt-1.5 text-red-600 block';
                msgEl.innerText = data.message;
                discountRow.classList.add('hidden');
                voucherDiscount = 0;
            }
            recalcTotal();
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
