<?php

namespace App\Services;

use App\Repositories\FavoritRepository;
use Illuminate\Database\Eloquent\Collection;

class FavoritService
{
    protected FavoritRepository $favoritRepository;

    public function __construct(FavoritRepository $favoritRepository)
    {
        $this->favoritRepository = $favoritRepository;
    }

    public function toggleFavorite(int $userId, int $postinganId): array
    {
        $isFavorited = $this->favoritRepository->toggle($userId, $postinganId);
        $message = $isFavorited ? 'Barang berhasil ditambahkan ke daftar favorit.' : 'Barang dihapus dari daftar favorit.';

        return [
            'favorited' => $isFavorited,
            'message' => $message,
        ];
    }

    public function getUserFavorites(int $userId): Collection
    {
        return $this->favoritRepository->getFavoritedPostings($userId);
    }
}
