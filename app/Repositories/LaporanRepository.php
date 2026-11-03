<?php

namespace App\Repositories;

use App\Models\Laporan;
use Illuminate\Database\Eloquent\Collection;

class LaporanRepository
{
    public function getAll(): Collection
    {
        return Laporan::with(['user', 'postingan'])->latest()->get();
    }

    public function create(array $data): Laporan
    {
        return Laporan::create($data);
    }
}
