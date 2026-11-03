<?php

namespace Database\Factories;

use App\Models\Laporan;
use App\Models\Postingan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Laporan>
 */
class LaporanFactory extends Factory
{
    protected $model = Laporan::class;

    public function definition(): array
    {
        return [
            'jenis_laporan' => 'Penipuan',
            'teks_laporan' => fake()->sentence(),
            'user_id' => User::factory(),
            'postingan_id' => Postingan::factory(),
            'status' => 'pending',
        ];
    }
}
