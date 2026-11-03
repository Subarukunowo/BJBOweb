<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi & Pemberitahuan Real-time - BJBO Sedagho</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" crossorigin="anonymous">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bjbo-bg text-gray-900 font-sans min-h-screen flex flex-col">

@include('sweetalert::alert')
@include('components.navbar-user')

<main class="container mx-auto px-3 sm:px-4 my-6 flex-grow max-w-4xl">
    <!-- Header Notifikasi -->
    <div class="flex items-center justify-between gap-4 mb-6 pb-4 border-b border-bjbo-border">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-blue-100 text-bjbo-primary flex items-center justify-center text-xl">
                <i class="bi bi-bell-fill"></i>
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Notifikasi Saya</h1>
                <p class="text-xs sm:text-sm text-gray-500">Pemberitahuan aktivitas chat, pembayaran pesanan, dan transaksi Rekber</p>
            </div>
        </div>

        @if($unreadCount > 0)
        <form action="{{ route('user.notifikasi.readAll') }}" method="POST">
            @csrf
            <button type="submit" class="bjbo-btn-outline text-xs py-2 px-3 flex items-center gap-1.5 shadow-xs">
                <i class="bi bi-check2-all text-sm"></i>
                <span>Tandai Semua Dibaca</span>
            </button>
        </form>
        @endif
    </div>

    <!-- Alert Real-time Reverb Banner -->
    <div id="liveAlertBox" class="hidden bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-3 mb-4 text-xs flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
            <span id="liveAlertText">Notifikasi baru diterima!</span>
        </div>
        <button type="button" onclick="this.parentElement.classList.add('hidden')" class="text-emerald-700 hover:text-emerald-900 font-bold">&times;</button>
    </div>

    <!-- List Notifikasi -->
    <div class="bg-white border border-bjbo-border rounded-2xl shadow-sm divide-y divide-bjbo-border overflow-hidden" id="notificationList">
        @forelse($notifications as $notif)
            @php
                $bgClass = $notif->is_read ? 'bg-white' : 'bg-blue-50/40';
                $badgeColors = [
                    'chat' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                    'pembayaran' => 'bg-amber-100 text-amber-800 border-amber-200',
                    'pesanan' => 'bg-blue-100 text-blue-800 border-blue-200',
                    'rekber' => 'bg-purple-100 text-purple-800 border-purple-200',
                    'toko' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                    'general' => 'bg-gray-100 text-gray-800 border-gray-200',
                ];
                $typeBadge = $badgeColors[$notif->type] ?? $badgeColors['general'];
            @endphp
            <div class="{{ $bgClass }} p-4 sm:p-5 flex items-start justify-between gap-3 transition hover:bg-gray-50/70" id="notif-{{ $notif->id }}">
                <div class="flex items-start gap-3 sm:gap-4 flex-1">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center flex-shrink-0 text-sm {{ $typeBadge }}">
                        @if($notif->type === 'chat')
                            <i class="bi bi-chat-dots-fill"></i>
                        @elseif($notif->type === 'pembayaran')
                            <i class="bi bi-cash-stack"></i>
                        @elseif($notif->type === 'pesanan')
                            <i class="bi bi-box-seam-fill"></i>
                        @elseif($notif->type === 'rekber')
                            <i class="bi bi-wallet2"></i>
                        @else
                            <i class="bi bi-bell-fill"></i>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            <span class="text-[11px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full border {{ $typeBadge }}">
                                {{ $notif->type }}
                            </span>
                            <span class="text-xs text-gray-400">&bull;</span>
                            <span class="text-xs text-gray-400">{{ $notif->created_at->diffForHumans() }}</span>
                        </div>
                        <h4 class="text-sm font-bold text-gray-900 mb-1 {{ $notif->is_read ? 'font-semibold' : 'font-bold' }}">{{ $notif->title }}</h4>
                        <p class="text-xs text-gray-600 leading-relaxed">{{ $notif->message }}</p>
                    </div>
                </div>

                @if(! $notif->is_read)
                <form action="{{ route('user.notifikasi.read', $notif->id) }}" method="POST" class="flex-shrink-0">
                    @csrf
                    <button type="submit" class="p-1.5 text-gray-400 hover:text-bjbo-primary hover:bg-blue-50 rounded-lg transition" title="Tandai telah dibaca">
                        <i class="bi bi-check2 text-base"></i>
                    </button>
                </form>
                @endif
            </div>
        @empty
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-full bg-gray-100 text-gray-400 mx-auto flex items-center justify-center text-2xl mb-3">
                    <i class="bi bi-bell-slash"></i>
                </div>
                <h3 class="text-base font-bold text-gray-700 mb-1">Belum Ada Notifikasi</h3>
                <p class="text-xs text-gray-500">Aktivitas pesanan, pembayaran, dan pesan chat akan muncul di sini secara real-time.</p>
            </div>
        @endforelse
    </div>

    <!-- Paginasi -->
    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
</main>

<script>
    // Real-time listener via Laravel Echo & Reverb
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof window.Echo !== 'undefined') {
            const userId = {{ Auth::id() }};
            window.Echo.private(`user.${userId}`)
                .listen('.notification.received', (e) => {
                    const alertBox = document.getElementById('liveAlertBox');
                    const alertText = document.getElementById('liveAlertText');
                    const badge = document.getElementById('notifBadge');

                    if (alertBox && alertText) {
                        alertText.innerText = `Notifikasi baru: ${e.title}`;
                        alertBox.classList.remove('hidden');
                    }

                    if (badge) {
                        badge.classList.remove('hidden');
                        let currentCount = parseInt(badge.innerText) || 0;
                        badge.innerText = currentCount + 1;
                    }

                    // Prepend new notification to top
                    const list = document.getElementById('notificationList');
                    if (list) {
                        const newElement = document.createElement('div');
                        newElement.className = 'bg-blue-50/70 p-4 sm:p-5 flex items-start justify-between gap-3 border-b border-bjbo-border animate-fade-in';
                        newElement.innerHTML = `
                            <div class="flex items-start gap-3 sm:gap-4 flex-1">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 text-sm bg-blue-100 text-blue-800">
                                    <i class="bi bi-bell-fill"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <span class="text-[11px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full border bg-blue-100 text-blue-800 border-blue-200">${e.type}</span>
                                    <h4 class="text-sm font-bold text-gray-900 mt-1 mb-1">${e.title}</h4>
                                    <p class="text-xs text-gray-600 leading-relaxed">${e.message}</p>
                                </div>
                            </div>
                        `;
                        list.prepend(newElement);
                    }
                });
        }
    });
</script>

@include('components.footer')
</body>
</html>
