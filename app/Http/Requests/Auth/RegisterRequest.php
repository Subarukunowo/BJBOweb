<?php

namespace App\Http\Requests\Auth;

use App\Services\EmailSecurityService;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $rawName = (string) $this->input('name', '');
        $rawEmail = (string) $this->input('email', '');

        $sanitizedName = strip_tags(trim($rawName));
        $sanitizedEmail = strtolower(strip_tags(trim($rawEmail)));

        $this->merge([
            'name' => $sanitizedName,
            'email' => $sanitizedEmail,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z0-9\s\.\'\-]+$/'],
            'email' => [
                'required',
                'email',
                'max:150',
                'unique:users,email',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $securityService = app(EmailSecurityService::class);
                    if ($securityService->isDummyEmail((string) $value)) {
                        $fail('Alamat email terdeteksi sebagai email sementara (disposable/dummy email) atau domain tidak valid. Harap gunakan email aktif yang sah (seperti Gmail, Yahoo, Outlook, dsb).');
                    }
                },
            ],
            'password' => ['required', 'string', 'min:6', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.regex' => 'Nama tidak boleh mengandung simbol berbahaya atau kode skrip.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar. Silakan gunakan email lain.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'password.max' => 'Kata sandi maksimal 50 karakter.',
        ];
    }
}
