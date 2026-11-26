<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Postingan;
use App\Models\User;
use App\Services\PostinganService;
use App\Services\RecommendationEngineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends BaseApiController
{
    public function __construct(
        protected PostinganService $postinganService,
        protected RecommendationEngineService $recommendationService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $keyword = trim((string) $request->query('q', ''));
        $category = trim((string) $request->query('category', ''));
        $sort = $request->query('sort', 'terbaru');
        $perPage = min(40, max(1, (int) $request->query('per_page', 20)));
        $page = max(1, (int) $request->query('page', 1));

        $cacheKey = 'api_v1_products_'.md5("{$keyword}|{$category}|{$sort}|{$perPage}|{$page}");
        $products = \Illuminate\Support\Facades\Cache::remember($cacheKey, 45, function () use ($keyword, $category, $sort, $perPage) {
            $query = Postingan::query()
                ->select(['id', 'name', 'price', 'tipe_penawaran', 'category', 'kondisi', 'description', 'image', 'lokasi', 'user_id', 'status', 'is_sold', 'views_count', 'created_at'])
                ->with(['user:id,name,profile_picture'])
                ->where('status', 'disetujui')
                ->where('is_auto_blocked', false);

            if ($keyword !== '') {
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                        ->orWhere('description', 'like', "%{$keyword}%");
                });
            }

            if ($category !== '' && $category !== 'Semua') {
                $query->where('category', $category);
            }

            match ($sort) {
                'termurah' => $query->orderBy('price', 'asc'),
                'termahal' => $query->orderBy('price', 'desc'),
                default => $query->orderBy('id', 'desc'),
            };

            return $query->paginate($perPage);
        });

        return $this->successResponse($products, 'Daftar produk berhasil dimuat.');
    }

    public function categories(): JsonResponse
    {
        $categories = [
            'Elektronik',
            'Fashion',
            'Otomotif',
            'Perabot',
            'Hobi & Olahraga',
            'Buku & Alat Tulis',
            'Lainnya',
        ];

        $counts = Postingan::where('status', 'disetujui')
            ->where('is_auto_blocked', false)
            ->where('is_sold', false)
            ->selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        $data = array_map(fn ($cat) => [
            'name' => $cat,
            'total_items' => $counts->get($cat, 0),
        ], $categories);

        return $this->successResponse($data, 'Kategori produk berhasil dimuat.');
    }

    public function show(int $id): JsonResponse
    {
        $postingan = Postingan::with([
            'user:id,name,profile_picture,created_at',
            'ulasans' => fn ($q) => $q->with('user:id,name,profile_picture')->latest(),
        ])->find($id);

        if (! $postingan) {
            return $this->errorResponse('Produk tidak ditemukan.', 404);
        }

        $postingan->increment('views_count');

        $related = Postingan::where('category', $postingan->category)
            ->where('id', '!=', $postingan->id)
            ->where('status', 'disetujui')
            ->where('is_auto_blocked', false)
            ->latest()
            ->take(6)
            ->get();

        return $this->successResponse([
            'product' => $postingan,
            'related' => $related,
        ], 'Detail produk berhasil diambil.');
    }

    public function recommendations(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();
        $userId = $user ? $user->id : 0;

        $items = $this->recommendationService->getRecommendationsForUser($userId, 12);

        return $this->successResponse($items, 'Rekomendasi produk berhasil dimuat.');
    }

    public function store(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:1000',
            'category' => 'required|string',
            'description' => 'required|string',
            'lokasi' => 'required|string',
            'kondisi' => 'nullable|string|in:Baru / Segel,Bekas (Pre-loved)',
            'tipe_penawaran' => 'nullable|string|in:Bisa Nego,Harga Pas',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $postingan = $this->postinganService->createPostingan(
            $validated,
            $request->file('image'),
            $user->id,
            $user->name
        );

        $message = $postingan->is_auto_blocked
            ? 'Produk terdeteksi melanggar ketentuan dan diblokir otomatis.'
            : 'Produk berhasil diunggah dan aktif di etalase.';

        return $this->successResponse($postingan, $message, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $postingan = Postingan::find($id);

        if (! $postingan) {
            return $this->errorResponse('Produk tidak ditemukan.', 404);
        }

        if ($postingan->user_id !== $user->id) {
            return $this->errorResponse('Anda tidak memiliki wewenang untuk mengubah produk ini.', 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:1000',
            'category' => 'required|string',
            'description' => 'required|string',
            'lokasi' => 'required|string',
            'kondisi' => 'nullable|string',
            'tipe_penawaran' => 'nullable|string',
            'is_sold' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $success = $this->postinganService->updatePostingan($id, $validated, $request->file('image'));

        if (! $success) {
            return $this->errorResponse('Gagal memperbarui produk.', 500);
        }

        return $this->successResponse($postingan->fresh(), 'Produk berhasil diperbarui.');
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $postingan = Postingan::find($id);

        if (! $postingan) {
            return $this->errorResponse('Produk tidak ditemukan.', 404);
        }

        if ($postingan->user_id !== $user->id) {
            return $this->errorResponse('Anda tidak berhak menghapus produk ini.', 403);
        }

        $this->postinganService->deletePostingan($id);

        return $this->successResponse(null, 'Produk berhasil dihapus.');
    }

    public function toggleSold(Request $request, int $id): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $success = $this->postinganService->toggleSoldStatus($id, $user->id);

        if (! $success) {
            return $this->errorResponse('Anda tidak berhak mengubah status ketersediaan produk ini.', 403);
        }

        $postingan = Postingan::find($id);

        return $this->successResponse([
            'id' => $id,
            'is_sold' => (bool) $postingan->is_sold,
        ], 'Status ketersediaan berhasil diubah.');
    }

    public function sellerMetrics(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $metrics = $this->postinganService->getSellerMetrics($user->id);

        return $this->successResponse($metrics, 'Metrik toko berhasil diambil.');
    }

    public function sellerProducts(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $products = $this->postinganService->getUserPostings($user->id);

        return $this->successResponse($products, 'Daftar produk penjual berhasil diambil.');
    }
}
