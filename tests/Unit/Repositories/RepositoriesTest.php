<?php

namespace Tests\Unit\Repositories;

use App\Models\Postingan;
use App\Models\User;
use App\Repositories\FavoritRepository;
use App\Repositories\LaporanRepository;
use App\Repositories\PesananRepository;
use App\Repositories\PostinganRepository;
use App\Repositories\UlasanRepository;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RepositoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_repository_crud(): void
    {
        $repo = new UserRepository;

        $user = $repo->create([
            'name' => 'Repo User',
            'email' => 'repouser@example.com',
            'password' => bcrypt('secret'),
        ]);

        $this->assertEquals('Repo User', $user->name);
        $this->assertCount(1, $repo->all());

        $found = $repo->findById($user->id);
        $this->assertEquals($user->id, $found->id);

        $repo->setBlockStatus($user->id, true);
        $this->assertTrue($user->fresh()->is_blocked);

        $repo->update($user->id, ['name' => 'Updated User']);
        $this->assertEquals('Updated User', $user->fresh()->name);

        $deleted = $repo->delete($user->id);
        $this->assertTrue($deleted);
        $this->assertCount(0, $repo->all());
    }

    public function test_postingan_repository_crud(): void
    {
        $user = User::factory()->create();
        $repo = new PostinganRepository;

        $postingan = $repo->create([
            'name' => 'Laptop Gaming',
            'price' => 15000000,
            'category' => 'Elektronik',
            'description' => 'Mulus no minus',
            'image' => 'laptop.jpg',
            'lokasi' => 'Bandung',
            'user_id' => $user->id,
            'status' => 'disetujui',
        ]);

        $this->assertEquals('Laptop Gaming', $postingan->name);
        $this->assertCount(1, $repo->getApproved());
        $this->assertCount(1, $repo->getByUser($user->id));

        $found = $repo->findById($postingan->id);
        $this->assertEquals($postingan->id, $found->id);

        $searched = $repo->search('Laptop');
        $this->assertCount(1, $searched);

        $byCategory = $repo->getByCategory('Elektronik');
        $this->assertCount(1, $byCategory);

        $related = $repo->getRelated('Elektronik', $postingan->id);
        $this->assertCount(0, $related);

        $repo->update($postingan->id, ['price' => 14000000]);
        $this->assertEquals(14000000, $postingan->fresh()->price);

        $repo->delete($postingan->id);
        $this->assertCount(0, $repo->getApproved());
    }

    public function test_favorit_repository_toggle_and_check(): void
    {
        $user = User::factory()->create();
        $postingan = Postingan::factory()->create(['status' => 'disetujui']);
        $repo = new FavoritRepository;

        $this->assertFalse($repo->isFavorited($user->id, $postingan->id));

        // Toggle add
        $this->assertTrue($repo->toggle($user->id, $postingan->id));
        $this->assertTrue($repo->isFavorited($user->id, $postingan->id));
        $this->assertCount(1, $repo->getFavoritedPostings($user->id));

        // Toggle remove
        $this->assertFalse($repo->toggle($user->id, $postingan->id));
        $this->assertFalse($repo->isFavorited($user->id, $postingan->id));
    }

    public function test_pesanan_repository_operations(): void
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();
        $postingan = Postingan::factory()->create(['user_id' => $seller->id]);
        $repo = new PesananRepository;

        $pesanan = $repo->create([
            'order_number' => 'ORD-TEST-99',
            'user_id' => $buyer->id,
            'penjual_id' => $seller->id,
            'postingan_id' => $postingan->id,
            'total_price' => 45000,
            'status' => 'pending',
        ]);

        $this->assertEquals('ORD-TEST-99', $pesanan->order_number);

        $byId = $repo->findById($pesanan->id);
        $this->assertNotNull($byId);
        $this->assertEquals($pesanan->id, $byId->id);

        $byOrderNumber = $repo->findByOrderNumber('ORD-TEST-99');
        $this->assertNotNull($byOrderNumber);
        $this->assertEquals($pesanan->id, $byOrderNumber->id);

        $userOrders = $repo->getOrdersByUserId($buyer->id);
        $this->assertCount(1, $userOrders);

        $updated = $repo->updateStatus($pesanan->id, 'paid', 'qris', 'TX-999');
        $this->assertTrue($updated);
        $this->assertEquals('paid', $pesanan->fresh()->status);
        $this->assertNotNull($pesanan->fresh()->settlement_time);

        // Non-existent id
        $this->assertFalse($repo->updateStatus(999999, 'paid'));
    }

    public function test_laporan_and_ulasan_repositories(): void
    {
        $user = User::factory()->create();
        $postingan = Postingan::factory()->create();

        $laporanRepo = new LaporanRepository;
        $laporan = $laporanRepo->create([
            'jenis_laporan' => 'Spam',
            'teks_laporan' => 'Teks spam',
            'user_id' => $user->id,
            'postingan_id' => $postingan->id,
        ]);
        $this->assertEquals('Spam', $laporan->jenis_laporan);
        $this->assertCount(1, $laporanRepo->getAll());

        $ulasanRepo = new UlasanRepository;
        $ulasan = $ulasanRepo->create([
            'user_id' => $user->id,
            'postingan_id' => $postingan->id,
            'rating' => 5,
            'komentar' => 'Sangat memuaskan',
        ]);
        $this->assertEquals(5, $ulasan->rating);
        $this->assertCount(1, $ulasanRepo->getAllWithRelations());
    }
}
