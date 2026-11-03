<?php

namespace Database\Factories;

use App\Models\Favorit;
use App\Models\Postingan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Favorit>
 */
class FavoritFactory extends Factory
{
    protected $model = Favorit::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'postingan_id' => Postingan::factory(),
        ];
    }
}
