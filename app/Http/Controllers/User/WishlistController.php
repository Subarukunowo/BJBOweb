<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\PromoService;
use App\Services\RecommendationEngineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    protected RecommendationEngineService $recommendationEngine;

    protected PromoService $promoService;

    public function __construct(
        RecommendationEngineService $recommendationEngine,
        PromoService $promoService
    ) {
        $this->recommendationEngine = $recommendationEngine;
        $this->promoService = $promoService;
    }

    public function index(): View
    {
        $userId = (int) auth()->id();
        $wishlists = $this->recommendationEngine->getUserWishlist($userId);
        $recommendations = $this->recommendationEngine->getRecommendationsForUser($userId, 4);

        return view('pages.user.wishlist.index', compact('wishlists', 'recommendations'));
    }

    public function toggle(int $postinganId): JsonResponse|RedirectResponse
    {
        $userId = (int) auth()->id();
        $res = $this->recommendationEngine->toggleWishlist($userId, $postinganId);

        if (request()->wantsJson()) {
            return response()->json($res);
        }

        return back()->with('success', $res['message']);
    }

    public function checkPromo(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'total_price' => 'required|numeric|min:0',
        ]);

        $res = $this->promoService->validateAndCalculateDiscount($validated['code'], (int) $validated['total_price']);

        return response()->json($res);
    }
}
