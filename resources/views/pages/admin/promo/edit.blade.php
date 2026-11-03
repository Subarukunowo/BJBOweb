@extends('layouts.admin.main')

@section('title', 'Edit Promo / Iklan - BJBO Sedagho')

@section('content')
<div class="space-y-6 max-w-3xl">
    <!-- Header Page -->
    <div class="flex items-center justify-between pb-4 border-b border-bjbo-border">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Edit Iklan / Promo Diskon</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Perbarui rincian kampanye promosi.</p>
        </div>
        <a href="{{ route('admin.promo.index') }}" class="bjbo-btn-outline px-3.5 py-2 text-xs font-semibold rounded-xl">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Form Edit Promo -->
    <div class="bg-white border border-bjbo-border rounded-2xl p-6 shadow-2xs">
        <form action="{{ route('admin.promo.update', $promo->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Judul Promo / Iklan <span class="text-red-500">*</span></label>
                <input type="text" name="title" required value="{{ old('title', $promo->title) }}" class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:border-bjbo-primary focus:outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tipe Promo <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:border-bjbo-primary focus:outline-none">
                        <option value="diskon_persen" {{ $promo->type === 'diskon_persen' ? 'selected' : '' }}>Diskon Persentase (%)</option>
                        <option value="diskon_nominal" {{ $promo->type === 'diskon_nominal' ? 'selected' : '' }}>Diskon Nominal Tetap (Rp)</option>
                        <option value="banner_iklan" {{ $promo->type === 'banner_iklan' ? 'selected' : '' }}>Banner Iklan Utama</option>
                        <option value="flash_sale" {{ $promo->type === 'flash_sale' ? 'selected' : '' }}>Flash Sale</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kode Kupon / Voucher (Opsional)</label>
                    <input type="text" name="code" value="{{ old('code', $promo->code) }}" class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:border-bjbo-primary focus:outline-none uppercase font-mono">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Diskon (%)</label>
                    <input type="number" name="discount_percentage" min="1" max="100" value="{{ old('discount_percentage', $promo->discount_percentage) }}" class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:border-bjbo-primary focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Diskon Nominal (Rp)</label>
                    <input type="number" name="discount_amount" min="0" value="{{ old('discount_amount', $promo->discount_amount) }}" class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:border-bjbo-primary focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Min. Belanja (Rp)</label>
                    <input type="number" name="min_purchase" min="0" value="{{ old('min_purchase', $promo->min_purchase) }}" class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:border-bjbo-primary focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Gambar Banner Baru (Opsional)</label>
                @if($promo->banner_image)
                    <div class="mb-2">
                        <img src="{{ asset('storage/'.$promo->banner_image) }}" alt="Banner" class="h-20 rounded-lg object-cover border border-gray-200">
                    </div>
                @endif
                <input type="file" name="banner_image" accept="image/*" class="w-full text-xs p-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Deskripsi & Syarat Ketentuan</label>
                <textarea name="description" rows="3" class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:border-bjbo-primary focus:outline-none">{{ old('description', $promo->description) }}</textarea>
            </div>

            <div class="pt-3 flex gap-2">
                <button type="submit" class="bjbo-btn-primary px-6 py-2.5 text-xs font-semibold rounded-xl shadow">
                    <i class="bi bi-check2"></i> Perbarui Promo
                </button>
                <a href="{{ route('admin.promo.index') }}" class="bjbo-btn-outline px-4 py-2.5 text-xs font-semibold rounded-xl">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
