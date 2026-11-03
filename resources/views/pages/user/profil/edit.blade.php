<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Rincian Produk: {{ $postingan->name }} - BJBO Sedagho</title>
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
            <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">Ubah Rincian Produk</h1>
            <p class="text-xs text-gray-500">Perbarui spesifikasi, harga jual, ketersediaan, atau foto produk di etalase Anda.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('user.detail.postingan', $postingan->id) }}" class="bjbo-btn-outline text-xs py-1.5 px-3">
                <i class="bi bi-eye"></i> Pratinjau Produk
            </a>
        </div>
    </div>

    <form action="{{ route('profil.update', $postingan->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Bagian 1: Informasi Dasar Produk -->
        <div class="bg-white border border-bjbo-border rounded-2xl p-5 sm:p-7 shadow-sm">
            <div class="flex items-center gap-2.5 pb-4 border-b border-bjbo-border mb-5">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-bjbo-primary flex items-center justify-center font-bold text-sm">
                    1
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Informasi Dasar Produk</h2>
                    <p class="text-[11px] text-gray-500">Nama produk dan kategori penempatan di etalase pembeli.</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-xs font-bold text-gray-700 mb-1.5">
                        Nama Produk <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" maxlength="100" class="w-full px-3.5 py-2.5 text-sm bg-white border border-bjbo-border-strong rounded-xl focus:border-bjbo-primary focus:ring-2 focus:ring-bjbo-primary/20 focus:outline-none transition" value="{{ old('name', $postingan->name) }}" required>
                    @error('name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="category" class="block text-xs font-bold text-gray-700 mb-1.5">
                            Kategori Etalase <span class="text-red-500">*</span>
                        </label>
                        <select name="category" id="category" class="w-full px-3.5 py-2.5 text-sm bg-white border border-bjbo-border-strong rounded-xl focus:border-bjbo-primary focus:outline-none" required>
                            <option value="Elektronik" {{ old('category', $postingan->category) == 'Elektronik' ? 'selected' : '' }}>Elektronik & Gadget</option>
                            <option value="Fashion" {{ old('category', $postingan->category) == 'Fashion' ? 'selected' : '' }}>Fashion & Aksesoris</option>
                            <option value="Otomotif" {{ old('category', $postingan->category) == 'Otomotif' ? 'selected' : '' }}>Otomotif & Sparepart</option>
                            <option value="Perabot" {{ old('category', $postingan->category) == 'Perabot' ? 'selected' : '' }}>Perabot & Rumah Tangga</option>
                            <option value="Lainnya" {{ old('category', $postingan->category) == 'Lainnya' ? 'selected' : '' }}>Hobi & Lain-lain</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">
                            Kondisi Barang <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center justify-center gap-2 p-2.5 border rounded-xl cursor-pointer text-xs font-semibold transition hover:bg-gray-50 has-checked:bg-blue-50 has-checked:border-bjbo-primary has-checked:text-bjbo-primary border-gray-300">
                                <input type="radio" name="kondisi" value="Bekas (Pre-loved)" {{ old('kondisi', $postingan->kondisi) == 'Bekas (Pre-loved)' ? 'checked' : '' }} class="text-bjbo-primary focus:ring-0">
                                <span>Bekas (Pre-loved)</span>
                            </label>
                            <label class="flex items-center justify-center gap-2 p-2.5 border rounded-xl cursor-pointer text-xs font-semibold transition hover:bg-gray-50 has-checked:bg-blue-50 has-checked:border-bjbo-primary has-checked:text-bjbo-primary border-gray-300">
                                <input type="radio" name="kondisi" value="Baru / Segel" {{ old('kondisi', $postingan->kondisi) == 'Baru / Segel' ? 'checked' : '' }} class="text-bjbo-primary focus:ring-0">
                                <span>Baru / Segel</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bagian 2: Foto Produk -->
        <div class="bg-white border border-bjbo-border rounded-2xl p-5 sm:p-7 shadow-sm">
            <div class="flex items-center gap-2.5 pb-4 border-b border-bjbo-border mb-5">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-bjbo-primary flex items-center justify-center font-bold text-sm">
                    2
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Foto Produk</h2>
                    <p class="text-[11px] text-gray-500">Pratinjau foto saat ini dan unggah foto baru jika ada pembaruan.</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-6 p-4 bg-gray-50/70 border border-bjbo-border rounded-2xl">
                <div class="text-center flex-shrink-0">
                    <span class="text-[10px] font-bold text-gray-500 block mb-1.5 uppercase tracking-wider">Foto Sekarang</span>
                    <img id="currentImgPreview" src="{{ $postingan->image_url }}" alt="{{ $postingan->name }}" class="w-28 h-28 rounded-xl object-cover border border-bjbo-border shadow-sm mx-auto">
                </div>

                <div class="flex-grow min-w-0 border-t sm:border-t-0 sm:border-l border-bjbo-border pt-4 sm:pt-0 sm:pl-6 text-center sm:text-left">
                    <label class="block text-xs font-bold text-gray-800 mb-1">Ganti Foto Baru (Opsional)</label>
                    <p class="text-[11px] text-gray-500 mb-3">Biarkan kosong jika tidak ingin mengganti foto yang sudah ada.</p>
                    <input type="file" name="image" id="editImageInput" accept="image/png,image/jpeg,image/jpg,image/webp" class="text-xs text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-bjbo-primary file:text-white hover:file:bg-bjbo-primary-hover cursor-pointer">
                </div>
            </div>
            @error('image')
                <p class="text-xs text-red-500 mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Bagian 3: Harga & Status Ketersediaan -->
        <div class="bg-white border border-bjbo-border rounded-2xl p-5 sm:p-7 shadow-sm">
            <div class="flex items-center gap-2.5 pb-4 border-b border-bjbo-border mb-5">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-bjbo-primary flex items-center justify-center font-bold text-sm">
                    3
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Harga & Ketersediaan Stok</h2>
                    <p class="text-[11px] text-gray-500">Atur nominal harga jual dan status ketersediaan barang.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="price" class="block text-xs font-bold text-gray-700 mb-1.5">
                        Harga Jual (Rp) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-extrabold text-gray-400">Rp</span>
                        <input type="number" name="price" id="price" min="1000" class="w-full pl-10 pr-3.5 py-2.5 text-sm font-bold bg-white border border-bjbo-border-strong rounded-xl focus:border-bjbo-primary focus:ring-2 focus:ring-bjbo-primary/20 focus:outline-none transition" value="{{ old('price', $postingan->price) }}" required>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">
                        Sistem Penawaran <span class="text-red-500">*</span>
                    </label>
                    <select name="tipe_penawaran" class="w-full px-3.5 py-2.5 text-sm bg-white border border-bjbo-border-strong rounded-xl focus:border-bjbo-primary focus:outline-none">
                        <option value="Bisa Nego" {{ old('tipe_penawaran', $postingan->tipe_penawaran) == 'Bisa Nego' ? 'selected' : '' }}>Bisa Nego Sedagho</option>
                        <option value="Harga Pas" {{ old('tipe_penawaran', $postingan->tipe_penawaran) == 'Harga Pas' ? 'selected' : '' }}>Harga Pas (Nett)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">
                        Ketersediaan Stok
                    </label>
                    <select name="is_sold" class="w-full px-3.5 py-2.5 text-sm bg-white border border-bjbo-border-strong rounded-xl focus:border-bjbo-primary focus:outline-none">
                        <option value="0" {{ old('is_sold', $postingan->is_sold) ? '' : 'selected' }}>Tersedia (Siap Dijual)</option>
                        <option value="1" {{ old('is_sold', $postingan->is_sold) ? 'selected' : '' }}>Sudah Terjual (Habis)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Bagian 4: Lokasi Pengiriman -->
        <div class="bg-white border border-bjbo-border rounded-2xl p-5 sm:p-7 shadow-sm">
            <div class="flex items-center gap-2.5 pb-4 border-b border-bjbo-border mb-5">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-bjbo-primary flex items-center justify-center font-bold text-sm">
                    4
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Lokasi Penjual & Pengiriman</h2>
                    <p class="text-[11px] text-gray-500">Kecamatan domisili produk di wilayah Bengkalis.</p>
                </div>
            </div>

            <div>
                <label for="lokasi" class="block text-xs font-bold text-gray-700 mb-1.5">
                    Kecamatan / Lokasi Barang <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <i class="bi bi-geo-alt absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" name="lokasi" id="lokasi" list="lokasiBengkalisList" class="w-full pl-10 pr-3.5 py-2.5 text-sm bg-white border border-bjbo-border-strong rounded-xl focus:border-bjbo-primary focus:ring-2 focus:ring-bjbo-primary/20 focus:outline-none transition" value="{{ old('lokasi', $postingan->lokasi) }}" required>
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
            </div>
        </div>

        <!-- Bagian 5: Deskripsi Rinci -->
        <div class="bg-white border border-bjbo-border rounded-2xl p-5 sm:p-7 shadow-sm">
            <div class="flex items-center gap-2.5 pb-4 border-b border-bjbo-border mb-5">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-bjbo-primary flex items-center justify-center font-bold text-sm">
                    5
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Deskripsi Rinci Spesifikasi</h2>
                    <p class="text-[11px] text-gray-500">Rincian kondisi fisik, kelengkapan, dan catatan penggunaan.</p>
                </div>
            </div>

            <div>
                <label for="description" class="block text-xs font-bold text-gray-700 mb-1.5">
                    Rincian Barang <span class="text-red-500">*</span>
                </label>
                <textarea name="description" id="description" rows="5" class="w-full px-3.5 py-2.5 text-sm bg-white border border-bjbo-border-strong rounded-xl focus:border-bjbo-primary focus:ring-2 focus:ring-bjbo-primary/20 focus:outline-none transition leading-relaxed" required>{{ old('description', $postingan->description) }}</textarea>
            </div>
        </div>

        <!-- Sticky Footer Action -->
        <div class="sticky bottom-4 z-20 bg-white/95 backdrop-blur-md border border-bjbo-border rounded-2xl p-4 shadow-lg flex items-center justify-between gap-4">
            <a href="{{ route('user.profil') }}" class="bjbo-btn-outline text-xs py-2.5 px-4">
                <i class="bi bi-x-circle"></i> Batal
            </a>
            <button type="submit" class="bjbo-btn-primary text-xs py-2.5 px-6 font-bold shadow-md">
                <i class="bi bi-check-circle-fill"></i> Simpan Perubahan Produk
            </button>
        </div>
    </form>
</main>

@include('components.footer')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fileInput = document.getElementById('editImageInput');
        const previewImg = document.getElementById('currentImgPreview');

        if (fileInput && previewImg) {
            fileInput.addEventListener('change', function () {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        previewImg.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    });
</script>

</body>
</html>
