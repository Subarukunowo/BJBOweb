<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use App\Services\OtpService;
use App\Services\PostinganService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class AuthController extends Controller
{
    protected AuthService $authService;

    protected PostinganService $postinganService;

    protected OtpService $otpService;

    public function __construct(
        AuthService $authService,
        PostinganService $postinganService,
        OtpService $otpService
    ) {
        $this->authService = $authService;
        $this->postinganService = $postinganService;
        $this->otpService = $otpService;
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function login(LoginRequest $request)
    {
        $throttleKey = Str::lower($request->input('email')).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            Alert::warning('Akses Dibatasi!', "Terlalu banyak percobaan login gagal untuk mencegah serangan brute-force dan DDoS. Silakan coba lagi dalam {$seconds} detik.");

            return redirect()->back()->withInput();
        }

        $remember = $request->has('remember') ? $request->boolean('remember') : true;
        $result = $this->authService->attemptLogin($request->only('email', 'password'), $remember);

        if (! $result['success']) {
            RateLimiter::hit($throttleKey, 60);
            $remaining = RateLimiter::remaining($throttleKey, 5);
            Alert::error('Login Gagal!', $result['message']." (Sisa percobaan aman: {$remaining})");

            return redirect()->back()->withInput();
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        if ($result['role'] === 'admin') {
            toast($result['message'], 'success');

            return redirect()->route('admin.dashboard');
        }

        toast($result['message'], 'success');

        return redirect()->route('user.dashboard');
    }

    public function admin_logout()
    {
        $this->authService->logoutAdmin();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        toast('Berhasil logout!', 'success');

        return redirect('/');
    }

    public function user_logout()
    {
        $this->authService->logoutUser();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        toast('Berhasil logout!', 'success');

        return redirect('/');
    }

    public function register(RegisterRequest $request)
    {
        $registerKey = 'register|'.$request->ip();
        if (RateLimiter::tooManyAttempts($registerKey, 3)) {
            $seconds = RateLimiter::availableIn($registerKey);

            return response()->json([
                'success' => false,
                'message' => "Terlalu banyak pendaftaran akun dari IP Anda. Coba lagi dalam {$seconds} detik.",
            ], 429);
        }
        RateLimiter::hit($registerKey, 60);

        $otpResult = $this->otpService->sendOtp($request->input('email'), $request->validated());
        session(['pending_otp_email' => $request->input('email')]);

        return response()->json([
            'success' => true,
            'message' => $otpResult['message'],
            'redirect' => route('otp.verify.show'),
        ]);
    }

    public function post_register(RegisterRequest $request)
    {
        $registerKey = 'register|'.$request->ip();
        if (RateLimiter::tooManyAttempts($registerKey, 3)) {
            $seconds = RateLimiter::availableIn($registerKey);
            Alert::warning('Akses Dibatasi!', "Terlalu banyak pendaftaran akun dari IP Anda. Coba lagi dalam {$seconds} detik.");

            return redirect()->back()->withInput();
        }
        RateLimiter::hit($registerKey, 60);

        $otpResult = $this->otpService->sendOtp($request->input('email'), $request->validated());

        if (! $otpResult['success']) {
            Alert::error('Gagal Mengirim OTP!', $otpResult['message']);

            return redirect()->back()->withInput();
        }

        session(['pending_otp_email' => $request->input('email')]);
        Alert::info('Kode OTP Terkirim!', 'Kami telah mengirimkan 6 digit kode OTP verifikasi ke email '.$request->input('email').'. Silakan verifikasi untuk mengaktifkan akun.');

        return redirect()->route('otp.verify.show');
    }

    public function showVerifyOtp()
    {
        $email = (string) session('pending_otp_email', '');
        if (! $email) {
            return redirect()->route('register');
        }

        $cooldown = $this->otpService->getRemainingCooldown($email);

        return view('auth.verify-otp', compact('email', 'cooldown'));
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ], [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.size' => 'Kode OTP harus berupa 6 digit angka.',
        ]);

        $email = (string) session('pending_otp_email', '');
        if (! $email) {
            Alert::error('Sesi Kedaluwarsa!', 'Sesi pendaftaran Anda telah kedaluwarsa. Silakan lakukan pendaftaran ulang.');

            return redirect()->route('register');
        }

        $result = $this->otpService->verifyOtp($email, (string) $request->input('otp'));

        if (! $result['success']) {
            Alert::error('Verifikasi Gagal!', $result['message']);

            return redirect()->back();
        }

        $user = $this->authService->registerUser($result['data']);
        $user->markEmailAsVerified();

        session()->forget('pending_otp_email');
        Auth::login($user);

        Alert::success('Pendaftaran Berhasil!', 'Akun Anda berhasil diverifikasi. Selamat datang di pasar komunitas BJBO Sedagho!');

        return redirect()->route('user.dashboard');
    }

    public function resendOtp(Request $request)
    {
        $email = (string) session('pending_otp_email', '');
        if (! $email) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Sesi tidak ditemukan.'], 400);
            }

            return redirect()->route('register');
        }

        $result = $this->otpService->resendOtp($email);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($result);
        }

        if (! $result['success']) {
            Alert::warning('Tunggu Sebentar!', $result['message']);
        } else {
            Alert::success('Kode Terkirim!', $result['message']);
        }

        return redirect()->route('otp.verify.show');
    }

    public function splash()
    {
        return view('splash');
    }

    public function layout()
    {
        $postingans = $this->postinganService->getApprovedPostings();

        return view('layout', compact('postingans'));
    }

    public function about()
    {
        return view('about');
    }
}
