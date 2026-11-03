<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Resolve Google redirect URI safely.
     */
    protected function getGoogleRedirectUri(): string
    {
        $configured = config('services.google.redirect');
        if (! empty($configured)) {
            return $configured;
        }

        return url('/auth/google/callback');
    }

    /**
     * Resolve Facebook redirect URI safely.
     */
    protected function getFacebookRedirectUri(): string
    {
        $configured = config('services.facebook.redirect');
        if (! empty($configured)) {
            return $configured;
        }

        return url('/auth/facebook/callback');
    }

    /**
     * Redirect to Google OAuth.
     */
    public function redirectToGoogle()
    {
        $clientId = config('services.google.client_id');
        if (empty($clientId)) {
            return redirect()->route('login')->with('warning', 'Autentikasi Google belum dikonfigurasi di file .env. Silakan masukkan GOOGLE_CLIENT_ID dan GOOGLE_CLIENT_SECRET.');
        }

        try {
            $redirectUri = $this->getGoogleRedirectUri();
            $driver = Socialite::driver('google');
            assert($driver instanceof \Laravel\Socialite\Two\AbstractProvider);

            return $driver->redirectUrl($redirectUri)->redirect();
        } catch (\Throwable $e) {
            return redirect()->route('login')->with('error', 'Gagal menghubungi server Google: '.$e->getMessage());
        }
    }

    /**
     * Handle Google OAuth Callback.
     */
    public function handleGoogleCallback()
    {
        try {
            $redirectUri = $this->getGoogleRedirectUri();
            $driver = Socialite::driver('google');
            assert($driver instanceof \Laravel\Socialite\Two\AbstractProvider);
            $socialUser = $driver->redirectUrl($redirectUri)->stateless()->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')->with('error', 'Gagal memproses autentikasi Google: '.$e->getMessage());
        }

        return $this->loginOrRegisterSocialUser($socialUser, 'google');
    }

    /**
     * Redirect to Facebook OAuth.
     */
    public function redirectToFacebook()
    {
        $clientId = config('services.facebook.client_id');
        if (empty($clientId)) {
            return redirect()->route('login')->with('warning', 'Autentikasi Facebook belum dikonfigurasi di file .env. Silakan masukkan FACEBOOK_CLIENT_ID dan FACEBOOK_CLIENT_SECRET.');
        }

        try {
            $redirectUri = $this->getFacebookRedirectUri();
            $driver = Socialite::driver('facebook');
            assert($driver instanceof \Laravel\Socialite\Two\AbstractProvider);

            return $driver->redirectUrl($redirectUri)->redirect();
        } catch (\Throwable $e) {
            return redirect()->route('login')->with('error', 'Gagal menghubungi server Facebook: '.$e->getMessage());
        }
    }

    /**
     * Handle Facebook OAuth Callback.
     */
    public function handleFacebookCallback()
    {
        try {
            $redirectUri = $this->getFacebookRedirectUri();
            $driver = Socialite::driver('facebook');
            assert($driver instanceof \Laravel\Socialite\Two\AbstractProvider);
            $socialUser = $driver->redirectUrl($redirectUri)->stateless()->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')->with('error', 'Gagal memproses autentikasi Facebook: '.$e->getMessage());
        }

        return $this->loginOrRegisterSocialUser($socialUser, 'facebook');
    }

    /**
     * Redirect to Instagram OAuth.
     */
    public function redirectToInstagram()
    {
        $clientId = config('services.instagram.client_id');
        $redirectUri = config('services.instagram.redirect') ?: url('/auth/instagram/callback');

        if (empty($clientId)) {
            return redirect()->route('login')->with('warning', 'Autentikasi Instagram belum dikonfigurasi di file .env. Silakan lengkapi INSTAGRAM_CLIENT_ID dan INSTAGRAM_CLIENT_SECRET.');
        }

        $url = 'https://api.instagram.com/oauth/authorize'.
            '?client_id='.urlencode($clientId).
            '&redirect_uri='.urlencode($redirectUri).
            '&scope=user_profile,user_media'.
            '&response_type=code';

        return redirect()->away($url);
    }

    /**
     * Handle Instagram OAuth Callback.
     */
    public function handleInstagramCallback(Request $request)
    {
        $code = $request->input('code');
        if (! $code) {
            return redirect()->route('login')->with('error', 'Autentikasi Instagram dibatalkan atau kode otorisasi tidak ditemukan.');
        }

        $clientId = config('services.instagram.client_id');
        $clientSecret = config('services.instagram.client_secret');
        $redirectUri = config('services.instagram.redirect') ?: url('/auth/instagram/callback');

        try {
            $response = Http::asForm()->post('https://api.instagram.com/oauth/access_token', [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $redirectUri,
                'code' => $code,
            ]);

            if ($response->failed()) {
                return redirect()->route('login')->with('error', 'Gagal menukarkan token autentikasi Instagram.');
            }

            $data = $response->json();
            $instagramId = $data['user_id'] ?? null;
            $accessToken = $data['access_token'] ?? null;

            if (! $instagramId) {
                return redirect()->route('login')->with('error', 'ID pengguna Instagram tidak ditemukan.');
            }

            $profileResponse = Http::get("https://graph.instagram.com/me?fields=id,username&access_token={$accessToken}");
            $username = $profileResponse->json()['username'] ?? 'ig_user_'.$instagramId;

            $user = User::where('instagram_id', $instagramId)->first();
            if (! $user) {
                $user = User::create([
                    'name' => $username,
                    'email' => $username.'@instagram.local',
                    'instagram_id' => $instagramId,
                    'password' => bcrypt(Str::random(24)),
                ]);
            }

            if ($user->is_blocked) {
                return redirect()->route('login')->with('error', 'Akun Anda sedang diblokir oleh admin.');
            }

            Auth::login($user);

            return redirect()->route('user.dashboard');
        } catch (\Throwable $e) {
            return redirect()->route('login')->with('error', 'Terjadi kesalahan sistem Instagram: '.$e->getMessage());
        }
    }

    /**
     * Helper to login or create a social user.
     */
    protected function loginOrRegisterSocialUser($socialUser, string $provider)
    {
        $providerColumn = $provider.'_id';
        $socialId = $socialUser->getId();
        $email = $socialUser->getEmail();
        $avatar = $socialUser->getAvatar();

        $query = User::where($providerColumn, $socialId);
        if ($email) {
            $query->orWhere('email', $email);
        }
        $user = $query->first();

        if (! $user) {
            $user = User::create([
                'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? ucfirst($provider).' User',
                'email' => $email ?? ($socialId.'@'.$provider.'.local'),
                $providerColumn => $socialId,
                'profile_picture' => $avatar,
                'password' => bcrypt(Str::random(24)),
            ]);
        } else {
            $updates = [];
            if (! $user->$providerColumn) {
                $updates[$providerColumn] = $socialId;
            }
            if (empty($user->profile_picture) && ! empty($avatar)) {
                $updates['profile_picture'] = $avatar;
            }
            if (! empty($updates)) {
                $user->update($updates);
            }
        }

        if ($user->is_blocked) {
            return redirect()->route('login')->with('error', 'Akun Anda sedang diblokir oleh admin.');
        }

        Auth::login($user);
        request()->session()->regenerate();

        return redirect()->route('user.dashboard');
    }
}
