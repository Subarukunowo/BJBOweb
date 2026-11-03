<?php

namespace App\Repositories;

use App\Models\Favorit;
use App\Models\Postingan;
use Illuminate\Database\Eloquent\Collection;

class FavoritRepository
{
    /**
     * Toggle favorite status for a posting.
     */
    public function toggle(int $userId, int $postinganId): bool
    {
        $existing = Favorit::where('user_id', $userId)
            ->where('postingan_id', $postinganId)
            ->first();

        if ($existing) {
            $existing->delete();

            return false;
        }

        Favorit::create([
            'user_id' => $userId,
            'postingan_id' => $postinganId,
        ]);

        return true;
    }

    /**
     * Check if a posting is favorited by user.
     */
    public function isFavorited(int $userId, int $postinganId): bool
    {
        return Favorit::where('user_id', $userId)
            ->where('postingan_id', $postinganId)
            ->exists();
    }

    /**
     * Get all postings favorited by user.
     */
    public function getFavoritedPostings(int $userId): Collection
    {
        return Postingan::whereHas('favorits', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with('user')->latest()->get();
    }
}
