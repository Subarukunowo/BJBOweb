<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Postingan;
use App\Models\Ulasan;
use App\Models\User;
use App\Services\UlasanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends BaseApiController
{
    public function __construct(
        protected UlasanService $ulasanService
    ) {}

    public function index(int $postinganId): JsonResponse
    {
        $reviews = Ulasan::with('user:id,name,profile_picture')
            ->where('postingan_id', $postinganId)
            ->latest('id')
            ->get();

        $averageRating = (float) Ulasan::where('postingan_id', $postinganId)->avg('rating');

        return $this->successResponse([
            'reviews' => $reviews,
            'average_rating' => round($averageRating, 1),
            'total_reviews' => $reviews->count(),
        ], 'Ulasan produk berhasil dimuat.');
    }

    public function store(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'postingan_id' => 'required|integer|exists:postingans,id',
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'required|string|max:1000',
        ]);

        $postingan = Postingan::find($validated['postingan_id']);

        if ($postingan && $postingan->user_id === $user->id) {
            return $this->errorResponse('Anda tidak dapat mengulas produk milik sendiri.', 422);
        }

        $review = $this->ulasanService->addUlasan($validated, $user->id);

        return $this->successResponse($review->load('user:id,name,profile_picture'), 'Ulasan berhasil dikirim.', 201);
    }
}
