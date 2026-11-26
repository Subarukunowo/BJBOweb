<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\User;
use App\Services\RecommendationEngineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends BaseApiController
{
    public function __construct(
        protected RecommendationEngineService $recommendationEngine
    ) {}

    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $wishlists = $this->recommendationEngine->getUserWishlist($user->id);

        return $this->successResponse($wishlists, 'Daftar wishlist berhasil dimuat.');
    }

    public function toggle(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'postingan_id' => 'required|integer|exists:postingans,id',
        ]);

        $res = $this->recommendationEngine->toggleWishlist($user->id, (int) $validated['postingan_id']);

        return $this->successResponse($res, $res['message']);
    }
}
