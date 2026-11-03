<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat dengan {{ $otherUser->name }} - BJBO Sedagho</title>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Vite + Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bjbo-bg text-gray-900 font-sans min-h-screen flex flex-col">
@include('sweetalert::alert')

<!-- Komponen Navbar Pengguna -->
@include('components.navbar-user')

<main class="container mx-auto px-3 sm:px-6 py-4 sm:py-6 flex-grow pb-24 lg:pb-8 max-w-4xl">
    <!-- Chat Card Container -->
    <div class="bg-white border border-bjbo-border rounded-2xl shadow-sm overflow-hidden flex flex-col h-[78vh] min-h-[520px]">
        
        <!-- Header Chat Room -->
        <div class="p-3.5 sm:p-4 bg-white border-b border-bjbo-border flex items-center justify-between gap-3 shrink-0">
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('user.chat.index') }}" class="w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center text-gray-500 transition shrink-0" title="Kembali ke Kotak Masuk">
                    <i class="bi bi-arrow-left text-lg"></i>
                </a>
                <div class="relative shrink-0">
                    <img src="{{ $otherUser->avatar_url }}" alt="{{ $otherUser->name }}" class="w-10 h-10 rounded-full object-cover border border-bjbo-border">
                    <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 rounded-full ring-2 ring-white"></span>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h2 class="text-sm font-bold text-gray-900 truncate">{{ $otherUser->name }}</h2>
                        <span class="hidden sm:inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-bjbo-primary border border-blue-200">
                            Warga Terverifikasi
                        </span>
                    </div>
                    <p class="text-[11px] text-gray-400 font-mono">Sedagho Chat</p>
                </div>
            </div>

            <!-- E2E Security Badge -->
            <div class="flex items-center gap-2 shrink-0">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs" title="Semua pesan dienkripsi dengan protokol End-to-End (E2E)">
                    <i class="bi bi-lock-fill text-xs"></i>
                    <span class="hidden md:inline">E2E Encrypted</span>
                </span>
            </div>
        </div>

        <!-- Product Preview Header (jika obrolan terkait produk tertentu) -->
        @if($postingan)
            <div class="bg-bjbo-surface-subtle px-4 py-2.5 border-b border-bjbo-border flex items-center justify-between gap-3 shrink-0">
                <div class="flex items-center gap-3 min-w-0">
                    <img src="{{ $postingan->image_url }}" alt="{{ $postingan->name }}" class="w-10 h-10 rounded-lg object-cover border border-bjbo-border shrink-0">
                    <div class="min-w-0">
                        <a href="{{ route('user.detail.postingan', $postingan->id) }}" class="text-xs font-bold text-gray-900 hover:text-bjbo-primary transition truncate block" target="_blank">
                            {{ $postingan->name }}
                        </a>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-xs font-mono font-bold text-bjbo-accent">{{ $postingan->formatted_price }}</span>
                            <span class="text-[10px] px-1.5 py-0.2 rounded font-medium {{ $postingan->is_sold ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-800' }}">
                                {{ $postingan->is_sold ? 'Stok Habis / Terjual' : 'Stok Tersedia' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="shrink-0 flex items-center gap-2">
                    <a href="{{ route('user.detail.postingan', $postingan->id) }}" class="text-xs font-semibold text-bjbo-primary hover:underline hidden sm:inline-block">
                        Lihat Produk &rarr;
                    </a>
                </div>
            </div>
        @endif

        <!-- Banner Larangan Nomor Telepon -->
        <div class="bg-amber-50/80 px-4 py-1.5 border-b border-amber-200/60 text-[11px] text-amber-800 flex items-center justify-between gap-2 shrink-0">
            <span class="inline-flex items-center gap-1.5 truncate">
                <i class="bi bi-shield-exclamation text-amber-600"></i>
                <span><strong>Perhatian:</strong> Dilarang mengirimkan nomor telepon (+62 / 08). Pesan yang memuat nomor telepon otomatis disensor demi keamanan transaksi Anda.</span>
            </span>
        </div>

        <!-- Area Pesan (Scrollable) -->
        <div id="messages-container" class="flex-1 overflow-y-auto p-4 space-y-3.5 bg-gray-50/50">
            @if($messages->isEmpty())
                <div class="text-center py-10 max-w-xs mx-auto">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-xs text-bjbo-primary border border-bjbo-border">
                        <i class="bi bi-chat-heart text-2xl"></i>
                    </div>
                    <p class="text-xs text-gray-500 mb-3">Belum ada pesan. Mulai tanyakan ketersediaan stok produk ini kepada penjual:</p>
                    <button type="button" onclick="insertQuickMessage('Halo, apakah stok barang ini masih ada?')" class="bjbo-btn-accent px-4 py-2 text-xs rounded-full inline-flex items-center gap-1.5">
                        <i class="bi bi-box-seam"></i> "Halo, apakah stok barang ini masih ada?"
                    </button>
                </div>
            @endif

            @foreach($messages as $msg)
                @php
                    $isMe = ($msg->sender_id === auth()->id());
                    $isCensored = ($msg->decrypted_message === 'pesan ini tidak dapat dikirim');
                @endphp
                <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }} group items-end gap-1.5">
                    @if(! $isMe)
                        <img src="{{ $otherUser->avatar_url }}" alt="{{ $otherUser->name }}" class="w-6 h-6 rounded-full object-cover shrink-0 mb-1">
                    @endif

                    <div class="max-w-[80%] sm:max-w-md relative">
                        <div class="px-3.5 py-2.5 rounded-2xl text-xs sm:text-sm shadow-2xs leading-relaxed {{ $isMe ? 'bg-bjbo-primary text-white rounded-br-xs' : 'bg-white text-gray-900 border border-bjbo-border rounded-bl-xs' }} {{ $isCensored ? 'bg-red-50 text-red-700 border-red-300' : '' }}">
                            @if($isCensored)
                                <div class="flex items-center gap-1.5 font-medium {{ $isMe ? 'text-red-200' : 'text-red-600' }}">
                                    <i class="bi bi-slash-circle-fill"></i>
                                    <span>Pesan ini tidak dapat dikirim</span>
                                </div>
                                <p class="text-[10px] mt-0.5 opacity-80">
                                    Konten disensor karena terdeteksi memuat nomor telepon.
                                </p>
                            @else
                                <p class="whitespace-pre-wrap break-words">{{ $msg->decrypted_message }}</p>
                            @endif

                            <div class="flex items-center justify-end gap-1 mt-1 text-[10px] {{ $isMe ? 'text-blue-200' : 'text-gray-400' }} font-mono">
                                <span>{{ $msg->created_at->format('H:i') }}</span>
                                @if($isMe)
                                    <i class="bi {{ $msg->is_read ? 'bi-check2-all text-sky-300' : 'bi-check2' }}"></i>
                                @endif
                            </div>
                        </div>

                        <!-- Tombol Laporkan Pesan -->
                        @if(! $isMe)
                            <button type="button"
                                    onclick="openReportModal({{ $msg->id }}, '{{ addslashes(Str::limit($msg->decrypted_message, 40)) }}')"
                                    class="opacity-0 group-hover:opacity-100 transition absolute -right-6 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-600 p-1 text-xs"
                                    title="Laporkan pesan tidak pantas">
                                <i class="bi bi-flag-fill"></i>
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Quick Chips Tanyakan Stok -->
        <div class="px-3 py-1.5 bg-white border-t border-bjbo-border flex items-center gap-1.5 overflow-x-auto text-xs shrink-0 no-scrollbar">
            <span class="text-[11px] font-semibold text-gray-400 shrink-0 mr-1">Pertanyaan Cepat:</span>
            <button type="button" onclick="insertQuickMessage('Halo, apakah stok barang ini masih ada?')" class="px-2.5 py-1 rounded-full bg-bjbo-surface-subtle hover:bg-gray-200 text-gray-700 text-[11px] font-medium shrink-0 transition">
                📦 Stok masih ada?
            </button>
            <button type="button" onclick="insertQuickMessage('Halo, apakah harganya masih bisa nego?')" class="px-2.5 py-1 rounded-full bg-bjbo-surface-subtle hover:bg-gray-200 text-gray-700 text-[11px] font-medium shrink-0 transition">
                🤝 Bisa nego?
            </button>
            <button type="button" onclick="insertQuickMessage('Bisa COD di daerah mana mas?')" class="px-2.5 py-1 rounded-full bg-bjbo-surface-subtle hover:bg-gray-200 text-gray-700 text-[11px] font-medium shrink-0 transition">
                📍 Lokasi COD
            </button>
        </div>

        <!-- Input Bar Form Chat -->
        <div class="p-3 bg-white border-t border-bjbo-border shrink-0">
            <!-- Peringatan live jika mengetik nomor HP -->
            <div id="phone-warning" class="hidden mb-2 p-2 rounded-lg bg-red-50 border border-red-200 text-xs text-red-700 flex items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill text-red-500"></i>
                <span>Terdeteksi format nomor telepon (+62 / 08). Pesan akan otomatis diubah menjadi <strong>"pesan ini tidak dapat dikirim"</strong>.</span>
            </div>

            <form id="chat-form" action="{{ route('user.chat.send') }}" method="POST" class="flex items-center gap-2">
                @csrf
                <input type="hidden" name="receiver_id" value="{{ $otherUser->id }}">
                <input type="hidden" name="postingan_id" value="{{ $postingan ? $postingan->id : '' }}">

                <div class="flex-1 relative">
                    <input type="text"
                           id="chat-input"
                           name="message"
                           value="{{ $messages->isEmpty() ? $defaultMessage : '' }}"
                           placeholder="Ketik pesan untuk menanyakan stok atau negosiasi..."
                           maxlength="1000"
                           autocomplete="off"
                           class="w-full pl-3.5 pr-10 py-2.5 text-xs sm:text-sm bg-bjbo-surface-subtle border border-bjbo-border-strong rounded-xl focus:border-bjbo-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-bjbo-primary/15 transition"
                           required>
                </div>

                <button type="submit" class="bjbo-btn-primary px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold shrink-0 flex items-center gap-1.5">
                    <span>Kirim</span>
                    <i class="bi bi-send-fill text-xs"></i>
                </button>
            </form>
        </div>
    </div>
</main>

<!-- Modal Laporkan Pesan Tidak Pantas -->
<div id="report-modal" class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white border border-bjbo-border rounded-2xl max-w-md w-full p-5 sm:p-6 shadow-xl animate-fade-in">
        <div class="flex items-center justify-between pb-3 border-b border-bjbo-border mb-4">
            <h3 class="text-sm sm:text-base font-bold text-gray-900 flex items-center gap-2">
                <i class="bi bi-flag-fill text-red-600"></i> Laporkan Pesan Tidak Pantas
            </h3>
            <button type="button" onclick="closeReportModal()" class="text-gray-400 hover:text-gray-600 text-lg">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form id="report-form" action="" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Cuplikan Pesan yang Dilaporkan</label>
                <div id="reported-snippet" class="p-2.5 bg-gray-50 border border-gray-200 rounded-lg text-xs font-mono text-gray-600 italic"></div>
            </div>

            <div>
                <label for="alasan" class="block text-xs font-semibold text-gray-700 mb-1.5">Kategori Pelanggaran</label>
                <select id="alasan" name="alasan" class="w-full text-xs sm:text-sm p-2.5 bg-white border border-bjbo-border-strong rounded-lg focus:border-bjbo-primary focus:outline-none" required>
                    <option value="">Pilih Alasan Laporan...</option>
                    <option value="Kata-kata Kasar & Pelecehan">Kata-kata Kasar & Pelecehan</option>
                    <option value="Indikasi Penipuan Transaksi di Luar Platform">Indikasi Penipuan Transaksi di Luar Platform</option>
                    <option value="Menawarkan Barang Ilegal atau Terlarang">Menawarkan Barang Ilegal atau Terlarang</option>
                    <option value="Spam atau Pelecehan Berulang">Spam atau Gangguan Berulang</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>

            <div>
                <label for="keterangan" class="block text-xs font-semibold text-gray-700 mb-1.5">Keterangan Tambahan (Opsional)</label>
                <textarea id="keterangan" name="keterangan" rows="3" class="w-full text-xs p-2.5 bg-white border border-bjbo-border-strong rounded-lg focus:border-bjbo-primary focus:outline-none" placeholder="Berikan rincian singkat mengenai pelanggaran ini..."></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeReportModal()" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-lg transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold bg-red-600 text-white hover:bg-red-700 rounded-lg transition shadow-xs">
                    Kirim Laporan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Komponen Mobile Bottom Nav -->
@include('components.mobile-bottom-nav')

<!-- Komponen Footer -->
@include('components.footer')

<script>
    function scrollToBottom() {
        const container = document.getElementById('messages-container');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }

    function insertQuickMessage(text) {
        const input = document.getElementById('chat-input');
        if (input) {
            input.value = text;
            input.focus();
            checkPhoneInInput(text);
        }
    }

    // Deteksi nomor telepon di sisi klien (+62 / 08 / digit berderet)
    function checkPhoneInInput(val) {
        const warning = document.getElementById('phone-warning');
        const phoneRegex = /(?:\+62|62|08)[\s\-\.]*[0-9]{2,4}[\s\-\.]*[0-9]{3,5}[\s\-\.]*[0-9]{2,5}|0\s*8\s*[0-9\s]{7,14}/i;
        const digits = val.replace(/[^\d]/g, '');

        if (phoneRegex.test(val) || (digits.length >= 10 && (digits.startsWith('08') || digits.startsWith('62')))) {
            warning.classList.remove('hidden');
        } else {
            warning.classList.add('hidden');
        }
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function appendSentMessage(text, time, isCensored) {
        const container = document.getElementById('messages-container');
        if (!container) return;

        const emptyState = container.querySelector('.text-center.py-10');
        if (emptyState) emptyState.remove();

        const bubbleDiv = document.createElement('div');
        bubbleDiv.className = 'flex justify-end group items-end gap-1.5 transition-all duration-200 transform translate-y-1 opacity-0';

        const bgClass = isCensored ? 'bg-red-50 text-red-700 border-red-300' : 'bg-bjbo-primary text-white rounded-br-xs';
        const innerContent = isCensored
            ? `<div class="flex items-center gap-1.5 font-medium text-red-200"><i class="bi bi-slash-circle-fill"></i><span>Pesan ini tidak dapat dikirim</span></div><p class="text-[10px] mt-0.5 opacity-80">Konten disensor karena terdeteksi memuat nomor telepon.</p>`
            : `<p class="whitespace-pre-wrap break-words">${escapeHtml(text)}</p>`;

        bubbleDiv.innerHTML = `
            <div class="max-w-[80%] sm:max-w-md relative">
                <div class="px-3.5 py-2.5 rounded-2xl text-xs sm:text-sm shadow-2xs leading-relaxed ${bgClass}">
                    ${innerContent}
                    <div class="flex items-center justify-end gap-1 mt-1 text-[10px] text-blue-200 font-mono">
                        <span>${time || 'Baru saja'}</span>
                        <i class="bi bi-check2"></i>
                    </div>
                </div>
            </div>
        `;

        container.appendChild(bubbleDiv);
        requestAnimationFrame(() => {
            bubbleDiv.classList.remove('translate-y-1', 'opacity-0');
            scrollToBottom();
        });
    }

    function appendIncomingMessage(data) {
        const container = document.getElementById('messages-container');
        if (!container) return;

        const emptyState = container.querySelector('.text-center.py-10');
        if (emptyState) emptyState.remove();

        const bubbleDiv = document.createElement('div');
        bubbleDiv.className = 'flex justify-start group items-end gap-1.5 transition-all duration-200 transform translate-y-1 opacity-0';

        const isCensored = data.was_censored;
        const bgClass = isCensored ? 'bg-red-50 text-red-700 border-red-300' : 'bg-white text-gray-900 border border-bjbo-border rounded-bl-xs';
        const innerContent = isCensored
            ? `<div class="flex items-center gap-1.5 font-medium text-red-600"><i class="bi bi-slash-circle-fill"></i><span>Pesan ini tidak dapat dikirim</span></div><p class="text-[10px] mt-0.5 opacity-80">Konten disensor karena terdeteksi memuat nomor telepon.</p>`
            : `<p class="whitespace-pre-wrap break-words">${escapeHtml(data.decrypted_message)}</p>`;

        bubbleDiv.innerHTML = `
            <img src="{{ $otherUser->avatar_url }}" alt="{{ $otherUser->name }}" class="w-6 h-6 rounded-full object-cover shrink-0 mb-1">
            <div class="max-w-[80%] sm:max-w-md relative">
                <div class="px-3.5 py-2.5 rounded-2xl text-xs sm:text-sm shadow-2xs leading-relaxed ${bgClass}">
                    ${innerContent}
                    <div class="flex items-center justify-end gap-1 mt-1 text-[10px] text-gray-400 font-mono">
                        <span>${data.created_at || 'Baru saja'}</span>
                    </div>
                </div>
                <button type="button"
                        onclick="openReportModal(${data.id || 0}, '${escapeHtml(data.decrypted_message.substring(0, 40))}')"
                        class="opacity-0 group-hover:opacity-100 transition absolute -right-6 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-600 p-1 text-xs"
                        title="Laporkan pesan tidak pantas">
                    <i class="bi bi-flag-fill"></i>
                </button>
            </div>
        `;

        container.appendChild(bubbleDiv);
        requestAnimationFrame(() => {
            bubbleDiv.classList.remove('translate-y-1', 'opacity-0');
            scrollToBottom();
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        scrollToBottom();

        const input = document.getElementById('chat-input');
        if (input) {
            input.addEventListener('input', function () {
                checkPhoneInInput(this.value);
            });
            checkPhoneInInput(input.value);
        }

        const chatForm = document.getElementById('chat-form');
        if (chatForm) {
            chatForm.addEventListener('submit', async function (e) {
                e.preventDefault();
                const chatInput = document.getElementById('chat-input');
                const messageText = chatInput ? chatInput.value.trim() : '';
                if (!messageText) return;

                const submitBtn = chatForm.querySelector('button[type="submit"]');
                if (submitBtn) submitBtn.disabled = true;

                const formData = new FormData(chatForm);
                if (chatInput) chatInput.value = '';

                try {
                    const response = await fetch(chatForm.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });

                    const data = await response.json();
                    if (data.success) {
                        appendSentMessage(data.decrypted_message, data.created_at, data.was_censored);
                    } else {
                        alert(data.message || 'Gagal mengirim pesan.');
                    }
                } catch (err) {
                    console.error('Chat send error:', err);
                    alert('Koneksi terputus saat mengirim pesan.');
                } finally {
                    if (submitBtn) submitBtn.disabled = false;
                    if (chatInput) chatInput.focus();
                }
            });
        }

        // Real-Time Listener via Laravel Echo & Reverb
        if (window.Echo) {
            const minId = Math.min({{ auth()->id() }}, {{ $otherUser->id }});
            const maxId = Math.max({{ auth()->id() }}, {{ $otherUser->id }});
            window.Echo.channel(`chat-room.${minId}.${maxId}`)
                .listen('.chat.sent', (event) => {
                    if (event.sender_id === {{ $otherUser->id }}) {
                        appendIncomingMessage(event);
                    }
                });
        }
    });

    // Modal Laporan Pesan
    function openReportModal(messageId, snippet) {
        const modal = document.getElementById('report-modal');
        const form = document.getElementById('report-form');
        const snippetEl = document.getElementById('reported-snippet');

        form.action = `/user/chat/report/${messageId}`;
        snippetEl.textContent = `"${snippet}"`;
        modal.classList.remove('hidden');
    }

    function closeReportModal() {
        const modal = document.getElementById('report-modal');
        modal.classList.add('hidden');
    }
</script>

</body>
</html>
