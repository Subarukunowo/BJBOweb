<?php

namespace Tests\Unit\Services;

use App\Models\Postingan;
use App\Models\User;
use App\Repositories\PostinganRepository;
use App\Services\PostinganService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostinganServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PostinganService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PostinganService(new PostinganRepository);
    }

    public function test_get_approved_postings(): void
    {
        Postingan::factory()->create(['status' => 'disetujui']);
        Postingan::factory()->create(['status' => 'belum disetujui']);

        $approved = $this->service->getApprovedPostings();

        $this->assertCount(1, $approved);
        $this->assertEquals('disetujui', $approved->first()->status);
    }

    public function test_get_user_postings(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Postingan::factory()->count(2)->create(['user_id' => $user1->id]);
        Postingan::factory()->create(['user_id' => $user2->id]);

        $userPostings = $this->service->getUserPostings($user1->id);
        $this->assertCount(2, $userPostings);
    }

    public function test_get_postingan_detail_increments_views(): void
    {
        $postingan = Postingan::factory()->create([
            'status' => 'disetujui',
            'views_count' => 0,
            'category' => 'Elektronik',
        ]);

        $detail = $this->service->getPostinganDetail($postingan->id);

        $this->assertEquals($postingan->id, $detail['postingan']->id);
        $this->assertEquals(1, $detail['postingan']->fresh()->views_count);
    }

    public function test_search_and_category_postings(): void
    {
        Postingan::factory()->create([
            'name' => 'Kamera Canon Murah',
            'status' => 'disetujui',
            'category' => 'Elektronik',
        ]);
        Postingan::factory()->create([
            'name' => 'Jaket Jeans',
            'status' => 'disetujui',
            'category' => 'Fashion',
        ]);

        $searchResults = $this->service->searchPostings('Canon');
        $this->assertCount(1, $searchResults);

        $categoryResults = $this->service->getPostingsByCategory('Fashion');
        $this->assertCount(1, $categoryResults);
    }

    public function test_seller_metrics(): void
    {
        $user = User::factory()->create();
        Postingan::factory()->create([
            'user_id' => $user->id,
            'status' => 'disetujui',
            'is_sold' => false,
            'price' => 50000,
        ]);
        Postingan::factory()->create([
            'user_id' => $user->id,
            'status' => 'disetujui',
            'is_sold' => true,
            'price' => 30000,
        ]);
        Postingan::factory()->create([
            'user_id' => $user->id,
            'status' => 'belum disetujui',
            'is_sold' => false,
            'price' => 20000,
        ]);

        $metrics = $this->service->getSellerMetrics($user->id);

        $this->assertEquals(3, $metrics['total_products']);
        $this->assertEquals(1, $metrics['active_products']);
        $this->assertEquals(1, $metrics['pending_products']);
        $this->assertEquals(1, $metrics['sold_products']);
        $this->assertEquals(70000, $metrics['total_catalog_value']);
    }

    public function test_toggle_sold_status(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $postingan = Postingan::factory()->create([
            'user_id' => $user->id,
            'is_sold' => false,
        ]);

        // Wrong user cannot toggle
        $this->assertFalse($this->service->toggleSoldStatus($postingan->id, $otherUser->id));

        // Owner can toggle
        $this->assertTrue($this->service->toggleSoldStatus($postingan->id, $user->id));
        $this->assertTrue($postingan->fresh()->is_sold);
    }

    public function test_create_update_and_delete_postingan(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('barang.jpg');

        $postingan = $this->service->createPostingan([
            'name' => 'Meja Belajar',
            'price' => 120000,
            'category' => 'Furnitur',
            'description' => 'Bagus dan kokoh',
            'lokasi' => 'Bandung',
        ], $file, $user->id, 'seller1');

        $this->assertInstanceOf(Postingan::class, $postingan);
        $this->assertEquals('Meja Belajar', $postingan->name);
        Storage::disk('public')->assertExists($postingan->image);

        // Update
        $newFile = UploadedFile::fake()->image('baru.jpg');
        $updated = $this->service->updatePostingan($postingan->id, [
            'name' => 'Meja Belajar Kayu',
            'price' => 130000,
            'category' => 'Furnitur',
            'description' => 'Bagus banget',
            'lokasi' => 'Cimahi',
            'is_sold' => false,
        ], $newFile);

        $this->assertTrue($updated);
        $this->assertEquals('Meja Belajar Kayu', $postingan->fresh()->name);

        // Delete
        $imagePath = $postingan->fresh()->image;
        $deleted = $this->service->deletePostingan($postingan->id);
        $this->assertTrue($deleted);
        Storage::disk('public')->assertMissing($imagePath);
    }
}
