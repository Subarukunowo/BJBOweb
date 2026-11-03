<?php

namespace App\Services;

use App\Models\Ulasan;
use App\Repositories\UlasanRepository;
use Illuminate\Database\Eloquent\Collection;

class UlasanService
{
    protected UlasanRepository $ulasanRepository;

    public function __construct(UlasanRepository $ulasanRepository)
    {
        $this->ulasanRepository = $ulasanRepository;
    }

    public function getAllUlasans(): Collection
    {
        return $this->ulasanRepository->getAllWithRelations();
    }

    public function addUlasan(array $data, int $userId): Ulasan
    {
        return $this->ulasanRepository->create([
            'postingan_id' => $data['postingan_id'],
            'user_id' => $userId,
            'rating' => $data['rating'],
            'komentar' => $data['komentar'],
        ]);
    }
}
