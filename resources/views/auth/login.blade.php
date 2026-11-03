<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - BJBO Sedagho</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bjbo-bg text-gray-900 font-sans min-h-screen flex flex-col justify-between p-4 sm:p-6">
    @include('sweetalert::alert')

    <!-- Header Kecil -->
    <div class="w-full max-w-md mx-auto pt-4 flex items-center justify-between">
        <a href="{{ route('layout') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-bjbo-primary transition">
            <i class="bi bi-arrow-left"></i> Kembali ke Pasar
        </a>
        <span class="text-xs font-mono text-gray-400">BJBO Auth</span>
    </div>

    <!-- Wadah Form Kartu Login -->
    <div class="w-full max-w-md mx-auto my-auto py-6">
        <div class="bg-white border border-bjbo-border rounded-2xl p-6 sm:p-8 shadow-sm">
            <div class="text-center mb-6">
                <a href="{{ route('layout') }}" class="inline-block mb-3">
                    <img src="{{ asset('images/logo.png') }}" alt="BJBO Logo" class="h-10 mx-auto object-contain">
                </a>
                <h1 class="text-xl font-bold text-gray-900 tracking-tight">Masuk ke Akun Anda</h1>
                <p class="text-xs text-gray-500 mt-1">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="text-bjbo-primary font-semibold hover:underline">
                        Daftar sekarang
                    </a>
                </p>
            </div>

            <!-- Notifikasi Pesan Flash / Error -->
            @if(session('error'))
                <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-xs text-red-700 flex items-start gap-2">
                    <i class="bi bi-exclamation-circle-fill text-red-500 mt-0.5"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('warning'))
                <div class="mb-4 p-3 rounded-lg bg-amber-50 border border-amber-200 text-xs text-amber-800 flex items-start gap-2">
                    <i class="bi bi-info-circle-fill text-amber-500 mt-0.5"></i>
                    <span>{{ session('warning') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-4 p-3 rounded-lg bg-green-50 border border-green-200 text-xs text-green-700 flex items-start gap-2">
                    <i class="bi bi-check-circle-fill text-green-500 mt-0.5"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('post.login') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-700 mb-1.5">Alamat Email / ID Akun</label>
                    <div class="relative">
                        <i class="bi bi-person-badge absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" id="email" name="email" value="{{ old('email') }}" class="w-full pl-10 pr-3.5 py-2.5 text-sm bg-white border border-bjbo-border-strong rounded-lg focus:border-bjbo-primary focus:ring-2 focus:ring-bjbo-primary/20 focus:outline-none transition" placeholder="nama@email.com atau ID akun" required autofocus autocomplete="username">
                    </div>
                    @error('email')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-gray-700 mb-1.5">Kata Sandi</label>
                    <div class="relative">
                        <i class="bi bi-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="password" id="password" name="password" class="w-full pl-10 pr-3.5 py-2.5 text-sm bg-white border border-bjbo-border-strong rounded-lg focus:border-bjbo-primary focus:ring-2 focus:ring-bjbo-primary/20 focus:outline-none transition" placeholder="Masukkan kata sandi" required>
                    </div>
                    @error('password')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 cursor-pointer text-gray-700 hover:text-gray-900 select-none">
                        <input type="checkbox" name="remember" value="1" checked class="w-4 h-4 rounded text-bjbo-primary border-gray-300 focus:ring-bjbo-primary">
                        <span>Ingat saya di perangkat ini (Tetap Masuk)</span>
                    </label>
                </div>

                <button type="submit" class="bjbo-btn-primary w-full py-2.5 text-sm">
                    Masuk ke BJBO
                </button>
            </form>

            <div class="relative my-6 text-center">
                <hr class="border-gray-200">
                <span class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white px-3 text-[11px] text-gray-400 font-medium uppercase tracking-wider">
                    Atau masuk dengan
                </span>
            </div>

            <!-- Tombol Social Login (Google, Facebook, Instagram) -->
            <div class="grid grid-cols-3 gap-2">
                <a href="{{ route('google.redirect') }}" class="inline-flex items-center justify-center gap-1.5 py-2 px-2 border border-bjbo-border-strong rounded-lg text-xs font-semibold text-gray-700 hover:bg-gray-50 transition" title="Masuk dengan Google">
                    <img src="{{ asset('images/google-icon.png') }}" alt="Google" class="h-4 w-4">
                    <span class="hidden sm:inline">Google</span>
                </a>
                <a href="{{ route('facebook.redirect') }}" class="inline-flex items-center justify-center gap-1.5 py-2 px-2 border border-bjbo-border-strong rounded-lg text-xs font-semibold text-blue-600 hover:bg-blue-50/50 transition" title="Masuk dengan Facebook">
                    <i class="bi bi-facebook text-base"></i>
                    <span class="hidden sm:inline">Facebook</span>
                </a>
                <a href="{{ route('instagram.redirect') }}" class="inline-flex items-center justify-center gap-1.5 py-2 px-2 border border-bjbo-border-strong rounded-lg text-xs font-semibold text-pink-600 hover:bg-pink-50/50 transition" title="Masuk dengan Instagram">
                    <i class="bi bi-instagram text-base"></i>
                    <span class="hidden sm:inline">Instagram</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Footer Copyright -->
    <div class="text-center pb-4 text-xs text-gray-400">
        &copy; 2024 BJBO Sedagho. Dikelola oleh PT Subaru Technologi Bangsa.
    </div>
</body>
</html>
