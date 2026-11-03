<?php

namespace Tests\Unit\Services;

use App\Models\Pesanan;
use App\Models\Postingan;
use App\Models\User;
use App\Repositories\FavoritRepository;
use App\Repositories\LaporanRepository;
use App\Repositories\PesananRepository;
use App\Repositories\UlasanRepository;
use App\Repositories\UserRepository;
use App\Services\FavoritService;
use App\Services\LaporanService;
use App\Services\MidtransService;
use App\Services\PesananService;
use App\Services\UlasanService;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OtherServicesTest extends TestCase
{
    use RefreshDatabase;

    public function test_pesanan_service_create_and_manage_order(): void
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();
        $postingan = Postingan::factory()->create([
            'user_id' => $seller->id,
            'price' => 50000,
            'status' => 'disetujui',
        ]);

        $pesananRepo = new PesananRepository;
        $midtransService = new MidtransService;
        $pesananService = new PesananService($pesananRepo, $midtransService);

        $pesanan = $pesananService->createOrder($buyer->id, $postingan->id, 'Tolong dipacking rapi');

        $this->assertInstanceOf(Pesanan::class, $pesanan);
        $this->assertEquals($buyer->id, $pesanan->user_id);
        $this->assertEquals(50000, $pesanan->total_price);
        $this->assertNotEmpty($pesanan->snap_token);

        // Fetch detail
        $found = $pesananService->getOrder($pesanan->id);
        $this->assertNotNull($found);
        $this->assertEquals($pesanan->id, $found->id);

        // User orders
        $orders = $pesananService->getUserOrders($buyer->id);
        $this->assertCount(1, $orders);

        // Mark as paid
        $paid = $pesananService->markOrderAsPaid($pesanan->id, 'qris', 'TRX-12345');
        $this->assertTrue($paid);
        $this->assertEquals('paid', $pesanan->fresh()->status);
        $this->assertEquals('TRX-12345', $pesanan->fresh()->transaction_id);

        // Client key & production check
        $this->assertNotEmpty($pesananService->getMidtransClientKey());
        $this->assertIsBool($pesananService->isProduction());
    }

    public function test_favorit_service_toggle_and_get(): void
    {
        $user = User::factory()->create();
        $postingan = Postingan::factory()->create(['status' => 'disetujui']);

        $service = new FavoritService(new FavoritRepository);

        // Toggle on
        $resultOn = $service->toggleFavorite($user->id, $postingan->id);
        $this->assertTrue($resultOn['favorited']);

        // Check favorites
        $favorites = $service->getUserFavorites($user->id);
        $this->assertCount(1, $favorites);

        // Toggle off
        $resultOff = $service->toggleFavorite($user->id, $postingan->id);
        $this->assertFalse($resultOff['favorited']);
        $this->assertCount(0, $service->getUserFavorites($user->id));
    }

    public function test_laporan_service_submit_and_get(): void
    {
        $user = User::factory()->create();
        $postingan = Postingan::factory()->create();

        $service = new LaporanService(new LaporanRepository);

        $laporan = $service->submitLaporan([
            'jenis_laporan' => 'Penipuan',
            'teks' => 'Barang tidak sesuai',
            'postingan_id' => $postingan->id,
        ], $user->id);

        $this->assertEquals('Penipuan', $laporan->jenis_laporan);
        $this->assertEquals('Barang tidak sesuai', $laporan->teks_laporan);

        $all = $service->getAllLaporan();
        $this->assertCount(1, $all);
    }

    public function test_ulasan_service_add_and_get(): void
    {
        $user = User::factory()->create();
        $postingan = Postingan::factory()->create();

        $service = new UlasanService(new UlasanRepository);

        $ulasan = $service->addUlasan([
            'postingan_id' => $postingan->id,
            'rating' => 5,
            'komentar' => 'Penjual ramah dan cepat',
        ], $user->id);

        $this->assertEquals(5, $ulasan->rating);
        $this->assertEquals('Penjual ramah dan cepat', $ulasan->komentar);

        $all = $service->getAllUlasans();
        $this->assertCount(1, $all);
    }

    public function test_user_service_update_profile_and_remove_avatar(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['name' => 'Old Name']);

        $service = new UserService(new UserRepository);

        $avatar = UploadedFile::fake()->image('avatar.jpg');
        $updatedUser = $service->updateProfile($user->id, [
            'name' => 'New Name',
            'email' => 'new@example.com',
            'password' => 'newpassword123',
        ], $avatar);

        $this->assertEquals('New Name', $updatedUser->name);
        $this->assertEquals('new@example.com', $updatedUser->email);
        $this->assertTrue(Hash::check('newpassword123', $updatedUser->password));
        $this->assertNotNull($updatedUser->profile_picture);
        Storage::disk('public')->assertExists($updatedUser->profile_picture);

        // Remove avatar
        $removed = $service->removeAvatar($user->id);
        $this->assertTrue($removed);
        $this->assertNull($user->fresh()->profile_picture);
    }
}
