<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends BaseApiController
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $notifications = $this->notificationService->getUserNotifications($user->id, 20);
        $unreadCount = $this->notificationService->getUnreadCount($user->id);

        return $this->successResponse([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ], 'Daftar notifikasi berhasil dimuat.');
    }

    public function markAsRead(Request $request, int $id): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $success = $this->notificationService->markAsRead($id, $user->id);

        if (! $success) {
            return $this->errorResponse('Notifikasi tidak ditemukan.', 404);
        }

        return $this->successResponse(null, 'Notifikasi telah ditandai sebagai dibaca.');
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $this->notificationService->markAllAsRead($user->id);

        return $this->successResponse(null, 'Seluruh notifikasi telah ditandai sebagai dibaca.');
    }
}
