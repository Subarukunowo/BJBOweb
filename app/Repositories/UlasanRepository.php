<?php

namespace App\Repositories;

use App\Models\Ulasan;
use Illuminate\Database\Eloquent\Collection;

class UlasanRepository
{
    public function getAllWithRelations(): Collection
    {
        return Ulasan::with(['postingan', 'user'])->latest()->get();
    }

    public function create(array $data): Ulasan
    {
        return Ulasan::create($data);
    }
}
