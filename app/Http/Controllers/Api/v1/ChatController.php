<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\User;
use App\Services\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends BaseApiController
{
    public function __construct(
        protected ChatService $chatService
    ) {}

    public function conversations(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $conversations = $this->chatService->getUserConversations($user->id);

        return $this->successResponse($conversations, 'Daftar percakapan berhasil dimuat.');
    }

    public function messages(Request $request, int $otherUserId): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $postinganId = $request->query('postingan_id') ? (int) $request->query('postingan_id') : null;

        $messages = $this->chatService->getConversationMessages($user->id, $otherUserId, $postinganId);
        $otherUser = User::select(['id', 'name', 'profile_picture'])->find($otherUserId);

        return $this->successResponse([
            'other_user' => $otherUser,
            'messages' => $messages,
        ], 'Riwayat pesan berhasil dimuat.');
    }

    public function send(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'receiver_id' => 'required|integer|exists:users,id',
            'postingan_id' => 'nullable|integer|exists:postingans,id',
            'message' => 'required|string|max:1000',
        ]);

        if ($user->id === (int) $validated['receiver_id']) {
            return $this->errorResponse('Anda tidak dapat mengirim pesan ke diri sendiri.', 422);
        }

        $message = $this->chatService->sendMessage(
            $user->id,
            (int) $validated['receiver_id'],
            $validated['postingan_id'] ?? null,
            $validated['message']
        );

        return $this->successResponse($message, 'Pesan berhasil dikirim.', 201);
    }

    public function report(Request $request, int $messageId): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'alasan' => 'required|string|max:100',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $report = $this->chatService->reportMessage(
            $messageId,
            $user->id,
            $validated['alasan'],
            $validated['keterangan'] ?? null
        );

        return $this->successResponse($report, 'Laporan pesan telah dikirim ke tim moderasi.', 201);
    }
}
