<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $rawEmail = (string) $this->input('email', '');
        $sanitizedEmail = strtolower(strip_tags(trim($rawEmail)));

        $this->merge([
            'email' => $sanitizedEmail,
        ]);
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'max:150', 'regex:/^[a-zA-Z0-9_\-\.\@\+]+$/'],
            'password' => ['required', 'string', 'min:6', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email atau ID login wajib diisi.',
            'email.regex' => 'Format email atau ID login mengandung karakter yang tidak diizinkan.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'password.max' => 'Kata sandi maksimal 50 karakter.',
        ];
    }
}
