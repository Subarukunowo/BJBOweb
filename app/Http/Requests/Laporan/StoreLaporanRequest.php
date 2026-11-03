<?php

namespace App\Http\Requests\Laporan;

use Illuminate\Foundation\Http\FormRequest;

class StoreLaporanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jenis_laporan' => 'required|string|max:100',
            'teks' => 'required|string|max:1000',
            'postingan_id' => 'required|exists:postingans,id',
        ];
    }

    public function messages(): array
    {
        return [
            'jenis_laporan.required' => 'Jenis laporan wajib dipilih.',
            'teks.required' => 'Keterangan laporan wajib diisi.',
            'postingan_id.required' => 'Postingan terkait wajib ditentukan.',
            'postingan_id.exists' => 'Postingan yang dilaporkan tidak ditemukan.',
        ];
    }
}
