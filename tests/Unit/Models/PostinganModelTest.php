<?php

namespace Tests\Unit\Models;

use App\Models\Favorit;
use App\Models\Laporan;
use App\Models\Pesanan;
use App\Models\Postingan;
use App\Models\Ulasan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostinganModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_postingan_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $postingan = Postingan::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $postingan->user);
        $this->assertEquals($user->id, $postingan->user->id);
    }

    public function test_postingan_has_many_ulasan(): void
    {
        $postingan = Postingan::factory()->create();
        $ulasan = Ulasan::factory()->create(['postingan_id' => $postingan->id, 'rating' => 5]);

        $this->assertTrue($postingan->ulasan->contains($ulasan));
        $this->assertEquals(5.0, $postingan->rating_score);
    }

    public function test_postingan_has_many_laporan(): void
    {
        $postingan = Postingan::factory()->create();
        $laporan = Laporan::factory()->create(['postingan_id' => $postingan->id]);

        $this->assertTrue($postingan->laporan->contains($laporan));
    }

    public function test_postingan_has_many_favorits(): void
    {
        $user = User::factory()->create();
        $postingan = Postingan::factory()->create();
        Favorit::factory()->create(['user_id' => $user->id, 'postingan_id' => $postingan->id]);

        $this->assertTrue($postingan->isFavoritedBy($user));
        $this->assertFalse($postingan->isFavoritedBy(null));
    }

    public function test_postingan_has_many_pesanans(): void
    {
        $postingan = Postingan::factory()->create();
        $pesanan = Pesanan::factory()->create(['postingan_id' => $postingan->id]);

        $this->assertTrue($postingan->pesanans->contains($pesanan));
    }

    public function test_postingan_attributes_and_helpers(): void
    {
        $postingan = Postingan::factory()->create([
            'price' => 75000,
            'image' => 'https://example.com/test.jpg',
        ]);

        $this->assertEquals('Rp75.000', $postingan->formatted_price);
        $this->assertEquals('https://example.com/test.jpg', $postingan->image_url);

        $noImagePost = new Postingan(['image' => '']);
        $this->assertStringContainsString('bjbo.png', $noImagePost->image_url);
    }
}
