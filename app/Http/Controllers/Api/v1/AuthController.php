<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\User;
use App\Services\AuthService;
use App\Services\OtpService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends BaseApiController
{
    public function __construct(
        protected AuthService $authService,
        protected OtpService $otpService,
        protected UserService $userService
    ) {}

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
        ]);

        $throttleKey = 'register-api|'.$request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return $this->errorResponse("Terlalu banyak permintaan pendaftaran. Silakan coba lagi dalam {$seconds} detik.", 429);
        }
        RateLimiter::hit($throttleKey, 60);

        $otpResult = $this->otpService->sendOtp($validated['email'], $validated);

        if (! $otpResult['success']) {
            return $this->errorResponse($otpResult['message'], 400);
        }

        return $this->successResponse([
            'email' => $validated['email'],
            'cooldown' => $otpResult['cooldown'] ?? 60,
        ], $otpResult['message']);
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'otp' => 'required|string|size:6',
        ]);

        $result = $this->otpService->verifyOtp($validated['email'], $validated['otp']);

        if (! $result['success']) {
            return $this->errorResponse($result['message'], 400);
        }

        $user = $this->authService->registerUser($result['data']);
        $user->markEmailAsVerified();

        $tokenResult = $user->createToken('mobile-app');

        return $this->successResponse([
            'user' => $user,
            'token' => $tokenResult->plainTextToken,
            'token_type' => 'Bearer',
        ], 'Pendaftaran akun berhasil diverifikasi.');
    }

    public function resendOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
        ]);

        $result = $this->otpService->resendOtp($validated['email']);

        if (! $result['success']) {
            return $this->errorResponse($result['message'], 429);
        }

        return $this->successResponse([
            'cooldown' => $result['cooldown'] ?? 60,
        ], $result['message']);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $throttleKey = Str::lower($validated['email']).'|'.$request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return $this->errorResponse("Percobaan login dibatasi. Silakan coba lagi dalam {$seconds} detik.", 429);
        }

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            RateLimiter::hit($throttleKey, 60);

            return $this->errorResponse('Kredensial yang dimasukkan tidak cocok dengan data kami.', 401);
        }

        if ($user->is_blocked) {
            return $this->errorResponse('Akun Anda telah dinonaktifkan oleh administrator.', 403);
        }

        RateLimiter::clear($throttleKey);

        $tokenResult = $user->createToken('mobile-app');

        return $this->successResponse([
            'user' => $user,
            'token' => $tokenResult->plainTextToken,
            'token_type' => 'Bearer',
        ], 'Login berhasil.');
    }

    public function me(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $user->loadMissing(['store']);

        return $this->successResponse([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'avatar_url' => $user->profile_picture_url,
            'saldo_bjpay' => $user->saldo_bjpay ?? 0,
            'formatted_saldo_bjpay' => $user->formatted_saldo_bjpay,
            'saldo_rekber' => $user->saldo_rekber ?? 0,
            'has_active_store' => $user->hasActiveStore(),
            'store' => $user->store,
        ], 'Data profil berhasil diambil.');
    }

    public function updateProfile(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $this->userService->updateProfile(
            $user->id,
            $validated,
            $request->file('profile_picture')
        );

        return $this->successResponse($user->fresh(), 'Profil berhasil diperbarui.');
    }

    public function logout(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return $this->successResponse(null, 'Logout berhasil dilakukan.');
    }
}
