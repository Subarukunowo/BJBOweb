<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lacak Pengiriman Pesanan #{{ $pesanan->order_number }} - BJBO Sedagho</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" crossorigin="anonymous">
    <!-- Leaflet.js Free OpenStreetMap -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        #deliveryMap {
            height: 380px;
            width: 100%;
            border-radius: 1rem;
            z-index: 10;
        }
    </style>
</head>
<body class="bg-bjbo-bg text-gray-900 font-sans min-h-screen flex flex-col">

@include('sweetalert::alert')
@include('components.navbar-user')

<main class="container mx-auto px-3 sm:px-4 my-6 flex-grow max-w-5xl">
    <!-- Breadcrumb & Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-6 pb-4 border-b border-bjbo-border">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                <a href="{{ route('user.pesanan.index') }}" class="hover:text-bjbo-primary">Pesanan Saya</a>
                <span>&rsaquo;</span>
                <span class="text-gray-800 font-semibold">Pelacakan Real-time</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Lacak Pengiriman #{{ $pesanan->order_number }}</h1>
        </div>

        <div class="flex items-center gap-2">
            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                {{ $pesanan->shipping_status === 'sampai_tujuan' ? 'bg-green-100 text-green-800 border border-green-200' : ($pesanan->shipping_status === 'dikirim' ? 'bg-blue-100 text-blue-800 border border-blue-200' : ($pesanan->shipping_status === 'diproses' ? 'bg-indigo-100 text-indigo-800 border border-indigo-200' : ($pesanan->shipping_status === 'ditolak_penjual' ? 'bg-red-100 text-red-800 border border-red-200' : 'bg-amber-100 text-amber-800 border border-amber-200'))) }}">
                {{ str_replace('_', ' ', strtoupper($pesanan->shipping_status)) }}
            </span>
            <a href="{{ route('user.pesanan.detail', $pesanan->id) }}" class="bjbo-btn-outline text-xs py-1.5 px-3">
                Detail Transaksi
            </a>
        </div>
    </div>

    <!-- 4 Tahapan Siklus Pesanan (Tokopedia / Shopee Tracking Stepper) -->
    <div class="bg-white border border-bjbo-border rounded-2xl p-5 sm:p-6 mb-6 shadow-sm">
        <h2 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-6">Status Perjalanan Paket</h2>

        @php
            $stages = [
                'menunggu_konfirmasi' => 1,
                'diproses' => 2,
                'dikirim' => 3,
                'sampai_tujuan' => 4,
            ];
            $currentStage = $stages[$pesanan->shipping_status] ?? ( $pesanan->shipping_status === 'ditolak_penjual' ? 0 : 1 );
        @endphp

        <div class="relative flex items-center justify-between max-w-3xl mx-auto px-4">
            <!-- Progress Line -->
            <div class="absolute left-10 right-10 top-1/2 -translate-y-1/2 h-1 bg-gray-200 -z-0">
                <div class="h-full bg-bjbo-primary transition-all duration-500" style="width: {{ $currentStage > 1 ? (($currentStage - 1) / 3) * 100 : 0 }}%"></div>
            </div>

            <!-- Step 1: Menunggu Konfirmasi -->
            <div class="relative z-10 flex flex-col items-center text-center">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs mb-1.5
                    {{ $currentStage >= 1 ? 'bg-bjbo-primary text-white ring-4 ring-blue-100' : 'bg-gray-200 text-gray-500' }}">
                    <i class="bi bi-clock-history"></i>
                </div>
                <span class="text-[11px] font-bold text-gray-800">1. Konfirmasi</span>
                <span class="text-[10px] text-gray-400">Penjual</span>
            </div>

            <!-- Step 2: Diproses -->
            <div class="relative z-10 flex flex-col items-center text-center">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs mb-1.5
                    {{ $currentStage >= 2 ? 'bg-bjbo-primary text-white ring-4 ring-blue-100' : 'bg-gray-200 text-gray-500' }}">
                    <i class="bi bi-box-seam"></i>
                </div>
                <span class="text-[11px] font-bold text-gray-800">2. Diproses</span>
                <span class="text-[10px] text-gray-400">Sedang Dikemas</span>
            </div>

            <!-- Step 3: Sedang Dikirim -->
            <div class="relative z-10 flex flex-col items-center text-center">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs mb-1.5
                    {{ $currentStage >= 3 ? 'bg-bjbo-primary text-white ring-4 ring-blue-100' : 'bg-gray-200 text-gray-500' }}">
                    <i class="bi bi-truck"></i>
                </div>
                <span class="text-[11px] font-bold text-gray-800">3. Dikirim</span>
                <span class="text-[10px] text-gray-400">Kurir Pengantar</span>
            </div>

            <!-- Step 4: Sampai Tujuan -->
            <div class="relative z-10 flex flex-col items-center text-center">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs mb-1.5
                    {{ $currentStage >= 4 ? 'bg-green-600 text-white ring-4 ring-green-100' : 'bg-gray-200 text-gray-500' }}">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <span class="text-[11px] font-bold text-gray-800">4. Sampai</span>
                <span class="text-[10px] text-gray-400">Escrow Cair</span>
            </div>
        </div>

        @if($pesanan->shipping_status === 'ditolak_penjual')
            <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-xl text-center text-xs text-red-800">
                <strong>Pesanan Ditolak Penjual:</strong> {{ $pesanan->alasan_penolakan ?? 'Stok habis.' }}. Dana pembayaran tersimpan aman dalam Rekber dan dikembalikan ke saldo pembeli.
            </div>
        @endif
    </div>

    <!-- PETA LEAFLET & OPENSTREETMAP (Free, Gratis & Akurat) -->
    <div class="bg-white border border-bjbo-border rounded-2xl p-5 sm:p-6 mb-6 shadow-sm">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4">
            <div>
                <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                    <i class="bi bi-geo-alt-fill text-red-500"></i> Peta Posisi Kurir Pengiriman (OpenStreetMap)
                </h3>
                <p class="text-xs text-gray-500">Peta interaktif navigasi gratis tanpa biaya API key pihak ketiga</p>
            </div>
            <div class="text-xs text-gray-600">
                <span class="font-bold text-gray-800">{{ $pesanan->kurir_name ?? 'Kurir Lokal' }}</span> &bull; Resi: <span class="font-mono font-bold text-bjbo-primary">{{ $pesanan->nomor_resi ?? '-' }}</span>
            </div>
        </div>

        <!-- Wadah Leaflet Map -->
        <div id="deliveryMap" class="shadow-inner border border-bjbo-border"></div>

        <div class="mt-4 p-3 bg-gray-50 rounded-xl flex items-center justify-between text-xs text-gray-600">
            <div class="flex items-center gap-2">
                <i class="bi bi-pin-map text-bjbo-primary text-base"></i>
                <span><strong>Lokasi Saat Ini:</strong> {{ $pesanan->last_checkpoint ?? 'Paket sedang disiapkan penjual' }}</span>
            </div>
            <span class="text-[11px] text-gray-400">&copy; OpenStreetMap Contributors</span>
        </div>
    </div>

    <!-- Aksi Penjual & Pembeli (Konfirmasi, Kirim, Selesai) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
        <!-- Rincian Produk & Pengiriman -->
        <div class="bg-white border border-bjbo-border rounded-2xl p-5 sm:p-6 shadow-sm">
            <h3 class="text-sm font-bold text-gray-900 mb-3 pb-2 border-b border-bjbo-border">Detail Produk & Mitra</h3>
            <div class="flex items-center gap-3 mb-4">
                <img src="{{ $pesanan->postingan->image_url ?? asset('images/bjbo.png') }}" class="w-16 h-16 rounded-xl object-cover border border-bjbo-border flex-shrink-0">
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-gray-900 line-clamp-1">{{ $pesanan->postingan->name ?? 'Produk' }}</h4>
                    <p class="text-xs text-bjbo-primary font-bold">Rp{{ number_format($pesanan->getEffectivePrice(), 0, ',', '.') }}</p>
                    <p class="text-[11px] text-gray-500">Kategori: {{ $pesanan->postingan->category ?? '-' }}</p>
                </div>
            </div>

            <div class="space-y-2 text-xs text-gray-600 pt-2 border-t border-bjbo-border">
                <div class="flex justify-between">
                    <span>Penjual:</span>
                    <span class="font-bold text-gray-800">{{ $pesanan->penjual->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Pembeli:</span>
                    <span class="font-bold text-gray-800">{{ $pesanan->pembeli->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Alamat Tujuan:</span>
                    <span class="font-semibold text-gray-800 text-right max-w-[200px]">{{ $pesanan->destination_address ?? ($pesanan->catatan ?? 'Bengkalis Kota') }}</span>
                </div>
            </div>
        </div>

        <!-- Tombol Aksi Sesuai Peran -->
        <div class="bg-white border border-bjbo-border rounded-2xl p-5 sm:p-6 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-bold text-gray-900 mb-3 pb-2 border-b border-bjbo-border">Kontrol Operasional Pesanan</h3>

                @if(Auth::id() === $pesanan->penjual_id)
                    <!-- AREA PENJUAL -->
                    @if($pesanan->shipping_status === 'menunggu_konfirmasi')
                        <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800 mb-4">
                            <strong>Konfirmasi Pesanan Baru:</strong> Pembeli telah menyelesaikan pembayaran via QRIS/Midtrans. Harap konfirmasi ketersediaan stok produk Anda.
                        </div>

                        <div class="flex gap-2">
                            <form action="{{ route('user.pesanan.confirm', $pesanan->id) }}" method="POST" class="flex-1">
                                @csrf
                                <input type="hidden" name="action" value="accept">
                                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs py-2.5 px-4 rounded-xl font-bold shadow transition">
                                    <i class="bi bi-check-circle"></i> Terima & Proses Pesanan
                                </button>
                            </form>

                            <button type="button" onclick="document.getElementById('rejectForm').classList.toggle('hidden')" class="bjbo-btn-outline text-xs py-2.5 px-4 text-red-600 border-red-300 hover:bg-red-50">
                                Tolak
                            </button>
                        </div>

                        <form id="rejectForm" action="{{ route('user.pesanan.confirm', $pesanan->id) }}" method="POST" class="hidden mt-3 pt-3 border-t border-bjbo-border space-y-2">
                            @csrf
                            <input type="hidden" name="action" value="reject">
                            <label class="block text-xs font-semibold text-gray-700">Alasan Penolakan Pesanan:</label>
                            <input type="text" name="alasan_penolakan" required placeholder="Contoh: Stok barang sudah habis" class="w-full text-xs p-2 bg-gray-50 border border-gray-300 rounded-lg">
                            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white text-xs py-2 rounded-lg font-bold">Kirim Penolakan</button>
                        </form>

                    @elseif($pesanan->shipping_status === 'diproses')
                        <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-xs text-blue-800 mb-4">
                            Pesanan sedang dikemas. Silakan masukkan nama kurir dan nomor resi pengiriman untuk pembeli.
                        </div>

                        <form action="{{ route('user.pesanan.ship', $pesanan->id) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Pilih / Nama Ekspedisi / Kurir <span class="text-red-500">*</span></label>
                                <input type="text" name="kurir_name" required placeholder="J&T Express, JNE, SiCepat, Kurir Lokal Bengkalis" class="w-full text-xs p-2 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:border-bjbo-primary focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor Resi / Bukti Pengiriman <span class="text-red-500">*</span></label>
                                <input type="text" name="nomor_resi" required placeholder="Contoh: JT892193812" class="w-full text-xs p-2 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:border-bjbo-primary focus:outline-none font-mono">
                            </div>
                            <button type="submit" class="w-full bjbo-btn-primary text-xs py-2.5 rounded-xl font-bold shadow">
                                <i class="bi bi-send-check"></i> Konfirmasi Barang Sedang Dikirim
                            </button>
                        </form>

                    @else
                        <div class="p-4 bg-gray-50 rounded-xl text-center text-xs text-gray-600">
                            Pengiriman sedang berlangsung dengan kurir <strong>{{ $pesanan->kurir_name }}</strong>.
                        </div>
                    @endif

                @else
                    <!-- AREA PEMBELI -->
                    @if($pesanan->shipping_status === 'dikirim')
                        <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-xs text-emerald-800 mb-4">
                            Paket Anda sedang dalam perjalanan bersama kurir. Jika paket sudah tiba di tangan Anda dengan selamat, klik tombol di bawah untuk menyelesaikan pesanan.
                        </div>

                        <form action="{{ route('user.pesanan.deliver', $pesanan->id) }}" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('Apakah paket pesanan telah Anda terima dengan baik? Dana akan diteruskan ke penjual.')" class="w-full bg-green-600 hover:bg-green-700 text-white text-xs py-3 px-4 rounded-xl font-bold shadow transition">
                                <i class="bi bi-check2-all"></i> Konfirmasi Pesanan Sampai & Selesai
                            </button>
                        </form>
                    @elseif($pesanan->shipping_status === 'sampai_tujuan')
                        <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-center text-xs text-green-800">
                            <i class="bi bi-patch-check-fill text-2xl block mb-1"></i>
                            <strong>Pesanan Selesai!</strong> Terima kasih telah berbelanja menggunakan sistem Rekber aman BJBO Sedagho.
                        </div>
                    @else
                        <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl text-center text-xs text-blue-800">
                            Penjual sedang memproses paket pesanan Anda. Halaman ini akan diperbarui ketika kurir mulai mengantarkan paket.
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const originLat = {{ $trackingData['origin']['lat'] }};
        const originLng = {{ $trackingData['origin']['lng'] }};
        const destLat = {{ $trackingData['destination']['lat'] }};
        const destLng = {{ $trackingData['destination']['lng'] }};
        const curLat = {{ $trackingData['current']['lat'] }};
        const curLng = {{ $trackingData['current']['lng'] }};

        // Initialize Leaflet Map centered around Bengkalis coordinates
        const map = L.map('deliveryMap').setView([curLat, curLng], 13);

        // Add OpenStreetMap Free Tile Layer
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        // Marker Gudang Penjual (Origin)
        const originMarker = L.marker([originLat, originLng]).addTo(map)
            .bindPopup('<b>Gudang Penjual</b><br>{{ $trackingData['origin']['address'] }}');

        // Marker Alamat Tujuan (Destination)
        const destMarker = L.marker([destLat, destLng]).addTo(map)
            .bindPopup('<b>Alamat Penerima</b><br>{{ $trackingData['destination']['address'] }}');

        // Marker Posisi Kurir Saat Ini
        const courierMarker = L.marker([curLat, curLng]).addTo(map)
            .bindPopup('<b>Kurir {{ $pesanan->kurir_name ?? 'Lokal' }}</b><br>{{ $pesanan->last_checkpoint ?? 'Dalam Perjalanan' }}')
            .openPopup();

        // Draw Polyline Route Path
        const pathLine = L.polyline([
            [originLat, originLng],
            [curLat, curLng],
            [destLat, destLng]
        ], {
            color: '#1E3A8A',
            weight: 4,
            opacity: 0.7,
            dashArray: '8, 8'
        }).addTo(map);

        map.fitBounds(pathLine.getBounds(), { padding: [40, 40] });
    });
</script>

@include('components.footer')
</body>
</html>
