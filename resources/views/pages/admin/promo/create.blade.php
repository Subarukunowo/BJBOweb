@extends('layouts.admin.main')

@section('title', 'Tambah Promo / Iklan Baru - BJBO Sedagho')

@section('content')
<div class="space-y-6 max-w-3xl">
    <!-- Header Page -->
    <div class="flex items-center justify-between pb-4 border-b border-bjbo-border">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Tambah Iklan / Promo Diskon</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Buat kampanye promosi atau kode voucher belanja baru.</p>
        </div>
        <a href="{{ route('admin.promo.index') }}" class="bjbo-btn-outline px-3.5 py-2 text-xs font-semibold rounded-xl">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Form Create Promo -->
    <div class="bg-white border border-bjbo-border rounded-2xl p-6 shadow-2xs">
        <form action="{{ route('admin.promo.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Judul Promo / Iklan <span class="text-red-500">*</span></label>
                <input type="text" name="title" required value="{{ old('title') }}" placeholder="Contoh: Diskon Kemerdekaan Bengkalis 10%" class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:border-bjbo-primary focus:outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tipe Promo <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:border-bjbo-primary focus:outline-none">
                        <option value="diskon_persen">Diskon Persentase (%)</option>
                        <option value="diskon_nominal">Diskon Nominal Tetap (Rp)</option>
                        <option value="banner_iklan">Banner Iklan Utama</option>
                        <option value="flash_sale">Flash Sale</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kode Kupon / Voucher (Opsional)</label>
                    <input type="text" name="code" value="{{ old('code') }}" placeholder="Contoh: MERDEKA10" class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:border-bjbo-primary focus:outline-none uppercase font-mono">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Diskon (%)</label>
                    <input type="number" name="discount_percentage" min="1" max="100" value="{{ old('discount_percentage') }}" placeholder="10" class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:border-bjbo-primary focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Diskon Nominal (Rp)</label>
                    <input type="number" name="discount_amount" min="0" value="{{ old('discount_amount') }}" placeholder="15000" class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:border-bjbo-primary focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Min. Belanja (Rp)</label>
                    <input type="number" name="min_purchase" min="0" value="{{ old('min_purchase', 0) }}" placeholder="50000" class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:border-bjbo-primary focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Mulai (Opsional)</label>
                    <input type="datetime-local" name="start_date" class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:border-bjbo-primary focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Berakhir (Opsional)</label>
                    <input type="datetime-local" name="end_date" class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:border-bjbo-primary focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Gambar Banner / Iklan (Opsional)</label>
                <input type="file" name="banner_image" accept="image/*" class="w-full text-xs p-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Deskripsi & Syarat Ketentuan</label>
                <textarea name="description" rows="3" placeholder="Jelaskan ketentuan promo..." class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:border-bjbo-primary focus:outline-none">{{ old('description') }}</textarea>
            </div>

            <div class="pt-3 flex gap-2">
                <button type="submit" class="bjbo-btn-primary px-6 py-2.5 text-xs font-semibold rounded-xl shadow">
                    <i class="bi bi-check2"></i> Simpan Promo
                </button>
                <a href="{{ route('admin.promo.index') }}" class="bjbo-btn-outline px-4 py-2.5 text-xs font-semibold rounded-xl">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
