<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Postingan;
use App\Models\Promo;
use App\Models\User;
use App\Services\InvoiceService;
use App\Services\NotificationService;
use App\Services\PesananService;
use App\Services\PostinganService;
use App\Services\PromoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class InvoiceAndPaymentFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $buyer;

    protected User $seller;

    protected Postingan $postingan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyer = User::factory()->create([
            'name' => 'Budi Pembeli',
            'email' => 'budi@gmail.com',
            'saldo_bjpay' => 100000,
        ]);

        $this->seller = User::factory()->create([
            'name' => 'Siti Penjual',
            'email' => 'siti@gmail.com',
        ]);

        $this->postingan = Postingan::factory()->create([
            'user_id' => $this->seller->id,
            'name' => 'Kamera Canon 600D Bekas',
            'price' => 50000,
            'status' => 'disetujui',
            'is_auto_blocked' => false,
            'is_sold' => false,
        ]);
    }

    /**
     * Test checkout dengan metode BJPay berhasil, memotong saldo, dan memberikan potongan ongkir.
     */
    public function test_checkout_with_bjpay_deducts_balance_and_applies_shipping_discount(): void
    {
        $pesananService = app(PesananService::class);

        // Harga: 50.000, Ongkir: 10.000, Diskon Ongkir BJPay: 5.000 -> Total: 55.000
        $pesanan = $pesananService->createOrder(
            $this->buyer->id,
            $this->postingan->id,
            'Mohon packing kayu aman',
            null,
            'bjpay'
        );

        $this->assertEquals('bjpay', $pesanan->metode_pembayaran);
        $this->assertEquals('paid', $pesanan->status);
        $this->assertEquals(10000, $pesanan->ongkir);
        $this->assertEquals(5000, $pesanan->diskon_ongkir);
        $this->assertEquals(55000, $pesanan->final_price);

        // Saldo pembeli berkurang dari 100.000 menjadi 45.000
        $this->buyer->refresh();
        $this->assertEquals(45000, $this->buyer->saldo_bjpay);

        // Invoice otomatis terbit dengan status lunas
        $invoice = Invoice::where('pesanan_id', $pesanan->id)->first();
        $this->assertNotNull($invoice);
        $this->assertEquals('lunas', $invoice->status);
        $this->assertEquals('bjpay', $invoice->metode_pembayaran);
        $this->assertEquals(55000, $invoice->total_bayar);
        $this->assertEquals(5000, $invoice->diskon_ongkir);
    }

    /**
     * Test checkout dengan BJPay gagal jika saldo tidak mencukupi.
     */
    public function test_checkout_with_insufficient_bjpay_balance_fails(): void
    {
        $this->buyer->update(['saldo_bjpay' => 10000]); // Saldo hanya 10.000, butuh 55.000
        $pesananService = app(PesananService::class);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Saldo BJPay Anda tidak mencukupi');

        $pesananService->createOrder(
            $this->buyer->id,
            $this->postingan->id,
            null,
            null,
            'bjpay'
        );
    }

    /**
     * Test checkout dengan metode COD (Bayar di Tempat).
     */
    public function test_checkout_with_cod_payment_method(): void
    {
        $pesananService = app(PesananService::class);

        // Harga: 50.000, Ongkir: 10.000, Tanpa diskon ongkir BJPay -> Total: 60.000
        $pesanan = $pesananService->createOrder(
            $this->buyer->id,
            $this->postingan->id,
            'Alamat kantor camat',
            null,
            'cod'
        );

        $this->assertEquals('cod', $pesanan->metode_pembayaran);
        $this->assertEquals('pending', $pesanan->status);
        $this->assertEquals(60000, $pesanan->final_price);

        // Invoice otomatis terbit dengan status menunggu_pembayaran
        $invoice = Invoice::where('pesanan_id', $pesanan->id)->first();
        $this->assertNotNull($invoice);
        $this->assertEquals('menunggu_pembayaran', $invoice->status);
        $this->assertEquals('cod', $invoice->metode_pembayaran);
        $this->assertEquals(60000, $invoice->total_bayar);
    }

    /**
     * Test verifikasi dan pencarian struk digital melalui InvoiceService.
     */
    public function test_invoice_service_lookup_and_receipt_token(): void
    {
        $invoiceService = app(InvoiceService::class);
        $pesananService = app(PesananService::class);

        $pesanan = $pesananService->createOrder(
            $this->buyer->id,
            $this->postingan->id,
            null,
            null,
            'transfer'
        );

        $invoice = $invoiceService->getInvoiceByOrderId($pesanan->id);
        $this->assertNotNull($invoice);

        $foundByNumber = $invoiceService->getInvoiceByNumber($invoice->invoice_number);
        $this->assertNotNull($foundByNumber);
        $this->assertEquals($invoice->id, $foundByNumber->id);

        $foundByToken = $invoiceService->getInvoiceByToken($invoice->receipt_token);
        $this->assertNotNull($foundByToken);
        $this->assertEquals($invoice->id, $foundByToken->id);
    }

    /**
     * Test rute tampilan struk digital dan verifikasi publik.
     */
    public function test_digital_receipt_views(): void
    {
        $pesananService = app(PesananService::class);
        $pesanan = $pesananService->createOrder(
            $this->buyer->id,
            $this->postingan->id,
            null,
            null,
            'bjpay'
        );

        $invoice = Invoice::where('pesanan_id', $pesanan->id)->first();

        // 1. User terautentikasi dapat melihat struk digital
        $response = $this->actingAs($this->buyer)->get(route('user.pesanan.invoice', $pesanan->id));
        $response->assertStatus(200);
        $response->assertSee($invoice->invoice_number);
        $response->assertSee('OFFICIAL RECEIPT');

        // 2. Publik dapat memverifikasi struk digital via receipt_token
        $publicResponse = $this->get(route('invoice.view', $invoice->receipt_token));
        $publicResponse->assertStatus(200);
        $publicResponse->assertSee($invoice->invoice_number);
    }

    /**
     * Test top up saldo dompet BJPay.
     */
    public function test_user_can_topup_bjpay_wallet(): void
    {
        $response = $this->actingAs($this->buyer)->post(route('user.bjpay.topup'), [
            'nominal' => 75000,
        ]);

        $response->assertRedirect(route('user.bjpay.index'));
        $this->buyer->refresh();
        $this->assertEquals(175000, $this->buyer->saldo_bjpay);
    }

    /**
     * Test caching query pada PostinganService, PromoService, dan NotificationService.
     */
    public function test_caching_and_invalidation(): void
    {
        Cache::flush();

        $postinganService = app(PostinganService::class);
        $promoService = app(PromoService::class);
        $notificationService = app(NotificationService::class);

        // 1. Caching postingan
        $postings = $postinganService->getApprovedPostings();
        $this->assertTrue(Cache::has('bjbo_approved_postings_all'));

        $postinganService->clearPostinganCache();
        $this->assertFalse(Cache::has('bjbo_approved_postings_all'));

        // 2. Caching banner promo
        $banners = $promoService->getActiveBanners();
        $this->assertTrue(Cache::has('bjbo_active_banners'));

        $promoService->clearPromoCache();
        $this->assertFalse(Cache::has('bjbo_active_banners'));

        // 3. Caching unread count notifikasi
        $unreadCount = $notificationService->getUnreadCount($this->buyer->id);
        $this->assertTrue(Cache::has("user_unread_notif_count_{$this->buyer->id}"));

        $notificationService->sendNotification($this->buyer->id, 'Test', 'Pesan');
        $this->assertFalse(Cache::has("user_unread_notif_count_{$this->buyer->id}"));
    }
}
