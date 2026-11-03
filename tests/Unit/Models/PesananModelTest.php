<?php

namespace Tests\Unit\Models;

use App\Models\Pesanan;
use App\Models\Postingan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PesananModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_pesanan_relationships(): void
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();
        $postingan = Postingan::factory()->create(['user_id' => $seller->id]);

        $pesanan = Pesanan::factory()->create([
            'user_id' => $buyer->id,
            'penjual_id' => $seller->id,
            'postingan_id' => $postingan->id,
            'total_price' => 150000,
            'status' => 'pending',
        ]);

        $this->assertEquals($buyer->id, $pesanan->pembeli->id);
        $this->assertEquals($buyer->id, $pesanan->user->id);
        $this->assertEquals($seller->id, $pesanan->penjual->id);
        $this->assertEquals($postingan->id, $pesanan->postingan->id);
        $this->assertIsInt($pesanan->total_price);
        $this->assertEquals(150000, $pesanan->total_price);
    }
}
