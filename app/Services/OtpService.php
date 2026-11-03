<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\EmailOtp;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    /**
     * Mengirimkan kode OTP verifikasi baru ke email calon pengguna.
     * Mengikuti interval minimal 60 detik untuk permintaan kirim ulang.
     * Tidak memiliki perulangan (loop-free).
     */
    public function sendOtp(string $email, array $registrationData): array
    {
        $existing = EmailOtp::where('email', $email)->first();

        if ($existing && ! $existing->canResend()) {
            return [
                'success' => false,
                'message' => 'Harap tunggu '.$existing->remainingCooldownSeconds().' detik sebelum meminta kode baru.',
                'cooldown' => $existing->remainingCooldownSeconds(),
            ];
        }

        $otp = (string) random_int(100000, 999999);
        $expiresAt = now()->addMinutes(10);

        $record = EmailOtp::updateOrCreate(
            ['email' => $email],
            [
                'otp' => $otp,
                'registration_data' => json_encode($registrationData),
                'last_sent_at' => now(),
                'expires_at' => $expiresAt,
            ]
        );

        try {
            Mail::to($email)->send(new OtpMail($otp, $registrationData['name'] ?? 'Pengguna'));
        } catch (\Throwable $e) {
            Log::warning('Pengiriman email OTP via mailer gagal, disimpan di log: '.$e->getMessage());
        }

        Log::info("Kode OTP BJBO untuk [{$email}]: {$otp}");

        return [
            'success' => true,
            'message' => 'Kode OTP verifikasi berhasil dikirim ke '.$email,
            'cooldown' => 60,
            'otp' => app()->environment('local') ? $otp : null,
        ];
    }

    /**
     * Mengirim ulang kode OTP untuk email yang sedang dalam proses verifikasi.
     * Memastikan jeda interval 60 detik tetap dipatuhi.
     */
    public function resendOtp(string $email): array
    {
        $record = EmailOtp::where('email', $email)->first();

        if (! $record) {
            return [
                'success' => false,
                'message' => 'Sesi verifikasi tidak ditemukan. Silakan isi form registrasi kembali.',
                'cooldown' => 0,
            ];
        }

        if (! $record->canResend()) {
            return [
                'success' => false,
                'message' => 'Harap tunggu '.$record->remainingCooldownSeconds().' detik lagi untuk mengirim ulang.',
                'cooldown' => $record->remainingCooldownSeconds(),
            ];
        }

        $data = json_decode((string) $record->registration_data, true) ?: [];
        $name = $data['name'] ?? 'Pengguna';

        $newOtp = (string) random_int(100000, 999999);
        $record->update([
            'otp' => $newOtp,
            'last_sent_at' => now(),
            'expires_at' => now()->addMinutes(10),
        ]);

        try {
            Mail::to($email)->send(new OtpMail($newOtp, $name));
        } catch (\Throwable $e) {
            Log::warning('Pengiriman ulang email OTP gagal: '.$e->getMessage());
        }

        Log::info("Kirim Ulang Kode OTP BJBO untuk [{$email}]: {$newOtp}");

        return [
            'success' => true,
            'message' => 'Kode OTP baru telah dikirimkan ke '.$email,
            'cooldown' => 60,
            'otp' => app()->environment('local') ? $newOtp : null,
        ];
    }

    /**
     * Memverifikasi kode OTP yang dimasukkan oleh pengguna.
     */
    public function verifyOtp(string $email, string $inputOtp): array
    {
        $record = EmailOtp::where('email', $email)->first();

        if (! $record) {
            return [
                'success' => false,
                'message' => 'Sesi pendaftaran tidak ditemukan atau telah kedaluwarsa.',
            ];
        }

        if ($record->isExpired()) {
            return [
                'success' => false,
                'message' => 'Kode OTP telah kedaluwarsa. Silakan minta kode baru.',
            ];
        }

        if (trim($record->otp) !== trim($inputOtp)) {
            return [
                'success' => false,
                'message' => 'Kode OTP yang Anda masukkan salah. Silakan periksa kembali email Anda.',
            ];
        }

        $registrationData = json_decode((string) $record->registration_data, true) ?: [];

        // Hapus record OTP setelah berhasil diverifikasi
        $record->delete();

        return [
            'success' => true,
            'message' => 'Verifikasi email berhasil!',
            'data' => $registrationData,
        ];
    }

    /**
     * Mendapatkan sisa waktu cooldown untuk email tertentu.
     */
    public function getRemainingCooldown(string $email): int
    {
        $record = EmailOtp::where('email', $email)->first();
        if (! $record) {
            return 0;
        }

        return $record->remainingCooldownSeconds();
    }
}
