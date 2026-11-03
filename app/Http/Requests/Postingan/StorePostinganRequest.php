<?php

namespace App\Http\Requests\Postingan;

use Illuminate\Foundation\Http\FormRequest;

class StorePostinganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $rawName = (string) $this->input('name', '');
        $rawDesc = (string) $this->input('description', '');
        $rawLokasi = (string) $this->input('lokasi', '');

        $this->merge([
            'name' => strip_tags(trim($rawName)),
            'description' => strip_tags(trim($rawDesc)),
            'lokasi' => strip_tags(trim($rawLokasi)),
            'kondisi' => $this->input('kondisi', 'Bekas (Pre-loved)'),
            'tipe_penawaran' => $this->input('tipe_penawaran', 'Bisa Nego'),
            'status' => $this->input('status', 'belum disetujui'),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:1000',
            'category' => 'required|string|max:100',
            'kondisi' => 'nullable|string|max:50',
            'tipe_penawaran' => 'nullable|string|max:50',
            'description' => 'required|string',
            'image' => 'required|image|mimes:png,jpeg,jpg,webp|max:2048',
            'lokasi' => 'required|string|max:255',
            'status' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama produk wajib diisi.',
            'price.required' => 'Harga produk wajib diisi.',
            'category.required' => 'Kategori produk wajib dipilih.',
            'description.required' => 'Deskripsi produk wajib diisi.',
            'image.required' => 'Foto produk wajib diunggah.',
            'image.image' => 'Berkas harus berupa gambar valid.',
            'image.max' => 'Ukuran foto maksimal adalah 2MB.',
            'lokasi.required' => 'Lokasi penjual wajib diisi.',
        ];
    }
}
