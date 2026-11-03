<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buka Toko di BJBO - Toko Perorangan & Official Store</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" crossorigin="anonymous">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bjbo-bg text-gray-900 font-sans min-h-screen flex flex-col">

@include('sweetalert::alert')
@include('components.navbar-user')

<main class="container mx-auto px-3 sm:px-4 my-6 flex-grow max-w-4xl">
    <!-- Header Buka Toko -->
    <div class="text-center max-w-2xl mx-auto mb-8">
        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-800 bg-amber-100 border border-amber-300 px-3 py-1 rounded-full mb-3">
            <i class="bi bi-shop-window"></i> BJBO Seller Onboarding
        </span>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900">Mulai Berjualan di Pasar Digital Bengkalis</h1>
        <p class="text-xs sm:text-sm text-gray-600 mt-2">Pilih skema pendaftaran toko yang sesuai dengan profil bisnis Anda: Toko Perorangan untuk UMKM lokal atau Official Store untuk brand terdaftar.</p>
    </div>

    @if($store)
        <!-- Kartu Status Toko Jika Sudah Mendaftar -->
        <div class="bg-white border border-bjbo-border rounded-2xl p-6 sm:p-8 shadow-sm text-center max-w-2xl mx-auto mb-10">
            <div class="w-16 h-16 rounded-full mx-auto flex items-center justify-center text-3xl mb-4
                {{ $store->status === 'aktif' ? 'bg-green-100 text-green-700' : ($store->status === 'ditolak' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                @if($store->status === 'aktif')
                    <i class="bi bi-patch-check-fill"></i>
                @elseif($store->status === 'ditolak')
                    <i class="bi bi-x-circle-fill"></i>
                @else
                    <i class="bi bi-hourglass-split"></i>
                @endif
            </div>

            <span class="inline-block text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full mb-2
                {{ $store->isOfficial() ? 'bg-purple-100 text-purple-800 border border-purple-200' : 'bg-blue-100 text-blue-800 border border-blue-200' }}">
                {{ $store->isOfficial() ? 'Official Store' : 'Toko Perorangan' }}
            </span>

            <h2 class="text-xl font-bold text-gray-900 mb-1">{{ $store->store_name }}</h2>
            <p class="text-xs text-gray-500 mb-4">No. HP: {{ $store->phone_number }} &bull; Email: {{ $store->email }}</p>

            <div class="p-4 rounded-xl mb-6 text-xs
                {{ $store->status === 'aktif' ? 'bg-green-50 border border-green-200 text-green-800' : ($store->status === 'ditolak' ? 'bg-red-50 border border-red-200 text-red-800' : 'bg-amber-50 border border-amber-200 text-amber-800') }}">
                @if($store->status === 'aktif')
                    <strong>Toko Anda Aktif!</strong> Selamat, toko Anda telah disetujui dan berhak menggunakan lencana seller resmi BJBO serta dompet escrow Rekber.
                @elseif($store->status === 'ditolak')
                    <strong>Pendaftaran Ditolak:</strong> {{ $store->rejected_reason ?? 'Dokumen tidak memenuhi persyaratan verifikasi.' }}
                @else
                    <strong>Sedang Dalam Proses Kurasi:</strong> Berkas identitas Anda sedang ditinjau tim verifikator BJBO dalam 1x24 jam kerja.
                @endif
            </div>

            <div class="flex justify-center gap-3">
                <a href="{{ route('user.profil.index') }}" class="bjbo-btn-primary text-xs py-2.5 px-6">
                    <i class="bi bi-person-circle"></i> Kembali ke Profil Saya
                </a>
                @if($store->status === 'aktif')
                    <a href="{{ route('user.postingan.create') }}" class="bjbo-btn-accent text-xs py-2.5 px-6 font-bold">
                        <i class="bi bi-plus-circle"></i> Tambah Produk
                    </a>
                @endif
            </div>
        </div>
    @else
        <!-- Tab Selector: Perorangan vs Official Store -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <button type="button" onclick="switchStoreTab('perorangan')" id="tabBtnPerorangan" class="p-4 rounded-2xl border-2 text-left transition border-bjbo-primary bg-blue-50/50 shadow-sm">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-bjbo-primary flex items-center justify-center text-lg">
                        <i class="bi bi-person-badge"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">1. Toko Perorangan</h3>
                        <span class="text-[11px] text-gray-500">Khusus UMKM & Penjual Mandiri</span>
                    </div>
                </div>
                <p class="text-xs text-gray-600">Syarat: Nomor HP aktif, Email terverifikasi, dan Foto KTP asli pemilik akun.</p>
            </button>

            <button type="button" onclick="switchStoreTab('official')" id="tabBtnOfficial" class="p-4 rounded-2xl border-2 text-left transition border-gray-200 bg-white hover:border-purple-300">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-lg">
                        <i class="bi bi-patch-check-fill"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">2. Official Store</h3>
                        <span class="text-[11px] text-purple-700 font-semibold">Brand Resmi & Perusahaan</span>
                    </div>
                </div>
                <p class="text-xs text-gray-600">Syarat: Nomor NIB, NPWP Perusahaan, dan Sertifikat Hak Kekayaan Intelektual (HAKI).</p>
            </button>
        </div>

        <!-- FORM 1: Toko Perorangan -->
        <div id="formPeroranganSection" class="bg-white border border-bjbo-border rounded-2xl p-6 sm:p-8 shadow-sm mb-10">
            <div class="flex items-center gap-2 mb-4 pb-3 border-b border-bjbo-border">
                <i class="bi bi-person-vcard text-bjbo-primary text-xl"></i>
                <h2 class="text-base font-bold text-gray-900">Formulir Pendaftaran Toko Perorangan</h2>
            </div>

            <form action="{{ route('user.toko.storePerorangan') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Toko / Usaha <span class="text-red-500">*</span></label>
                        <input type="text" name="store_name" required value="{{ old('store_name') }}" placeholder="Contoh: Toko Berkah Bengkalis" class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:border-bjbo-primary focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor WhatsApp / HP Aktif <span class="text-red-500">*</span></label>
                        <input type="text" name="phone_number" required value="{{ old('phone_number') }}" placeholder="081234567890" class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:border-bjbo-primary focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Email Korespondensi <span class="text-red-500">*</span></label>
                        <input type="email" name="email" required value="{{ old('email', $user->email) }}" class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:border-bjbo-primary focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor NIK KTP (16 Digit) <span class="text-red-500">*</span></label>
                        <input type="text" name="ktp_number" required maxlength="16" minlength="16" value="{{ old('ktp_number') }}" placeholder="140301xxxxxxxxxx" class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:border-bjbo-primary focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Foto KTP Asli (Maks 3MB) <span class="text-red-500">*</span></label>
                    <input type="file" name="ktp_image" required accept="image/*" class="w-full text-xs p-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none">
                    <p class="text-[11px] text-gray-400 mt-1">Pastikan foto KTP terbaca jelas dan tidak buram untuk validasi KYC.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Alamat Lengkap Toko / Lokasi Gudang <span class="text-red-500">*</span></label>
                    <textarea name="address" required rows="2" placeholder="Jl. Antara No. 12, Bengkalis Kota" class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:border-bjbo-primary focus:outline-none">{{ old('address') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Deskripsi Singkat Toko (Opsional)</label>
                    <textarea name="description" rows="2" placeholder="Jual aneka produk olahan ikan lempuk durian khas Bengkalis..." class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:border-bjbo-primary focus:outline-none">{{ old('description') }}</textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full sm:w-auto bjbo-btn-primary text-xs py-3 px-8 font-bold shadow">
                        <i class="bi bi-send-check"></i> Ajukan Pendaftaran Toko Perorangan
                    </button>
                </div>
            </form>
        </div>

        <!-- FORM 2: Official Store -->
        <div id="formOfficialSection" class="hidden bg-white border border-bjbo-border rounded-2xl p-6 sm:p-8 shadow-sm mb-10">
            <div class="flex items-center gap-2 mb-4 pb-3 border-b border-bjbo-border">
                <i class="bi bi-patch-check-fill text-purple-600 text-xl"></i>
                <h2 class="text-base font-bold text-gray-900">Formulir Pendaftaran Official Store Brand Terdaftar</h2>
            </div>

            <form action="{{ route('user.toko.storeOfficial') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Brand / Official Store <span class="text-red-500">*</span></label>
                        <input type="text" name="store_name" required value="{{ old('store_name') }}" placeholder="Contoh: PT Bengkalis Maritime Official" class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:border-purple-600 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor Telepon Kantor / Hotline <span class="text-red-500">*</span></label>
                        <input type="text" name="phone_number" required value="{{ old('phone_number') }}" placeholder="0766-xxxxx atau 08xxxx" class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:border-purple-600 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Email Resmi Perusahaan <span class="text-red-500">*</span></label>
                        <input type="email" name="email" required value="{{ old('email', $user->email) }}" class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:border-purple-600 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor Induk Berusaha (NIB) <span class="text-red-500">*</span></label>
                        <input type="text" name="nib_number" required value="{{ old('nib_number') }}" placeholder="Contoh: 9120001234567" class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:border-purple-600 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor NPWP Perusahaan <span class="text-red-500">*</span></label>
                        <input type="text" name="npwp_company" required value="{{ old('npwp_company') }}" placeholder="01.234.567.8-901.000" class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:border-purple-600 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Unggah Dokumen NPWP (PDF / Gambar) <span class="text-red-500">*</span></label>
                        <input type="file" name="npwp_image" required accept="image/*,application/pdf" class="w-full text-xs p-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor Sertifikat HAKI Merek <span class="text-red-500">*</span></label>
                        <input type="text" name="haki_certificate_number" required value="{{ old('haki_certificate_number') }}" placeholder="Contoh: IDM000123456" class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:border-purple-600 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Unggah Sertifikat HAKI (PDF / Gambar) <span class="text-red-500">*</span></label>
                        <input type="file" name="haki_certificate_file" required accept="image/*,application/pdf" class="w-full text-xs p-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Alamat Kantor Pusat / Pabrik <span class="text-red-500">*</span></label>
                    <textarea name="address" required rows="2" placeholder="Komplek Pergudangan Pelabuhan Roro, Bengkalis" class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:border-purple-600 focus:outline-none">{{ old('address') }}</textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full sm:w-auto bg-purple-700 hover:bg-purple-800 text-white text-xs py-3 px-8 rounded-xl font-bold shadow transition">
                        <i class="bi bi-shield-check"></i> Ajukan Pendaftaran Official Store
                    </button>
                </div>
            </form>
        </div>
    @endif
</main>

<script>
    function switchStoreTab(tab) {
        const formPerorangan = document.getElementById('formPeroranganSection');
        const formOfficial = document.getElementById('formOfficialSection');
        const tabBtnPerorangan = document.getElementById('tabBtnPerorangan');
        const tabBtnOfficial = document.getElementById('tabBtnOfficial');

        if (tab === 'perorangan') {
            formPerorangan.classList.remove('hidden');
            formOfficial.classList.add('hidden');
            tabBtnPerorangan.classList.add('border-bjbo-primary', 'bg-blue-50/50');
            tabBtnPerorangan.classList.remove('border-gray-200', 'bg-white');
            tabBtnOfficial.classList.remove('border-purple-600', 'bg-purple-50/50');
            tabBtnOfficial.classList.add('border-gray-200', 'bg-white');
        } else {
            formPerorangan.classList.add('hidden');
            formOfficial.classList.remove('hidden');
            tabBtnOfficial.classList.add('border-purple-600', 'bg-purple-50/50');
            tabBtnOfficial.classList.remove('border-gray-200', 'bg-white');
            tabBtnPerorangan.classList.remove('border-bjbo-primary', 'bg-blue-50/50');
            tabBtnPerorangan.classList.add('border-gray-200', 'bg-white');
        }
    }
</script>

@include('components.footer')
</body>
</html>
