<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function registerUser(array $data): User
    {
        return $this->userRepository->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function attemptLogin(array $credentials, bool $remember = true): array
    {
        $identifier = $credentials['email'] ?? '';
        $password = $credentials['password'] ?? '';
        $isEmail = (bool) filter_var($identifier, FILTER_VALIDATE_EMAIL);

        $adminCreds = $isEmail
            ? ['email' => $identifier, 'password' => $password]
            : ['username' => $identifier, 'password' => $password];

        if (Auth::guard('admin')->attempt($adminCreds, $remember)) {
            return [
                'success' => true,
                'role' => 'admin',
                'message' => 'Selamat datang, admin!',
            ];
        }

        $userCreds = ['password' => $password];
        if ($isEmail) {
            $userCreds['email'] = $identifier;
        } elseif (is_numeric($identifier)) {
            $userCreds['id'] = (int) $identifier;
        } else {
            $userCreds['email'] = $identifier;
        }

        if (Auth::attempt($userCreds, $remember)) {
            $user = Auth::user();

            if ($user->is_blocked) {
                Auth::logout();

                return [
                    'success' => false,
                    'role' => null,
                    'message' => 'Akun Anda telah ditangguhkan/diblokir oleh admin.',
                ];
            }

            return [
                'success' => true,
                'role' => 'user',
                'message' => 'Selamat datang kembali di BJBO!',
            ];
        }

        return [
            'success' => false,
            'role' => null,
            'message' => 'Email/ID atau kata sandi tidak valid.',
        ];
    }

    public function logoutAdmin(): void
    {
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        }
    }

    public function logoutUser(): void
    {
        if (Auth::check()) {
            Auth::logout();
        }
    }
}
