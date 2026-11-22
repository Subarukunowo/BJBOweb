<?php

namespace Tests\Unit\Services;

use App\Models\Pesanan;
use App\Models\Postingan;
use App\Models\User;
use App\Services\CategoryAnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CategoryAnalyticsServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CategoryAnalyticsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CategoryAnalyticsService;
        Cache::flush();
    }

    public function test_get_all_periods_data_returns_all_four_timeframes(): void
    {
        $data = $this->service->getAllPeriodsData();

        $this->assertIsArray($data);
        $this->assertArrayHasKey('1_minggu', $data);
        $this->assertArrayHasKey('1_bulan', $data);
        $this->assertArrayHasKey('1_tahun', $data);
        $this->assertArrayHasKey('all_time', $data);

        $this->assertEquals('1 Minggu Terakhir', $data['1_minggu']['period_label']);
        $this->assertEquals('1 Bulan Terakhir', $data['1_bulan']['period_label']);
        $this->assertEquals('1 Tahun Terakhir', $data['1_tahun']['period_label']);
        $this->assertEquals('Semua Waktu', $data['all_time']['period_label']);
    }

    public function test_get_category_sales_data_calculates_correct_totals_and_percentages(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        $elektronikPost = Postingan::factory()->create([
            'user_id' => $seller->id,
            'category' => 'Elektronik',
            'price' => 1000000,
        ]);

        $fashionPost = Postingan::factory()->create([
            'user_id' => $seller->id,
            'category' => 'Fashion',
            'price' => 500000,
        ]);

        // Create 2 Elektronik orders and 1 Fashion order
        Pesanan::factory()->create([
            'user_id' => $buyer->id,
            'postingan_id' => $elektronikPost->id,
            'penjual_id' => $seller->id,
            'total_price' => 1000000,
            'final_price' => 1000000,
            'status' => 'paid',
            'created_at' => now()->subDays(2),
        ]);

        Pesanan::factory()->create([
            'user_id' => $buyer->id,
            'postingan_id' => $elektronikPost->id,
            'penjual_id' => $seller->id,
            'total_price' => 1000000,
            'final_price' => 1000000,
            'status' => 'paid',
            'created_at' => now()->subDays(4),
        ]);

        Pesanan::factory()->create([
            'user_id' => $buyer->id,
            'postingan_id' => $fashionPost->id,
            'penjual_id' => $seller->id,
            'total_price' => 500000,
            'final_price' => 500000,
            'status' => 'paid',
            'created_at' => now()->subDays(3),
        ]);

        $result = $this->service->getCategorySalesData('1_minggu', now()->subDays(7));

        $this->assertEquals(3, $result['total_sold']);
        $this->assertEquals(2500000, $result['total_revenue']);
        $this->assertEquals('Elektronik', $result['top_category']);
        $this->assertContains('Elektronik', $result['categories']);
        $this->assertContains('Fashion', $result['categories']);

        $elektronikItem = collect($result['items'])->firstWhere('category', 'Elektronik');
        $this->assertNotNull($elektronikItem);
        $this->assertEquals(2, $elektronikItem['total_sold']);
        $this->assertEquals(2000000, $elektronikItem['total_revenue']);
        $this->assertEquals(66.7, $elektronikItem['percentage']);

        $fashionItem = collect($result['items'])->firstWhere('category', 'Fashion');
        $this->assertNotNull($fashionItem);
        $this->assertEquals(1, $fashionItem['total_sold']);
        $this->assertEquals(500000, $fashionItem['total_revenue']);
        $this->assertEquals(33.3, $fashionItem['percentage']);
    }

    public function test_get_category_sales_data_handles_empty_dataset_gracefully(): void
    {
        $result = $this->service->getCategorySalesData('1_minggu', now()->subDays(7));

        $this->assertEquals(0, $result['total_sold']);
        $this->assertEquals(0, $result['total_revenue']);
        $this->assertEquals('Rp 0', $result['formatted_revenue']);
        $this->assertEquals('Belum Ada', $result['top_category']);
        $this->assertEmpty($result['items']);
    }
}
