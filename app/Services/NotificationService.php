<?php

namespace App\Services;

use App\Events\NewNotificationEvent;
use App\Models\AppNotification;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send an application notification and broadcast it via Laravel Reverb.
     * Strictly loop-free implementation.
     */
    public function sendNotification(
        int $userId,
        string $title,
        string $message,
        string $type = 'general',
        array $data = []
    ): AppNotification {
        $notification = AppNotification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'data' => $data,
            'is_read' => false,
        ]);

        try {
            broadcast(new NewNotificationEvent($notification))->toOthers();
        } catch (\Throwable $e) {
            Log::warning('Broadcast notification failed: '.$e->getMessage());
        }

        return $notification;
    }

    /**
     * Notify user when a new chat message is received.
     */
    public function notifyNewChatMessage(
        int $recipientId,
        string $senderName,
        string $messageSnippet,
        int $chatId
    ): AppNotification {
        return $this->sendNotification(
            $recipientId,
            'Pesan Baru dari '.$senderName,
            $messageSnippet,
            'chat',
            ['chat_id' => $chatId, 'sender_name' => $senderName]
        );
    }

    /**
     * Notify seller when payment is confirmed from buyer.
     */
    public function notifyPaymentReceived(
        int $sellerId,
        string $orderNumber,
        int $amount,
        string $buyerName
    ): AppNotification {
        $formattedAmount = 'Rp'.number_format($amount, 0, ',', '.');

        return $this->sendNotification(
            $sellerId,
            'Pembayaran Diterima - Pesanan '.$orderNumber,
            'Pembeli '.$buyerName.' telah menyelesaikan pembayaran sebesar '.$formattedAmount.'. Silakan konfirmasi pesanan Anda.',
            'pembayaran',
            [
                'order_number' => $orderNumber,
                'amount' => $amount,
                'buyer_name' => $buyerName,
            ]
        );
    }

    /**
     * Retrieve paginated notifications avoiding asterisk select and N+1.
     */
    public function getUserNotifications(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return AppNotification::query()
            ->select(['id', 'user_id', 'title', 'message', 'type', 'data', 'is_read', 'created_at'])
            ->where('user_id', $userId)
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * Count unread notifications.
     */
    public function getUnreadCount(int $userId): int
    {
        return AppNotification::query()
            ->where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Mark single notification as read.
     */
    public function markAsRead(int $notificationId, int $userId): bool
    {
        return (bool) AppNotification::query()
            ->where('id', $notificationId)
            ->where('user_id', $userId)
            ->update(['is_read' => true]);
    }

    /**
     * Mark all notifications for a user as read.
     */
    public function markAllAsRead(int $userId): bool
    {
        return (bool) AppNotification::query()
            ->where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }
}
