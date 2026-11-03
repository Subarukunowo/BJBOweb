<?php

namespace Database\Factories;

use App\Models\Postingan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Postingan>
 */
class PostinganFactory extends Factory
{
    protected $model = Postingan::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'price' => fake()->numberBetween(10000, 500000),
            'tipe_penawaran' => 'jual',
            'category' => 'Elektronik',
            'kondisi' => 'Bekas',
            'description' => fake()->paragraph(),
            'image' => 'sample.jpg',
            'lokasi' => 'Bandung',
            'user_id' => User::factory(),
            'username' => fake()->userName(),
            'status' => 'belum disetujui',
            'is_sold' => false,
            'views_count' => 0,
        ];
    }
}
