<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kotak Masuk Pesan - BJBO Sedagho</title>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Vite + Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bjbo-bg text-gray-900 font-sans min-h-screen flex flex-col">
@include('sweetalert::alert')

<!-- Komponen Navbar Pengguna -->
@include('components.navbar-user')

<main class="container mx-auto px-3 sm:px-6 py-6 sm:py-8 flex-grow pb-24 lg:pb-12 max-w-5xl">
    <div class="flex items-center justify-between mb-6 pb-4 border-b border-bjbo-border">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Kotak Masuk Chat</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Komunikasi tawar-menawar dan tanya ketersediaan stok barang antar-warga Sedagho.</p>
        </div>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
            <i class="bi bi-shield-lock-fill"></i> E2E Encrypted
        </span>
    </div>

    @if($conversations->isEmpty())
        <div class="bg-white border border-bjbo-border rounded-2xl p-8 sm:p-12 text-center max-w-lg mx-auto shadow-xs">
            <div class="w-16 h-16 bg-blue-50 text-bjbo-primary rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="bi bi-chat-dots text-3xl"></i>
            </div>
            <h3 class="text-base font-bold text-gray-900">Belum Ada Percakapan</h3>
            <p class="text-xs text-gray-500 mt-1.5 max-w-xs mx-auto">
                Mulai obrolan dengan menekan tombol <strong>"Tanya Stok"</strong> di halaman produk yang Anda minati.
            </p>
            <div class="mt-6">
                <a href="{{ route('user.dashboard') }}" class="bjbo-btn-primary px-5 py-2.5 text-xs font-semibold">
                    <i class="bi bi-bag-check mr-1.5"></i> Jelajahi Produk Pasar
                </a>
            </div>
        </div>
    @else
        <div class="bg-white border border-bjbo-border rounded-2xl divide-y divide-bjbo-border overflow-hidden shadow-sm">
            @foreach($conversations as $conv)
                @php
                    $otherUser = $conv['other_user'];
                    $product = $conv['postingan'];
                    $latest = $conv['latest_message'];
                    $unread = $conv['unread_count'];
                    $url = route('user.chat.room', ['otherUserId' => $otherUser->id, 'postinganId' => $product ? $product->id : null]);
                @endphp
                <a href="{{ $url }}" class="flex items-center gap-3.5 sm:gap-4 p-4 hover:bg-bjbo-surface-subtle transition block group">
                    <div class="relative shrink-0">
                        <img src="{{ $otherUser->avatar_url }}" alt="{{ $otherUser->name }}" class="w-12 h-12 rounded-full object-cover border border-bjbo-border">
                        @if($unread > 0)
                            <span class="absolute -top-1 -right-1 bg-bjbo-accent text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center ring-2 ring-white">
                                {{ $unread }}
                            </span>
                        @endif
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <h2 class="text-sm font-bold text-gray-900 truncate group-hover:text-bjbo-primary transition">
                                {{ $otherUser->name }}
                            </h2>
                            <span class="text-[11px] font-mono text-gray-400 shrink-0">
                                {{ $latest->created_at->diffForHumans() }}
                            </span>
                        </div>

                        @if($product)
                            <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-600 mb-1 max-w-full truncate">
                                <i class="bi bi-box-seam text-gray-400"></i>
                                <span class="truncate">{{ $product->name }}</span>
                                <span class="font-mono text-gray-800 font-semibold shrink-0">{{ $product->formatted_price }}</span>
                            </div>
                        @endif

                        <p class="text-xs text-gray-500 truncate {{ $unread > 0 ? 'font-semibold text-gray-900' : '' }}">
                            @if($latest->decrypted_message === 'pesan ini tidak dapat dikirim')
                                <span class="text-red-600 italic">
                                    <i class="bi bi-slash-circle mr-1"></i> Pesan disensor (nomor telepon diblokir)
                                </span>
                            @else
                                {{ $latest->sender_id === auth()->id() ? 'Anda: ' : '' }}{{ $latest->decrypted_message }}
                            @endif
                        </p>
                    </div>

                    <div class="shrink-0 text-gray-300 group-hover:text-bjbo-primary transition pl-2">
                        <i class="bi bi-chevron-right text-sm"></i>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</main>

<!-- Komponen Mobile Bottom Nav -->
@include('components.mobile-bottom-nav')

<!-- Komponen Footer -->
@include('components.footer')

</body>
</html>
