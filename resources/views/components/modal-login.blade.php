<!-- Modal Masuk (Login) -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl rounded-2xl overflow-hidden">
            <div class="p-6 sm:p-8">
                <div class="text-center mb-6">
                    <img src="{{ asset('images/bjbo.png') }}" alt="Logo BJBO" class="h-14 mx-auto mb-2">
                    <h5 class="font-bold text-lg text-gray-900" id="loginModalLabel">Masuk ke Akun Anda</h5>
                    <p class="text-gray-500 text-xs mt-1">
                        Belum punya akun? <a href="#" class="text-bjbo-primary font-semibold hover:underline" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#registerModal">Daftar sekarang</a>
                    </p>
                </div>

                <form action="/post-login" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="loginEmail" class="block text-xs font-semibold text-gray-700 mb-1">Alamat Email</label>
                        <input type="email" class="w-full px-3.5 py-2 text-sm border border-bjbo-border-strong rounded-lg focus:border-bjbo-primary focus:ring-2 focus:ring-bjbo-primary/20 focus:outline-none transition" id="loginEmail" name="email" placeholder="nama@email.com" required>
                    </div>
                    <div>
                        <label for="loginPassword" class="block text-xs font-semibold text-gray-700 mb-1">Kata Sandi</label>
                        <input type="password" class="w-full px-3.5 py-2 text-sm border border-bjbo-border-strong rounded-lg focus:border-bjbo-primary focus:ring-2 focus:ring-bjbo-primary/20 focus:outline-none transition" id="loginPassword" name="password" placeholder="Masukkan sandi Anda" required>
                    </div>
                    <button type="submit" class="bjbo-btn-primary w-full py-2.5 text-center">
                        Masuk ke BJBO
                    </button>
                </form>

                <div class="relative my-6 text-center">
                    <hr class="border-gray-200">
                    <span class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white px-3 text-xs text-gray-400">Atau masuk dengan</span>
                </div>

                <a href="{{ route('google.redirect') }}" class="w-full inline-flex items-center justify-center gap-2 py-2 px-4 border border-bjbo-border-strong rounded-lg text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">
                    <img src="{{ asset('images/google-icon.png') }}" alt="Google" class="h-4 w-4"> Masuk dengan Google
                </a>
            </div>
        </div>
    </div>
</div>
