<?php

namespace Database\Factories;

use App\Models\Postingan;
use App\Models\Ulasan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ulasan>
 */
class UlasanFactory extends Factory
{
    protected $model = Ulasan::class;

    public function definition(): array
    {
        return [
            'postingan_id' => Postingan::factory(),
            'user_id' => User::factory(),
            'rating' => 5,
            'komentar' => fake()->sentence(),
        ];
    }
}
