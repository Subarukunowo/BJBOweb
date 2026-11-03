<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk Baru ke Etalase - Tokopedia Style BJBO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bjbo-bg text-gray-900 font-sans min-h-screen flex flex-col">

@include('sweetalert::alert')

<!-- Header Navigasi -->
@include('components.navbar-user')

<main class="container mx-auto px-3 sm:px-4 my-4 sm:my-6 flex-grow max-w-4xl pb-16 lg:pb-8">
    <!-- Breadcrumb & Header Halaman -->
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div>
            <a href="{{ route('user.profil') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-bjbo-primary transition mb-1">
                <i class="bi bi-arrow-left"></i> Kembali ke Seller Center
            </a>
            <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">Tambah Produk Baru</h1>
            <p class="text-xs text-gray-500">Lengkapi formulir standar e-commerce agar produk Anda diminati dan cepat laku oleh calon pembeli.</p>
        </div>
        <div class="hidden sm:flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 border border-blue-200 text-bjbo-primary text-xs font-semibold rounded-full">
                <i class="bi bi-shield-check"></i> Panduan Etalase Profesional
            </span>
        </div>
    </div>

    <form action="{{ route('user.postingan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <input type="hidden" name="status" value="belum disetujui">

        <!-- Bagian 1: Informasi Dasar Produk -->
        <div class="bg-white border border-bjbo-border rounded-2xl p-5 sm:p-7 shadow-sm">
            <div class="flex items-center gap-2.5 pb-4 border-b border-bjbo-border mb-5">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-bjbo-primary flex items-center justify-center font-bold text-sm">
                    1
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Informasi Dasar Produk</h2>
                    <p class="text-[11px] text-gray-500">Tentukan nama yang menarik, kategori yang tepat, serta kondisi fisik barang.</p>
                </div>
            </div>

            <div class="space-y-4">
                <!-- Nama Produk -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="name" class="block text-xs font-bold text-gray-700">
                            Nama Produk <span class="text-red-500">*</span>
                        </label>
                        <span class="text-[11px] text-gray-400 font-mono" id="nameCounter">0/100 Karakter</span>
                    </div>
                    <input type="text" name="name" id="name" maxlength="100" class="w-full px-3.5 py-2.5 text-sm bg-white border border-bjbo-border-strong rounded-xl focus:border-bjbo-primary focus:ring-2 focus:ring-bjbo-primary/20 focus:outline-none transition" placeholder="Contoh: Laptop Lenovo ThinkPad X270 Core i5 RAM 8GB SSD 256GB" value="{{ old('name') }}" required oninput="document.getElementById('nameCounter').innerText = this.value.length + '/100 Karakter'">
                    <p class="text-[11px] text-gray-400 mt-1">Tips Tokopedia: Gunakan rumus [Jenis Produk] + [Merek] + [Model/Keterangan Singkat] agar mudah dicari.</p>
                    @error('name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kategori & Kondisi -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="category" class="block text-xs font-bold text-gray-700 mb-1.5">
                            Kategori Etalase <span class="text-red-500">*</span>
                        </label>
                        <select name="category" id="category" class="w-full px-3.5 py-2.5 text-sm bg-white border border-bjbo-border-strong rounded-xl focus:border-bjbo-primary focus:outline-none" required>
                            <option value="">Pilih Kategori</option>
                            <option value="Elektronik" {{ old('category') == 'Elektronik' ? 'selected' : '' }}>Elektronik & Gadget</option>
                            <option value="Fashion" {{ old('category') == 'Fashion' ? 'selected' : '' }}>Fashion & Aksesoris</option>
                            <option value="Otomotif" {{ old('category') == 'Otomotif' ? 'selected' : '' }}>Otomotif & Sparepart</option>
                            <option value="Perabot" {{ old('category') == 'Perabot' ? 'selected' : '' }}>Perabot & Rumah Tangga</option>
                            <option value="Lainnya" {{ old('category') == 'Lainnya' ? 'selected' : '' }}>Hobi & Lain-lain</option>
                        </select>
                        @error('category')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">
                            Kondisi Barang <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center justify-center gap-2 p-2.5 border rounded-xl cursor-pointer text-xs font-semibold transition hover:bg-gray-50 has-checked:bg-blue-50 has-checked:border-bjbo-primary has-checked:text-bjbo-primary border-gray-300">
                                <input type="radio" name="kondisi" value="Bekas (Pre-loved)" {{ old('kondisi', 'Bekas (Pre-loved)') == 'Bekas (Pre-loved)' ? 'checked' : '' }} class="text-bjbo-primary focus:ring-0">
                                <span>Bekas (Pre-loved)</span>
                            </label>
                            <label class="flex items-center justify-center gap-2 p-2.5 border rounded-xl cursor-pointer text-xs font-semibold transition hover:bg-gray-50 has-checked:bg-blue-50 has-checked:border-bjbo-primary has-checked:text-bjbo-primary border-gray-300">
                                <input type="radio" name="kondisi" value="Baru / Segel" {{ old('kondisi') == 'Baru / Segel' ? 'checked' : '' }} class="text-bjbo-primary focus:ring-0">
                                <span>Baru / Segel</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bagian 2: Media & Foto Produk -->
        <div class="bg-white border border-bjbo-border rounded-2xl p-5 sm:p-7 shadow-sm">
            <div class="flex items-center gap-2.5 pb-4 border-b border-bjbo-border mb-5">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-bjbo-primary flex items-center justify-center font-bold text-sm">
                    2
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Foto Utama Produk</h2>
                    <p class="text-[11px] text-gray-500">Foto yang jelas dan terang meningkatkan kepercayaan calon pembeli hingga 3x lipat.</p>
                </div>
            </div>

            <div class="border-2 border-dashed border-bjbo-border-strong rounded-2xl p-6 text-center bg-gray-50/50 hover:bg-gray-50 transition">
                <div id="dropZoneContent">
                    <i class="bi bi-cloud-arrow-up text-4xl text-bjbo-primary mb-2 block"></i>
                    <p class="text-xs font-bold text-gray-800 mb-1">Pilih Berkas Foto Produk</p>
                    <p class="text-[11px] text-gray-500 mb-4">Format didukung: JPG, PNG, WEBP. Maksimal ukuran 2MB.</p>
                    <input type="file" name="image" id="productImageInput" accept="image/png,image/jpeg,image/jpg,image/webp" class="text-xs text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-bjbo-primary file:text-white hover:file:bg-bjbo-primary-hover cursor-pointer" required>
                </div>

                <div id="imagePreviewContainer" class="mt-4 hidden">
                    <div class="relative inline-block">
                        <img id="imagePreview" src="" alt="Pratinjau Foto" class="h-48 max-w-full rounded-xl border border-bjbo-border object-cover shadow-sm mx-auto">
                        <span class="absolute top-2 left-2 bg-black/70 text-white text-[10px] font-bold px-2 py-0.5 rounded">
                            Foto Utama
                        </span>
                    </div>
                </div>
            </div>
            @error('image')
                <p class="text-xs text-red-500 mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Bagian 3: Harga & Penawaran -->
        <div class="bg-white border border-bjbo-border rounded-2xl p-5 sm:p-7 shadow-sm">
            <div class="flex items-center gap-2.5 pb-4 border-b border-bjbo-border mb-5">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-bjbo-primary flex items-center justify-center font-bold text-sm">
                    3
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Harga & Ketentuan Penawaran</h2>
                    <p class="text-[11px] text-gray-500">Tentukan harga jual yang wajar dan fleksibilitas negosiasi untuk warga sekitar.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="price" class="block text-xs font-bold text-gray-700 mb-1.5">
                        Harga Produk (Rupiah) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-extrabold text-gray-400">Rp</span>
                        <input type="number" name="price" id="price" min="1000" class="w-full pl-10 pr-3.5 py-2.5 text-sm font-bold bg-white border border-bjbo-border-strong rounded-xl focus:border-bjbo-primary focus:ring-2 focus:ring-bjbo-primary/20 focus:outline-none transition" placeholder="Contoh: 350000" value="{{ old('price') }}" required>
                    </div>
                    @error('price')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">
                        Sistem Penawaran <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center justify-center gap-2 p-2.5 border rounded-xl cursor-pointer text-xs font-semibold transition hover:bg-gray-50 has-checked:bg-blue-50 has-checked:border-bjbo-primary has-checked:text-bjbo-primary border-gray-300">
                            <input type="radio" name="tipe_penawaran" value="Bisa Nego" {{ old('tipe_penawaran', 'Bisa Nego') == 'Bisa Nego' ? 'checked' : '' }} class="text-bjbo-primary focus:ring-0">
                            <span>Bisa Nego</span>
                        </label>
                        <label class="flex items-center justify-center gap-2 p-2.5 border rounded-xl cursor-pointer text-xs font-semibold transition hover:bg-gray-50 has-checked:bg-blue-50 has-checked:border-bjbo-primary has-checked:text-bjbo-primary border-gray-300">
                            <input type="radio" name="tipe_penawaran" value="Harga Pas" {{ old('tipe_penawaran') == 'Harga Pas' ? 'checked' : '' }} class="text-bjbo-primary focus:ring-0">
                            <span>Harga Pas</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bagian 4: Lokasi & Metode Transaksi -->
        <div class="bg-white border border-bjbo-border rounded-2xl p-5 sm:p-7 shadow-sm">
            <div class="flex items-center gap-2.5 pb-4 border-b border-bjbo-border mb-5">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-bjbo-primary flex items-center justify-center font-bold text-sm">
                    4
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Lokasi & Pengiriman</h2>
                    <p class="text-[11px] text-gray-500">Tentukan domisili barang untuk kemudahan transaksi ketemuan (COD) atau kurir lokal.</p>
                </div>
            </div>

            <div>
                <label for="lokasi" class="block text-xs font-bold text-gray-700 mb-1.5">
                    Kecamatan / Lokasi Barang <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <i class="bi bi-geo-alt absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" name="lokasi" id="lokasi" list="lokasiBengkalisList" class="w-full pl-10 pr-3.5 py-2.5 text-sm bg-white border border-bjbo-border-strong rounded-xl focus:border-bjbo-primary focus:ring-2 focus:ring-bjbo-primary/20 focus:outline-none transition" placeholder="Contoh: Bengkalis Kota (atau pilih rekomendasi)" value="{{ old('lokasi') }}" required>
                </div>
                <datalist id="lokasiBengkalisList">
                    <option value="Bengkalis Kota">
                    <option value="Mandau - Duri">
                    <option value="Pinggir">
                    <option value="Bukit Batu">
                    <option value="Bantan">
                    <option value="Rupat">
                    <option value="Rupat Utara">
                    <option value="Siak Kecil">
                    <option value="Bandar Laksamana">
                    <option value="Bathin Solapan">
                    <option value="Talang Muandau">
                </datalist>
                <p class="text-[11px] text-gray-400 mt-1">Mencakup wilayah transaksi sedagho di Kabupaten Bengkalis dan sekitarnya.</p>
                @error('lokasi')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Bagian 5: Deskripsi Rinci Spesifikasi Produk -->
        <div class="bg-white border border-bjbo-border rounded-2xl p-5 sm:p-7 shadow-sm">
            <div class="flex items-center gap-2.5 pb-4 border-b border-bjbo-border mb-5">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-bjbo-primary flex items-center justify-center font-bold text-sm">
                    5
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Deskripsi Lengkap & Spesifikasi</h2>
                    <p class="text-[11px] text-gray-500">Tuliskan rincian kelengkapan, riwayat pemakaian, serta catatan penting lainnya.</p>
                </div>
            </div>

            <div>
                <label for="description" class="block text-xs font-bold text-gray-700 mb-1.5">
                    Rincian Barang <span class="text-red-500">*</span>
                </label>
                <textarea name="description" id="description" rows="5" class="w-full px-3.5 py-2.5 text-sm bg-white border border-bjbo-border-strong rounded-xl focus:border-bjbo-primary focus:ring-2 focus:ring-bjbo-primary/20 focus:outline-none transition leading-relaxed" placeholder="Tuliskan spesifikasi detail seperti:&#10;- Kelengkapan: Unit, box, charger original&#10;- Kondisi fisik: Mulus 95%, fungsi normal&#10;- Alasan jual: Jarang dipakai lagi" required>{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Tombol Aksi Bawah Sticky -->
        <div class="sticky bottom-4 z-20 bg-white/95 backdrop-blur-md border border-bjbo-border rounded-2xl p-4 shadow-lg flex items-center justify-between gap-4">
            <a href="{{ route('user.profil') }}" class="bjbo-btn-outline text-xs py-2.5 px-4">
                <i class="bi bi-x-circle"></i> Batal
            </a>
            <button type="submit" class="bjbo-btn-accent text-xs py-2.5 px-6 font-bold shadow-md">
                <i class="bi bi-cloud-arrow-up-fill"></i> Simpan & Tayangkan ke Etalase
            </button>
        </div>
    </form>
</main>

@include('components.footer')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fileInput = document.getElementById('productImageInput');
        const previewContainer = document.getElementById('imagePreviewContainer');
        const previewImg = document.getElementById('imagePreview');

        if (fileInput && previewImg) {
            fileInput.addEventListener('change', function () {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        previewImg.src = e.target.result;
                        previewContainer.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    });
</script>

</body>
</html>
