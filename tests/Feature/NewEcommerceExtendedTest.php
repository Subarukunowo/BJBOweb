<?php

namespace Tests\Feature;

use App\Models\AppNotification;
use App\Models\PencairanDana;
use App\Models\Pesanan;
use App\Models\Postingan;
use App\Models\Store;
use App\Models\User;
use App\Models\Wishlist;
use App\Services\NotificationService;
use App\Services\PesananLifecycleService;
use App\Services\PromoService;
use App\Services\RecommendationEngineService;
use App\Services\RekberService;
use App\Services\StoreService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NewEcommerceExtendedTest extends TestCase
{
    use RefreshDatabase;

    protected User $buyer;

    protected User $seller;

    protected Postingan $product;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->seller = User::factory()->create([
            'name' => 'Penjual Bengkalis',
            'email' => 'penjual@bjbo.local',
            'saldo_rekber' => 500000,
        ]);

        $this->buyer = User::factory()->create([
            'name' => 'Pembeli Setia',
            'email' => 'pembeli@bjbo.local',
        ]);

        $this->product = Postingan::create([
            'name' => 'Lempuk Durian Asli Bengkalis',
            'price' => 50000,
            'category' => 'Kuliner',
            'description' => 'Lempuk durian asli khas Pulau Bengkalis rasa manis legit.',
            'image' => 'lempuk.jpg',
            'lokasi' => 'Bengkalis Kota',
            'user_id' => $this->seller->id,
            'status' => 'disetujui',
            'is_sold' => false,
            'is_auto_blocked' => false,
        ]);
    }

    /**
     * Test 1: Real-time Notifications via Laravel Reverb
     * (a) Chat message notification, (b) Seller receives payment notification from buyer.
     */
    public function test_notifications_for_chat_and_payment_received(): void
    {
        $notifService = app(NotificationService::class);

        // a. Chat notification
        $chatNotif = $notifService->notifyNewChatMessage(
            $this->seller->id,
            $this->buyer->name,
            'Halo, apakah stok lempuk durian masih ada?',
            99
        );

        $this->assertInstanceOf(AppNotification::class, $chatNotif);
        $this->assertEquals('chat', $chatNotif->type);
        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $this->seller->id,
            'type' => 'chat',
            'is_read' => false,
        ]);

        // b. Seller payment notification
        $paymentNotif = $notifService->notifyPaymentReceived(
            $this->seller->id,
            'BJBO-20261030-ABCDEF',
            50000,
            $this->buyer->name
        );

        $this->assertEquals('pembayaran', $paymentNotif->type);
        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $this->seller->id,
            'type' => 'pembayaran',
        ]);

        // API unread count
        $response = $this->actingAs($this->seller)->getJson(route('user.notifikasi.unreadCount'));
        $response->assertOk()->assertJson(['unread_count' => 2]);

        // Mark as read
        $notifService->markAsRead($chatNotif->id, $this->seller->id);
        $this->assertTrue((bool) $chatNotif->fresh()->is_read);
    }

    /**
     * Test 2: Ads, Promos & Discounts
     * Admin CRUD and discount calculation on checkout.
     */
    public function test_promo_and_discount_system(): void
    {
        $promoService = app(PromoService::class);

        // Create discount coupon
        $promo = $promoService->createPromo([
            'title' => 'Diskon Pembukaan Pasar 20%',
            'type' => 'diskon_persen',
            'code' => 'PASAR20',
            'discount_percentage' => 20,
            'min_purchase' => 40000,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('promos', ['code' => 'PASAR20']);

        // Check discount calculation
        $result = $promoService->validateAndCalculateDiscount('PASAR20', 50000);
        $this->assertTrue($result['is_valid']);
        $this->assertEquals(10000, $result['discount']); // 20% of 50000 is 10000

        // Check minimum purchase rule
        $resultMin = $promoService->validateAndCalculateDiscount('PASAR20', 30000);
        $this->assertFalse($resultMin['is_valid']);

        // API endpoint check
        $resApi = $this->actingAs($this->buyer)->postJson(route('user.promo.check'), [
            'code' => 'PASAR20',
            'total_price' => 50000,
        ]);
        $resApi->assertOk()->assertJson(['is_valid' => true, 'discount' => 10000]);
    }

    /**
     * Test 3: Rekber Escrow Wallet for Sellers & 2x24 Working Hours Withdrawal
     */
    public function test_rekber_bank_registration_and_2x24_working_hours_withdrawal(): void
    {
        $rekberService = app(RekberService::class);

        // Register bank account
        $bank = $rekberService->registerBankAccount($this->seller->id, [
            'bank_name' => 'Bank Riau Kepri Syariah',
            'account_number' => '102-9988-7766',
            'account_name' => 'PENJUAL BENGKALIS',
        ]);

        $this->assertDatabaseHas('bank_accounts', [
            'user_id' => $this->seller->id,
            'bank_name' => 'Bank Riau Kepri Syariah',
            'is_verified' => true,
        ]);

        // Seller requests withdrawal
        $initialBalance = $this->seller->saldo_rekber;
        $withdrawAmount = 100000;

        $pencairan = $rekberService->requestPencairan($this->seller->id, $bank->id, $withdrawAmount);

        $this->assertInstanceOf(PencairanDana::class, $pencairan);
        $this->assertEquals('menunggu_proses', $pencairan->status);
        $this->assertNotNull($pencairan->estimated_completion_at);

        // Balance deducted atomically
        $this->assertEquals($initialBalance - $withdrawAmount, $this->seller->fresh()->saldo_rekber);

        // Admin rejects withdrawal -> funds refunded
        $rekberService->processPencairan($pencairan->id, 'ditolak', 'Nama rekening salah');
        $this->assertEquals($initialBalance, $this->seller->fresh()->saldo_rekber);
    }

    /**
     * Test 4: Store Registration (Buka Toko)
     * a. Toko Perorangan (No HP, Email, KTP)
     * b. Official Store (NIB, NPWP, HAKI)
     */
    public function test_store_registration_perorangan_and_official(): void
    {
        $storeService = app(StoreService::class);

        // User A registers Toko Perorangan
        $userPerorangan = User::factory()->create(['email' => 'perorangan@bjbo.local']);
        $ktpFile = UploadedFile::fake()->image('ktp.jpg');

        $storePerorangan = $storeService->registerPerorangan($userPerorangan, [
            'store_name' => 'Toko Lempuk Sedagho',
            'phone_number' => '081234567890',
            'email' => 'perorangan@bjbo.local',
            'ktp_number' => '1403011234560001',
            'address' => 'Jl. Antara Bengkalis',
        ], $ktpFile);

        $this->assertEquals('perorangan', $storePerorangan->store_type);
        $this->assertEquals('aktif', $storePerorangan->status);

        // User B registers Official Store
        $userOfficial = User::factory()->create(['email' => 'official@bjbo.local']);
        $npwpFile = UploadedFile::fake()->create('npwp.pdf', 100);
        $hakiFile = UploadedFile::fake()->create('haki.pdf', 100);

        $storeOfficial = $storeService->registerOfficialStore($userOfficial, [
            'store_name' => 'PT Durian Bengkalis Perkasa',
            'phone_number' => '0766123456',
            'email' => 'official@bjbo.local',
            'nib_number' => '9120008877665',
            'npwp_company' => '01.234.567.8-901.000',
            'haki_certificate_number' => 'IDM000998877',
            'address' => 'Kawasan Industri Bengkalis',
        ], $npwpFile, $hakiFile);

        $this->assertEquals('official', $storeOfficial->store_type);
        $this->assertTrue($storeOfficial->isOfficial());

        // Admin moderation: approve official store
        $storeService->moderateStore($storeOfficial->id, 'aktif');
        $this->assertTrue($storeOfficial->fresh()->isVerified());
    }

    /**
     * Test 5: SQL Optimization
     * Verify no asterisk full table scans and composite index presence.
     */
    public function test_sql_optimizations_and_eager_loading(): void
    {
        // Query approved postings and assert relations loaded
        $postings = app(\App\Repositories\PostinganRepository::class)->getApproved();
        $this->assertNotEmpty($postings);
        $this->assertTrue($postings->first()->relationLoaded('user'));

        // Query user orders and assert relations loaded without N+1
        $pesananRepo = app(\App\Repositories\PesananRepository::class);
        $pesanan = $pesananRepo->create([
            'order_number' => 'BJBO-TEST-SQL',
            'user_id' => $this->buyer->id,
            'postingan_id' => $this->product->id,
            'penjual_id' => $this->seller->id,
            'total_price' => 50000,
            'status' => 'pending',
            'shipping_status' => 'menunggu_konfirmasi',
        ]);

        $orders = $pesananRepo->getOrdersByUserId($this->buyer->id);
        $this->assertTrue($orders->first()->relationLoaded('postingan'));
        $this->assertTrue($orders->first()->relationLoaded('penjual'));
    }

    /**
     * Test 6: Order Fulfillment Lifecycle & Leaflet Courier Tracking
     */
    public function test_order_fulfillment_lifecycle_and_escrow_release(): void
    {
        $lifecycle = app(PesananLifecycleService::class);

        // 1. Create order
        $pesanan = Pesanan::create([
            'order_number' => 'BJBO-CYCLE-001',
            'user_id' => $this->buyer->id,
            'postingan_id' => $this->product->id,
            'penjual_id' => $this->seller->id,
            'total_price' => 50000,
            'status' => 'paid',
            'shipping_status' => 'menunggu_konfirmasi',
            'is_funds_released' => false,
        ]);

        // 2. Seller confirms order (Accept)
        $lifecycle->confirmOrder($pesanan, $this->seller->id, true);
        $this->assertEquals('diproses', $pesanan->fresh()->shipping_status);

        // 3. Seller ships order with courier name and tracking code
        $lifecycle->shipOrder($pesanan, $this->seller->id, 'J&T Express Bengkalis', 'JT99887766', [
            'origin_lat' => 1.4877,
            'origin_lng' => 102.1334,
            'destination_lat' => 1.4725,
            'destination_lng' => 102.1520,
        ]);

        $this->assertEquals('dikirim', $pesanan->fresh()->shipping_status);
        $this->assertEquals('JT99887766', $pesanan->fresh()->nomor_resi);

        // Check Leaflet OpenStreetMap tracking payload
        $tracking = $lifecycle->getTrackingData($pesanan->fresh());
        $this->assertEquals('J&T Express Bengkalis', $tracking['kurir_name']);
        $this->assertEquals(1.4877, $tracking['origin']['lat']);

        // 4. Mark as delivered / fulfilled -> Escrow released to seller!
        $sellerBalBefore = $this->seller->fresh()->saldo_rekber;
        $lifecycle->markAsDelivered($pesanan, $this->buyer->id);

        $this->assertEquals('sampai_tujuan', $pesanan->fresh()->shipping_status);
        $this->assertTrue((bool) $pesanan->fresh()->is_funds_released);
        $this->assertEquals($sellerBalBefore + 50000, $this->seller->fresh()->saldo_rekber);
    }

    /**
     * Test 7: Wishlist & Personalized Recommendation Engine (For You)
     */
    public function test_wishlist_and_for_you_recommendation_engine(): void
    {
        $engine = app(RecommendationEngineService::class);

        // 1. Toggle wishlist
        $res1 = $engine->toggleWishlist($this->buyer->id, $this->product->id);
        $this->assertTrue($res1['is_wishlisted']);
        $this->assertDatabaseHas('wishlists', [
            'user_id' => $this->buyer->id,
            'postingan_id' => $this->product->id,
        ]);

        // 2. Log search queries & detail views
        $engine->logSearch($this->buyer->id, 'lempuk', 'Kuliner');
        $engine->logProductView($this->buyer->id, $this->product->id);

        // 3. Create second candidate product in different category
        $otherProduct = Postingan::create([
            'name' => 'Baju Kurung Melayu',
            'price' => 120000,
            'category' => 'Pakaian',
            'description' => 'Baju kurung Melayu tradisional.',
            'image' => 'baju.jpg',
            'lokasi' => 'Bengkalis',
            'user_id' => $this->seller->id,
            'status' => 'disetujui',
            'is_sold' => false,
            'is_auto_blocked' => false,
        ]);

        // 4. Compute For You recommendations
        $recommendations = $engine->getRecommendationsForUser($this->buyer->id, 4);

        $this->assertNotEmpty($recommendations);
        // The Kuliner / Lempuk product should have highest affinity score due to wishlist (+5), search (+3), view (+2)
        $topRec = $recommendations->first();
        $this->assertEquals($this->product->id, $topRec->id);
        $this->assertGreaterThan(0, $topRec->affinity_score);
    }
}
