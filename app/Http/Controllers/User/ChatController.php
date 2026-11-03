<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Postingan;
use App\Models\User;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class ChatController extends Controller
{
    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Menampilkan daftar kotak masuk (inbox) seluruh percakapan pengguna.
     */
    public function index()
    {
        $conversations = $this->chatService->getUserConversations(Auth::id());

        return view('pages.user.chat.index', compact('conversations'));
    }

    /**
     * Membuka ruang obrolan (chat room) dengan penjual/pembeli terkait produk tertentu.
     */
    public function room(int $otherUserId, ?int $postinganId = null)
    {
        $currentUserId = Auth::id();
        if ($currentUserId === $otherUserId) {
            Alert::info('Info', 'Anda tidak dapat mengirim pesan ke akun Anda sendiri.');

            return redirect()->route('user.chat.index');
        }

        $otherUser = User::findOrFail($otherUserId);
        $postingan = $postinganId ? Postingan::find($postinganId) : null;
        $messages = $this->chatService->getConversationMessages($currentUserId, $otherUserId, $postinganId);

        // Pesan default menanyakan ketersediaan stok produk
        $defaultMessage = $postingan ? 'Halo, apakah stok barang ini masih ada?' : '';

        return view('pages.user.chat.room', compact('otherUser', 'postingan', 'messages', 'defaultMessage'));
    }

    /**
     * Memulai chat langsung dari halaman detail produk untuk menanyakan stok barang.
     */
    public function startFromProduct(int $postinganId)
    {
        $postingan = Postingan::findOrFail($postinganId);
        $sellerId = $postingan->user_id;

        if (Auth::id() === $sellerId) {
            Alert::info('Info', 'Ini adalah produk dagangan milik Anda sendiri.');

            return redirect()->route('user.detail.postingan', $postinganId);
        }

        return redirect()->route('user.chat.room', ['otherUserId' => $sellerId, 'postinganId' => $postinganId]);
    }

    /**
     * Mengirim pesan baru dengan proteksi E2E dan sensor nomor telepon.
     */
    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'postingan_id' => 'nullable|exists:postingans,id',
            'message' => 'required|string|max:1500',
        ]);

        $senderId = Auth::id();
        $receiverId = (int) $request->input('receiver_id');
        $postinganId = $request->input('postingan_id') ? (int) $request->input('postingan_id') : null;
        $rawMessage = (string) $request->input('message');

        $message = $this->chatService->sendMessage($senderId, $receiverId, $postinganId, $rawMessage);

        $wasCensored = ($message->decrypted_message === 'pesan ini tidak dapat dikirim');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message_id' => $message->id,
                'decrypted_message' => $message->decrypted_message,
                'was_censored' => $wasCensored,
                'created_at' => $message->created_at->format('H:i'),
            ]);
        }

        if ($wasCensored) {
            Alert::warning(
                'Pesan Disensor!',
                'Pengiriman nomor telepon (+62 / 08) dilarang demi keamanan transaksi di BJBO. Pesan telah disensor menjadi: "pesan ini tidak dapat dikirim".'
            );
        }

        return redirect()->route('user.chat.room', ['otherUserId' => $receiverId, 'postinganId' => $postinganId]);
    }

    /**
     * Melaporkan pesan yang tidak pantas (pelecehan, spam, konten terlarang, dsb).
     */
    public function report(Request $request, int $messageId)
    {
        $request->validate([
            'alasan' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        try {
            $this->chatService->reportMessage(
                $messageId,
                Auth::id(),
                (string) $request->input('alasan'),
                (string) $request->input('keterangan')
            );

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Laporan berhasil dikirim ke administrator BJBO untuk ditindaklanjuti.',
                ]);
            }

            Alert::success('Laporan Terkirim!', 'Pesan telah dilaporkan ke administrator untuk peninjauan.');

            return redirect()->back();
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            Alert::error('Gagal!', 'Terjadi kesalahan saat melaporkan pesan.');

            return redirect()->back();
        }
    }
}
