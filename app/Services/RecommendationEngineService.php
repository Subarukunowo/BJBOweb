<?php

namespace App\Services;

use App\Models\Favorit;
use App\Models\Postingan;
use App\Models\UserProductView;
use App\Models\UserSearchLog;
use App\Models\Wishlist;
use Illuminate\Support\Collection;

class RecommendationEngineService
{
    /**
     * Weights for the Personalization Scoring Function:
     * S(p, u) = (W_wishlist * Score_wishlist) + (W_search * Score_search) + (W_view * Score_view)
     */
    private const WEIGHT_WISHLIST = 5;

    private const WEIGHT_SEARCH = 3;

    private const WEIGHT_VIEW = 2;

    /**
     * Record a user search query.
     */
    public function logSearch(?int $userId, string $query, ?string $category = null, ?string $ip = null): void
    {
        $cleanQuery = trim($query);
        if (empty($cleanQuery)) {
            return;
        }

        UserSearchLog::create([
            'user_id' => $userId,
            'query' => mb_strtolower($cleanQuery),
            'category' => $category,
            'ip_address' => $ip,
        ]);
    }

    /**
     * Record or increment a product detail view by user.
     */
    public function logProductView(?int $userId, int $postinganId): void
    {
        if (! $userId) {
            return;
        }

        $view = UserProductView::firstOrNew([
            'user_id' => $userId,
            'postingan_id' => $postinganId,
        ]);

        $view->view_count = ($view->view_count ?? 0) + 1;
        $view->last_viewed_at = now();
        $view->save();
    }

    /**
     * Toggle item in user's wishlist.
     */
    public function toggleWishlist(int $userId, int $postinganId): array
    {
        $existing = Wishlist::where('user_id', $userId)->where('postingan_id', $postinganId)->first();

        if ($existing) {
            $existing->delete();

            return ['is_wishlisted' => false, 'message' => 'Dihapus dari wishlist'];
        }

        Wishlist::create([
            'user_id' => $userId,
            'postingan_id' => $postinganId,
        ]);

        return ['is_wishlisted' => true, 'message' => 'Ditambahkan ke wishlist'];
    }

    /**
     * Get user's wishlist items with optimized SQL.
     */
    public function getUserWishlist(int $userId): Collection
    {
        return Wishlist::with(['postingan' => function ($q) {
            $q->select(['id', 'name', 'price', 'category', 'image', 'lokasi', 'user_id', 'status', 'is_sold', 'created_at'])
                ->with(['user:id,name']);
        }])
            ->where('user_id', $userId)
            ->latest('id')
            ->get()
            ->pluck('postingan')
            ->filter();
    }

    /**
     * Recommendation Engine: "For You" (for->$users).
     * Computes multi-factor interest vector without using any procedural loops.
     */
    public function getRecommendationsForUser(?int $userId, int $limit = 8): Collection
    {
        $baseCandidates = Postingan::query()
            ->select(['id', 'name', 'price', 'category', 'image', 'lokasi', 'user_id', 'status', 'is_sold', 'created_at'])
            ->with(['user:id,name'])
            ->where('status', 'disetujui')
            ->where('is_auto_blocked', false)
            ->where('is_sold', false);

        if (! $userId) {
            return $baseCandidates->latest('id')->take($limit)->get();
        }

        // Eksklusif produk dari penjual lain (penjual tidak direkomendasikan membeli barang sendiri)
        $baseCandidates->where('user_id', '!=', $userId);

        // 1. Ambil preferensi pencarian pengguna (Query & Kategori)
        $searchTerms = UserSearchLog::where('user_id', $userId)
            ->latest('id')
            ->take(10)
            ->pluck('query')
            ->map(fn ($q) => mb_strtolower($q))
            ->unique();

        $searchCategories = UserSearchLog::where('user_id', $userId)
            ->whereNotNull('category')
            ->latest('id')
            ->take(10)
            ->pluck('category')
            ->unique();

        // 2. Ambil kategori dari produk yang sering dilihat (Views)
        $viewedCategories = UserProductView::with(['postingan:id,category'])
            ->where('user_id', $userId)
            ->latest('last_viewed_at')
            ->take(15)
            ->get()
            ->pluck('postingan.category')
            ->filter()
            ->unique();

        // 3. Ambil kategori dari wishlist & favorit pengguna
        $wishlistCategories = Wishlist::with(['postingan:id,category'])
            ->where('user_id', $userId)
            ->latest('id')
            ->take(20)
            ->get()
            ->pluck('postingan.category')
            ->filter();

        $favoritCategories = Favorit::with(['postingan:id,category'])
            ->where('user_id', $userId)
            ->latest('id')
            ->take(20)
            ->get()
            ->pluck('postingan.category')
            ->filter();

        $highIntentCategories = $wishlistCategories->merge($favoritCategories)->unique();

        // Jika pengguna baru belum punya aktivitas, berikan produk terbaru dan terpopuler
        if ($searchTerms->isEmpty() && $viewedCategories->isEmpty() && $highIntentCategories->isEmpty()) {
            return $baseCandidates->latest('id')->take($limit)->get();
        }

        // Skor kandidat postingan secara fungsional (bebas loop)
        $candidates = $baseCandidates->latest('id')->take(60)->get();

        return $candidates->map(function ($product) use ($searchTerms, $searchCategories, $viewedCategories, $highIntentCategories) {
            $score = 0;
            $productCategory = $product->category;
            $productName = mb_strtolower($product->name);

            // Factor 1: Wishlist & Favorit Kategori Match (Weight: 5)
            if ($highIntentCategories->contains($productCategory)) {
                $score += self::WEIGHT_WISHLIST;
            }

            // Factor 2: Search Query & Category Match (Weight: 3)
            $matchesSearchTerm = $searchTerms->contains(fn ($term) => str_contains($productName, $term));
            if ($matchesSearchTerm || $searchCategories->contains($productCategory)) {
                $score += self::WEIGHT_SEARCH;
            }

            // Factor 3: Product View History Match (Weight: 2)
            if ($viewedCategories->contains($productCategory)) {
                $score += self::WEIGHT_VIEW;
            }

            $product->affinity_score = $score;

            return $product;
        })
            ->sortByDesc('affinity_score')
            ->take($limit)
            ->values();
    }
}
