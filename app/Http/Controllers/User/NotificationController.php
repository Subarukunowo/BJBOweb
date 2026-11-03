<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request): View
    {
        $userId = (int) auth()->id();
        $notifications = $this->notificationService->getUserNotifications($userId, 15);
        $unreadCount = $this->notificationService->getUnreadCount($userId);

        return view('pages.user.notifikasi.index', compact('notifications', 'unreadCount'));
    }

    public function markAsRead(int $id): JsonResponse|RedirectResponse
    {
        $userId = (int) auth()->id();
        $this->notificationService->markAsRead($id, $userId);

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notifikasi ditandai telah dibaca.');
    }

    public function markAllAsRead(): JsonResponse|RedirectResponse
    {
        $userId = (int) auth()->id();
        $this->notificationService->markAllAsRead($userId);

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }

    public function unreadCount(): JsonResponse
    {
        $userId = (int) auth()->id();
        $count = $this->notificationService->getUnreadCount($userId);

        return response()->json(['unread_count' => $count]);
    }
}
