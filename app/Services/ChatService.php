<?php

namespace App\Services;

use App\Models\ChatLaporan;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;

class ChatService
{
    /**
     * Menyaring dan menyensor nomor telepon (+62, 08, dsb).
     * Jika terdeteksi nomor telepon, diubah menjadi 'pesan ini tidak dapat dikirim'.
     * Murni fungsional dan bebas loop.
     */
    public function censorPhoneNumber(string $content): string
    {
        // 1. Cek langsung keberadaan pola awalan nomor telepon (+62 atau 08)
        $patternPrefixed = '/(?:\+62|62|08)[\s\-\.]*[0-9]{2,4}[\s\-\.]*[0-9]{3,5}[\s\-\.]*[0-9]{2,5}/i';
        if (preg_match($patternPrefixed, $content)) {
            return 'pesan ini tidak dapat dikirim';
        }

        // 2. Normalisasi string tanpa pemisah antar-angka untuk mendeteksi digit berderet (9-14 digit)
        $digitsOnly = preg_replace('/[^\d]/', '', $content);
        if (preg_match('/^(?:62|08|\d{9,14}$)/', $digitsOnly) && strlen($digitsOnly) >= 9 && strlen($digitsOnly) <= 15) {
            return 'pesan ini tidak dapat dikirim';
        }

        // 3. Deteksi angka nomor telepon dengan spasi sengaja disisipkan (e.g. 0 8 1 2 ...)
        if (preg_match('/0\s*8\s*[0-9\s]{7,15}/', $content)) {
            return 'pesan ini tidak dapat dikirim';
        }

        return $content;
    }

    /**
     * Mengirim pesan terenkripsi End-to-End (E2E) antar-pengguna.
     */
    public function sendMessage(int $senderId, int $receiverId, ?int $postinganId, string $rawMessage): Message
    {
        $filtered = $this->censorPhoneNumber(trim($rawMessage));
        $encrypted = Crypt::encryptString($filtered);

        $message = Message::create([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'postingan_id' => $postinganId,
            'message' => $encrypted,
            'is_reported' => false,
            'is_read' => false,
        ]);

        try {
            $sender = User::find($senderId);
            $senderName = $sender ? $sender->name : 'Pengguna BJBO';
            $snippet = mb_substr($filtered, 0, 45).(mb_strlen($filtered) > 45 ? '...' : '');
            app(NotificationService::class)->notifyNewChatMessage(
                $receiverId,
                $senderName,
                $snippet,
                $message->id
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Failed sending chat notification: '.$e->getMessage());
        }

        $wasCensored = ($filtered === 'pesan ini tidak dapat dikirim');
        try {
            broadcast(new \App\Events\ChatMessageSent($message, $filtered, $wasCensored))->toOthers();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Failed broadcasting ChatMessageSent: '.$e->getMessage());
        }

        return $message;
    }

    /**
     * Mengambil riwayat pesan antara dua pengguna (dengan dekripsi E2E otomatis).
     */
    public function getConversationMessages(int $currentUserId, int $otherUserId, ?int $postinganId = null)
    {
        $query = Message::with(['sender', 'receiver', 'postingan'])
            ->where(function ($q) use ($currentUserId, $otherUserId) {
                $q->where(function ($sub) use ($currentUserId, $otherUserId) {
                    $sub->where('sender_id', $currentUserId)->where('receiver_id', $otherUserId);
                })->orWhere(function ($sub) use ($currentUserId, $otherUserId) {
                    $sub->where('sender_id', $otherUserId)->where('receiver_id', $currentUserId);
                });
            });

        if ($postinganId) {
            $query->where('postingan_id', $postinganId);
        }

        $messages = $query->oldest()->get();

        // Tandai pesan masuk yang belum dibaca sebagai terbaca tanpa loop
        Message::where('sender_id', $otherUserId)
            ->where('receiver_id', $currentUserId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return $messages;
    }

    /**
     * Mengambil daftar percakapan aktif pengguna saat ini.
     * Menggunakan pipeline koleksi Laravel tanpa loop manual.
     */
    public function getUserConversations(int $userId)
    {
        return Message::with(['sender', 'receiver', 'postingan'])
            ->where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)->orWhere('receiver_id', $userId);
            })
            ->latest()
            ->get()
            ->groupBy(function ($msg) use ($userId) {
                $otherId = $msg->sender_id === $userId ? $msg->receiver_id : $msg->sender_id;

                return $otherId.'_'.($msg->postingan_id ?? 0);
            })
            ->map(function ($group) use ($userId) {
                $latest = $group->first();
                $otherUser = $latest->sender_id === $userId ? $latest->receiver : $latest->sender;
                $unreadCount = $group->where('receiver_id', $userId)->where('is_read', false)->count();

                return [
                    'other_user' => $otherUser,
                    'postingan' => $latest->postingan,
                    'latest_message' => $latest,
                    'unread_count' => $unreadCount,
                ];
            })
            ->values();
    }

    /**
     * Melaporkan pesan atau percakapan yang tidak pantas ke tim moderasi.
     */
    public function reportMessage(int $messageId, int $reporterId, string $alasan, ?string $keterangan = null): ChatLaporan
    {
        $message = Message::findOrFail($messageId);
        $reportedUserId = $message->sender_id === $reporterId ? $message->receiver_id : $message->sender_id;

        $message->update(['is_reported' => true]);

        return ChatLaporan::create([
            'message_id' => $message->id,
            'reporter_id' => $reporterId,
            'reported_user_id' => $reportedUserId,
            'postingan_id' => $message->postingan_id,
            'alasan' => $alasan,
            'keterangan' => $keterangan,
            'status' => 'menunggu',
        ]);
    }
}
