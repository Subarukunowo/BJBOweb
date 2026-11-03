<!-- Footer Komunitas BJBO -->
<footer class="bg-gray-900 text-gray-400 text-sm mt-12 sm:mt-16 pt-10 sm:pt-12 pb-20 lg:pb-6">
    <div class="container mx-auto px-3 sm:px-4">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
            <div class="md:col-span-6">
                <div class="flex items-center gap-2 mb-3">
                    <img src="{{ asset('images/bjbo.png') }}" alt="BJBO Logo" class="h-9 w-auto">
                    <h5 class="text-white font-bold text-lg mb-0">BJBO Sedagho</h5>
                </div>
                <p class="text-gray-400 text-sm leading-relaxed mb-3">
                    Pusat jual beli barang bekas dan baru komunitas lokal. Cepat, aman, dan tanpa perantara pihak ketiga yang merugikan.
                </p>
                <p class="text-gray-400 text-xs">
                    Jl. AL Muslihun, Desa Wonosari Tengah, Kec. Bengkalis, Kab. Bengkalis, Riau 28711
                </p>
            </div>
            <div class="md:col-span-3 col-span-6">
                <h6 class="text-white font-bold mb-3 uppercase tracking-wider text-xs">Navigasi</h6>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('about') }}" class="hover:text-white transition">Tentang Kami & Tim</a></li>
                    <li><a href="{{ route('layout') }}" class="hover:text-white transition">Katalog Produk</a></li>
                    <li><a href="#" class="hover:text-white transition">Syarat & Ketentuan</a></li>
                </ul>
            </div>
            <div class="md:col-span-3 col-span-6">
                <h6 class="text-white font-bold mb-3 uppercase tracking-wider text-xs">Komunitas</h6>
                <p class="text-gray-400 text-xs mb-3">Ikuti perkembangan pasar lokal kami:</p>
                <div class="flex gap-4">
                    <a href="https://www.instagram.com/bjbo.bengkalis/" target="_blank" class="text-gray-400 hover:text-white text-xl transition"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white text-xl transition"><i class="bi bi-facebook"></i></a>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-800 mt-8 pt-6 text-center text-xs text-gray-500">
            &copy; 2024 BJBO Sedagho. Dikelola oleh PT Subaru Technologi Bangsa. Hak cipta dilindungi.
        </div>
    </div>
</footer>
