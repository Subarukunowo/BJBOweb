<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang BJBO - Belanjo & Jual Barang Online Sedagho</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bjbo-bg text-gray-900 font-sans min-h-screen flex flex-col">

<!-- Header Navigasi -->
@if(Auth::check())
    @include('components.navbar-user')
@else
    @include('components.navbar-guest')
@endif

<main class="container mx-auto px-3 sm:px-4 my-6 sm:my-8 flex-grow max-w-5xl pb-20 lg:pb-0">
    <!-- Hero Cerita BJBO -->
    <div class="bg-gradient-to-br from-bjbo-primary to-blue-950 text-white rounded-2xl p-6 sm:p-10 mb-8 shadow-sm">
        <div class="max-w-3xl">
            <span class="inline-block bg-white/15 text-white border border-white/20 text-xs font-mono font-bold px-3 py-1 rounded-full mb-3">
                Kisah & Visi Kami
            </span>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight mb-3">
                Tentang BJBO Sedagho
            </h1>
            <p class="text-gray-200 text-sm sm:text-base leading-relaxed">
                BJBO (Belanjo & Jual Barang Online) lahir dari aspirasi untuk menciptakan ruang transaksi jual beli yang aman, jujur, dan berlandaskan semangat kekeluargaan sesamo sedagho di Bengkalis dan sekitarnya.
            </p>
        </div>
    </div>

    <!-- 3 Pilar Nilai -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="bg-white border border-bjbo-border rounded-2xl p-6 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-bjbo-primary flex items-center justify-center text-xl mb-3">
                <i class="bi bi-shield-check"></i>
            </div>
            <h2 class="font-bold text-base text-gray-900 mb-1">Kurasi Terverifikasi</h2>
            <p class="text-xs text-gray-600 leading-relaxed">
                Setiap barang yang diunggah melalui proses tinjau untuk memastikan informasi barang jelas dan bebas penipuan.
            </p>
        </div>

        <div class="bg-white border border-bjbo-border rounded-2xl p-6 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-orange-50 text-bjbo-accent flex items-center justify-center text-xl mb-3">
                <i class="bi bi-people"></i>
            </div>
            <h2 class="font-bold text-base text-gray-900 mb-1">Sesamo Sedagho</h2>
            <p class="text-xs text-gray-600 leading-relaxed">
                Mengedepankan hubungan kekerabatan warga lokal. Berbelanja sekaligus mempererat silaturahmi antarwarga sekitar.
            </p>
        </div>

        <div class="bg-white border border-bjbo-border rounded-2xl p-6 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-xl mb-3">
                <i class="bi bi-qr-code-scan"></i>
            </div>
            <h2 class="font-bold text-base text-gray-900 mb-1">Pembayaran Digital Bebas Biaya</h2>
            <p class="text-xs text-gray-600 leading-relaxed">
                Mendukung pembayaran langsung di tempat (COD) maupun pembayaran QRIS dan transfer Midtrans Popup secara praktis.
            </p>
        </div>
    </div>

    <!-- Tim Pengembang (Founders) -->
    <div class="mb-12">
        <div class="text-center max-w-xl mx-auto mb-8">
            <h2 class="text-xl sm:text-2xl font-extrabold text-gray-900 mb-1">Tim Pengembang BJBO</h2>
            <p class="text-xs text-gray-500">Mahasiswa dan praktisi teknologi di balik platform komunitas BJBO Sedagho.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Wan Muhammad Syafiq -->
            <div class="bg-white border border-bjbo-border rounded-2xl p-5 text-center shadow-sm flex flex-col items-center">
                <img src="{{ asset('assets/founders/founder1.jpg') }}" class="w-24 h-24 rounded-full object-cover mb-3 shadow" alt="Wan Muhammad Syafiq">
                <h3 class="font-bold text-sm text-gray-900 mb-0.5">Wan Muhammad Syafiq</h3>
                <span class="text-[11px] font-mono text-bjbo-primary bg-blue-50 px-2 py-0.5 rounded border border-blue-200 mb-2">Project Manager</span>
                <p class="text-xs text-gray-500 flex-grow leading-relaxed">Memimpin perencanaan proyek, koordinasi tim, dan perancangan arah strategis aplikasi.</p>
                <a href="https://www.instagram.com/wan_syafiq14/" target="_blank" class="bjbo-btn-outline text-xs py-1 px-3 mt-2">
                    <i class="bi bi-instagram"></i> Kontak
                </a>
            </div>

            <!-- Rahmad Ramadhan -->
            <div class="bg-white border border-bjbo-border rounded-2xl p-5 text-center shadow-sm flex flex-col items-center">
                <img src="{{ asset('assets/founders/founder2.jpg') }}" class="w-24 h-24 rounded-full object-cover mb-3 shadow" alt="Rahmad Ramadhan">
                <h3 class="font-bold text-sm text-gray-900 mb-0.5">Rahmad Ramadhan</h3>
                <span class="text-[11px] font-mono text-bjbo-primary bg-blue-50 px-2 py-0.5 rounded border border-blue-200 mb-2">UI & UX Designer</span>
                <p class="text-xs text-gray-500 flex-grow leading-relaxed">Merancang pengalaman pengguna, tata letak visual, estetika antarmuka, dan sistem desain.</p>
                <a href="https://www.instagram.com/rahmad_ramadhan_13/" target="_blank" class="bjbo-btn-outline text-xs py-1 px-3 mt-2">
                    <i class="bi bi-instagram"></i> Kontak
                </a>
            </div>

            <!-- Muhammad Reza Fahlefi -->
            <div class="bg-white border border-bjbo-border rounded-2xl p-5 text-center shadow-sm flex flex-col items-center">
                <img src="{{ asset('assets/founders/founder3.jpg') }}" class="w-24 h-24 rounded-full object-cover mb-3 shadow" alt="Muhammad Reza Fahlefi">
                <h3 class="font-bold text-sm text-gray-900 mb-0.5">Muhammad Reza Fahlefi</h3>
                <span class="text-[11px] font-mono text-bjbo-primary bg-blue-50 px-2 py-0.5 rounded border border-blue-200 mb-2">Frontend Developer</span>
                <p class="text-xs text-gray-500 flex-grow leading-relaxed">Mengimplementasikan antarmuka interaktif, responsivitas komponen, dan integrasi frontend.</p>
                <a href="https://www.instagram.com/rezafahlevi_05/" target="_blank" class="bjbo-btn-outline text-xs py-1 px-3 mt-2">
                    <i class="bi bi-instagram"></i> Kontak
                </a>
            </div>

            <!-- M Fikri Setiawan -->
            <div class="bg-white border border-bjbo-border rounded-2xl p-5 text-center shadow-sm flex flex-col items-center">
                <img src="{{ asset('assets/founders/founder4.jpg') }}" class="w-24 h-24 rounded-full object-cover mb-3 shadow" alt="M Fikri Setiawan">
                <h3 class="font-bold text-sm text-gray-900 mb-0.5">M Fikri Setiawan</h3>
                <span class="text-[11px] font-mono text-bjbo-primary bg-blue-50 px-2 py-0.5 rounded border border-blue-200 mb-2">Programmer</span>
                <p class="text-xs text-gray-500 flex-grow leading-relaxed">Mengembangkan arsitektur backend Laravel, integrasi database, dan keamanan data.</p>
                <a href="https://www.instagram.com/muhammad_fkriiiiii/" target="_blank" class="bjbo-btn-outline text-xs py-1 px-3 mt-2">
                    <i class="bi bi-instagram"></i> Kontak
                </a>
            </div>

            <!-- Riski Afendi -->
            <div class="bg-white border border-bjbo-border rounded-2xl p-5 text-center shadow-sm flex flex-col items-center">
                <img src="{{ asset('assets/founders/founder5.jpg') }}" class="w-24 h-24 rounded-full object-cover mb-3 shadow" alt="Riski Afendi">
                <h3 class="font-bold text-sm text-gray-900 mb-0.5">Riski Afendi</h3>
                <span class="text-[11px] font-mono text-bjbo-primary bg-blue-50 px-2 py-0.5 rounded border border-blue-200 mb-2">System Analyst</span>
                <p class="text-xs text-gray-500 flex-grow leading-relaxed">Menganalisis kebutuhan sistem, pemodelan relasi database, dan evaluasi proses bisnis.</p>
                <a href="https://www.instagram.com/riski_star_19/" target="_blank" class="bjbo-btn-outline text-xs py-1 px-3 mt-2">
                    <i class="bi bi-instagram"></i> Kontak
                </a>
            </div>
        </div>
    </div>
</main>

<!-- Komponen Mobile Bottom Nav (Khusus Handphone & Tablet Kecil) -->
@include('components.mobile-bottom-nav')

@include('components.footer')

</body>
</html>