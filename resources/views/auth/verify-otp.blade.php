<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email OTP - BJBO Sedagho</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bjbo-bg text-gray-900 font-sans min-h-screen flex flex-col justify-between p-4 sm:p-6">
    @include('sweetalert::alert')

    <!-- Header Kecil -->
    <div class="w-full max-w-md mx-auto pt-4 flex items-center justify-between">
        <a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-bjbo-primary transition">
            <i class="bi bi-arrow-left"></i> Kembali ke Form Daftar
        </a>
        <span class="text-xs font-mono text-gray-400">BJBO Security</span>
    </div>

    <!-- Wadah Form Kartu Verifikasi OTP -->
    <div class="w-full max-w-md mx-auto my-auto py-6">
        <div class="bg-white border border-bjbo-border rounded-2xl p-6 sm:p-8 shadow-sm">
            <div class="text-center mb-6">
                <div class="w-14 h-14 bg-bjbo-accent-light text-bjbo-accent rounded-full flex items-center justify-center mx-auto mb-3 shadow-inner">
                    <i class="bi bi-shield-check text-2xl"></i>
                </div>
                <h1 class="text-xl font-bold text-gray-900 tracking-tight">Verifikasi Email Anda</h1>
                <p class="text-xs text-gray-500 mt-1.5 leading-relaxed">
                    Kami telah mengirimkan 6 digit kode OTP ke alamat email:
                    <br>
                    <span class="font-semibold text-gray-800 font-mono">{{ $email }}</span>
                </p>
            </div>

            @if(session('error'))
                <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-xs text-red-700 flex items-start gap-2">
                    <i class="bi bi-exclamation-circle-fill text-red-500 mt-0.5"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form action="{{ route('otp.verify.submit') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label for="otp" class="block text-center text-xs font-semibold text-gray-700 mb-2">
                        Masukkan 6 Digit Kode OTP
                    </label>
                    <input type="text"
                           id="otp"
                           name="otp"
                           maxlength="6"
                           pattern="[0-9]{6}"
                           inputmode="numeric"
                           autocomplete="one-time-code"
                           class="w-full text-center text-2xl tracking-[0.4em] font-mono py-3 bg-bjbo-surface-subtle border-2 border-bjbo-border-strong rounded-xl focus:border-bjbo-accent focus:bg-white focus:outline-none focus:ring-4 focus:ring-bjbo-accent/15 transition font-bold"
                           placeholder="••••••"
                           required
                           autofocus>
                    @error('otp')
                        <p class="text-xs text-red-600 mt-1.5 text-center">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="bjbo-btn-accent w-full py-2.5 text-sm font-semibold shadow-sm">
                    Verifikasi & Selesaikan Pendaftaran
                </button>
            </form>

            <!-- Bagian Resend dengan Cooldown 60 Detik -->
            <div class="mt-6 pt-5 border-t border-bjbo-border text-center">
                <p class="text-xs text-gray-500 mb-2">Tidak menerima email atau kode kedaluwarsa?</p>
                <form id="resend-form" action="{{ route('otp.resend') }}" method="POST">
                    @csrf
                    <button type="submit"
                            id="resend-btn"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-bjbo-primary hover:text-bjbo-primary-hover disabled:text-gray-400 disabled:cursor-not-allowed transition"
                            {{ $cooldown > 0 ? 'disabled' : '' }}>
                        <i class="bi bi-arrow-clockwise"></i>
                        <span id="resend-label">
                            {{ $cooldown > 0 ? 'Kirim ulang dalam (' . $cooldown . 's)' : 'Kirim Ulang Kode OTP' }}
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer Kecil -->
    <div class="w-full max-w-md mx-auto pb-4 text-center">
        <p class="text-[11px] text-gray-400">
            &copy; {{ date('Y') }} BJBO Sedagho - Keamanan Verifikasi Dua Langkah Komunitas.
        </p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let cooldown = parseInt("{{ $cooldown }}", 10) || 0;
            const resendBtn = document.getElementById('resend-btn');
            const resendLabel = document.getElementById('resend-label');

            function updateTimer() {
                if (cooldown > 0) {
                    resendBtn.disabled = true;
                    resendLabel.textContent = `Kirim ulang dalam (${cooldown}s)`;
                    cooldown--;
                    setTimeout(updateTimer, 1000);
                } else {
                    resendBtn.disabled = false;
                    resendLabel.textContent = 'Kirim Ulang Kode OTP';
                }
            }

            if (cooldown > 0) {
                updateTimer();
            }

            // Otomatis submit jika 6 digit angka telah diketik
            const otpInput = document.getElementById('otp');
            otpInput.addEventListener('input', function (e) {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length === 6) {
                    // Bisa langsung enter atau tekan tombol submit
                }
            });
        });
    </script>
</body>
</html>
