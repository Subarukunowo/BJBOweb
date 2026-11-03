<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        $sanitizedName = $this->name ? strip_tags(trim($this->name)) : null;
        $sanitizedEmail = $this->email ? strtolower(strip_tags(trim($this->email))) : null;

        $this->merge([
            'name' => $sanitizedName,
            'email' => $sanitizedEmail,
        ]);
    }

    public function rules(): array
    {
        $userId = Auth::id();

        return [
            'name' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\.\'\-]+$/'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email,'.$userId],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'password' => ['nullable', 'string', 'min:6', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.regex' => 'Nama hanya boleh mengandung huruf, spasi, titik, atau tanda petik.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'profile_picture.image' => 'Berkas foto profil harus berupa gambar.',
            'profile_picture.mimes' => 'Format gambar foto profil harus jpeg, png, jpg, atau webp.',
            'profile_picture.max' => 'Ukuran foto profil maksimal adalah 2MB.',
            'password.min' => 'Kata sandi baru minimal 6 karakter jika ingin diubah.',
        ];
    }
}
