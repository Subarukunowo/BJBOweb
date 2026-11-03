<?php

namespace Database\Factories;

use App\Models\Pesanan;
use App\Models\Postingan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pesanan>
 */
class PesananFactory extends Factory
{
    protected $model = Pesanan::class;

    public function definition(): array
    {
        return [
            'order_number' => 'ORD-'.strtoupper(fake()->bothify('????####')),
            'user_id' => User::factory(),
            'postingan_id' => Postingan::factory(),
            'penjual_id' => User::factory(),
            'total_price' => 50000,
            'catatan' => 'Catatan pesanan',
            'status' => 'pending',
            'snap_token' => 'dummy-snap-token',
            'payment_type' => 'qris',
        ];
    }
}
