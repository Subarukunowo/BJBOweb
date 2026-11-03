<?php

namespace App\Http\Requests\Ulasan;

use Illuminate\Foundation\Http\FormRequest;

class StoreUlasanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'postingan_id' => 'required|exists:postingans,id',
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'required|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'postingan_id.required' => 'Postingan terkait wajib ditentukan.',
            'postingan_id.exists' => 'Postingan tidak ditemukan.',
            'rating.required' => 'Penilaian bintang wajib dipilih.',
            'rating.min' => 'Rating minimal 1 bintang.',
            'rating.max' => 'Rating maksimal 5 bintang.',
            'komentar.required' => 'Komentar ulasan wajib diisi.',
        ];
    }
}
