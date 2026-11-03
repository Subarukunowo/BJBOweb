<?php

namespace App\Services;

use App\Models\Postingan;
use App\Repositories\PostinganRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PostinganService
{
    protected PostinganRepository $postinganRepository;

    protected ContentModerationService $moderationService;

    public function __construct(
        PostinganRepository $postinganRepository,
        ?ContentModerationService $moderationService = null
    ) {
        $this->postinganRepository = $postinganRepository;
        $this->moderationService = $moderationService ?? app(ContentModerationService::class);
    }

    public function getApprovedPostings(): Collection
    {
        return $this->postinganRepository->getApproved();
    }

    public function getUserPostings(int $userId): Collection
    {
        return $this->postinganRepository->getByUser($userId);
    }

    public function getPostinganDetail(int $id): array
    {
        $postingan = $this->postinganRepository->findById($id);
        $postingan->increment('views_count');

        $related = $this->postinganRepository->getRelated($postingan->category, $postingan->id, 4);

        return [
            'postingan' => $postingan,
            'related' => $related,
        ];
    }

    public function searchPostings(string $keyword): Collection
    {
        return $this->postinganRepository->search($keyword);
    }

    public function getPostingsByCategory(string $category): Collection
    {
        return $this->postinganRepository->getByCategory($category);
    }

    public function getSellerMetrics(int $userId): array
    {
        $postings = $this->postinganRepository->getByUser($userId);

        $totalProducts = $postings->count();
        $activeProducts = $postings->where('status', 'disetujui')->where('is_auto_blocked', false)->where('is_sold', false)->count();
        $blockedProducts = $postings->where('is_auto_blocked', true)->count();
        $pendingProducts = $postings->where('status', '!=', 'disetujui')->where('is_auto_blocked', false)->count();
        $soldProducts = $postings->where('is_sold', true)->count();
        $totalCatalogValue = (int) $postings->where('is_auto_blocked', false)->where('is_sold', false)->sum('price');

        return [
            'total_products' => $totalProducts,
            'active_products' => $activeProducts,
            'blocked_products' => $blockedProducts,
            'pending_products' => $pendingProducts,
            'sold_products' => $soldProducts,
            'total_catalog_value' => $totalCatalogValue,
        ];
    }

    public function toggleSoldStatus(int $id, int $userId): bool
    {
        $postingan = $this->postinganRepository->findById($id);
        if ($postingan->user_id !== $userId) {
            return false;
        }

        $newStatus = ! $postingan->is_sold;

        return $this->postinganRepository->update($id, ['is_sold' => $newStatus]);
    }

    /**
     * Membuat postingan baru dengan auto-block cerdas untuk konten terlarang.
     * Postingan bersih langsung disetujui tanpa harus menunggu ACC admin.
     */
    public function createPostingan(array $data, ?UploadedFile $image, int $userId, string $username): Postingan
    {
        $imagePath = null;
        if ($image) {
            $imagePath = $image->store('postingan', 'public');
        }

        // Jalankan inspeksi moderasi konten terlarang
        $moderation = $this->moderationService->inspect(
            $data['name'],
            $data['category'] ?? '',
            $data['description']
        );

        $isAutoBlocked = $moderation['is_blocked'];
        $blockReason = $moderation['reason'];
        $status = $isAutoBlocked ? 'belum disetujui' : 'disetujui';

        return $this->postinganRepository->create([
            'name' => $data['name'],
            'price' => $data['price'],
            'tipe_penawaran' => $data['tipe_penawaran'] ?? 'Bisa Nego',
            'category' => $data['category'],
            'kondisi' => $data['kondisi'] ?? 'Bekas (Pre-loved)',
            'description' => $data['description'],
            'image' => $imagePath,
            'lokasi' => $data['lokasi'],
            'user_id' => $userId,
            'username' => $username,
            'status' => $status,
            'is_auto_blocked' => $isAutoBlocked,
            'block_reason' => $blockReason,
            'is_sold' => false,
            'views_count' => 0,
        ]);
    }

    public function updatePostingan(int $id, array $data, ?UploadedFile $newImage): bool
    {
        $postingan = $this->postinganRepository->findById($id);
        $imagePath = $postingan->image;

        if ($newImage) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $newImage->store('postingan', 'public');
        }

        // Inspeksi ulang konten saat diedit
        $moderation = $this->moderationService->inspect(
            $data['name'],
            $data['category'] ?? $postingan->category,
            $data['description']
        );

        $isAutoBlocked = $moderation['is_blocked'];
        $blockReason = $moderation['reason'];
        $status = $isAutoBlocked ? 'belum disetujui' : ($postingan->status === 'disetujui' ? 'disetujui' : 'disetujui');

        $payload = [
            'name' => $data['name'],
            'price' => $data['price'],
            'category' => $data['category'],
            'description' => $data['description'],
            'image' => $imagePath,
            'lokasi' => $data['lokasi'],
            'is_auto_blocked' => $isAutoBlocked,
            'block_reason' => $blockReason,
            'status' => $status,
        ];

        if (isset($data['kondisi'])) {
            $payload['kondisi'] = $data['kondisi'];
        }
        if (isset($data['tipe_penawaran'])) {
            $payload['tipe_penawaran'] = $data['tipe_penawaran'];
        }
        if (isset($data['is_sold'])) {
            $payload['is_sold'] = (bool) $data['is_sold'];
        }

        return $this->postinganRepository->update($id, $payload);
    }

    public function deletePostingan(int $id): bool
    {
        $postingan = $this->postinganRepository->findById($id);

        if ($postingan->image && Storage::disk('public')->exists($postingan->image)) {
            Storage::disk('public')->delete($postingan->image);
        }

        return $this->postinganRepository->delete($id);
    }
}
