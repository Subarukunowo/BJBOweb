<?php

namespace App\Services;

use App\Models\Laporan;
use App\Repositories\LaporanRepository;
use Illuminate\Database\Eloquent\Collection;

class LaporanService
{
    protected LaporanRepository $laporanRepository;

    public function __construct(LaporanRepository $laporanRepository)
    {
        $this->laporanRepository = $laporanRepository;
    }

    public function getAllLaporan(): Collection
    {
        return $this->laporanRepository->getAll();
    }

    public function submitLaporan(array $data, int $userId): Laporan
    {
        return $this->laporanRepository->create([
            'jenis_laporan' => $data['jenis_laporan'],
            'teks_laporan' => $data['teks'],
            'user_id' => $userId,
            'postingan_id' => $data['postingan_id'],
            'status' => 'Pending',
        ]);
    }
}
