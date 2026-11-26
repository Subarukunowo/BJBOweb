<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Pesanan;
use App\Models\Postingan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MobileApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_can_get_product_list_and_categories_publicly(): void
    {
        $seller = User::factory()->create();
        Postingan::factory()->count(3)->create([
            'user_id' => $seller->id,
            'status' => 'disetujui',
            'is_auto_blocked' => false,
            'category' => 'Elektronik',
        ]);

        $response = $this->getJson('/api/v1/products');
        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $catResponse = $this->getJson('/api/v1/products/categories');
        $catResponse->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_user_can_login_and_receive_bearer_token(): void
    {
        $user = User::factory()->create([
            'email' => 'mobiletest@bjbo.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'mobiletest@bjbo.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user',
                    'token',
                    'token_type',
                ],
            ]);

        $token = $response->json('data.token');

        // Test authenticated profile access
        $meResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/auth/me');

        $meResponse->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.email', 'mobiletest@bjbo.com');
    }

    public function test_unauthenticated_requests_are_rejected(): void
    {
        $response = $this->getJson('/api/v1/auth/me');
        $response->assertStatus(401)
            ->assertJsonPath('success', false);
    }

    public function test_cart_operations_via_api(): void
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();
        $token = $user->createToken('mobile-app')->plainTextToken;

        $product = Postingan::factory()->create([
            'user_id' => $seller->id,
            'status' => 'disetujui',
            'is_sold' => false,
        ]);

        // Add to cart
        $addResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/cart', [
                'postingan_id' => $product->id,
                'quantity' => 1,
            ]);

        $addResponse->assertStatus(200)
            ->assertJsonPath('success', true);

        // Get cart
        $getResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/cart');

        $getResponse->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.total_items', 1);

        $cartId = Cart::where('user_id', $user->id)->first()->id;

        // Delete from cart
        $delResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson("/api/v1/cart/{$cartId}");

        $delResponse->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_order_checkout_and_lifecycle_via_api(): void
    {
        $buyer = User::factory()->create(['saldo_bjpay' => 500000]);
        $seller = User::factory()->create();
        $buyerToken = $buyer->createToken('mobile-app')->plainTextToken;
        $sellerToken = $seller->createToken('mobile-app')->plainTextToken;

        $product = Postingan::factory()->create([
            'user_id' => $seller->id,
            'price' => 50000,
            'status' => 'disetujui',
            'is_sold' => false,
        ]);

        // Checkout with BJPay
        $checkoutResponse = $this->withHeader('Authorization', 'Bearer '.$buyerToken)
            ->postJson('/api/v1/orders/checkout', [
                'postingan_id' => $product->id,
                'metode_pembayaran' => 'bjpay',
                'catatan' => 'Jalan Antara Bengkalis',
            ]);

        $checkoutResponse->assertStatus(201)
            ->assertJsonPath('success', true);

        $orderId = $checkoutResponse->json('data.id');

        // Seller confirms order
        $confirmResponse = $this->withHeader('Authorization', 'Bearer '.$sellerToken)
            ->postJson("/api/v1/orders/{$orderId}/confirm", [
                'action' => 'accept',
            ]);

        $confirmResponse->assertStatus(200)
            ->assertJsonPath('success', true);

        // Seller ships order
        $shipResponse = $this->withHeader('Authorization', 'Bearer '.$sellerToken)
            ->postJson("/api/v1/orders/{$orderId}/ship", [
                'kurir_name' => 'J&T Express Bengkalis',
                'nomor_resi' => 'JT123456789',
            ]);

        $shipResponse->assertStatus(200)
            ->assertJsonPath('success', true);

        // Buyer checks tracking
        $trackingResponse = $this->withHeader('Authorization', 'Bearer '.$buyerToken)
            ->getJson("/api/v1/orders/{$orderId}/tracking");

        $trackingResponse->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.kurir_name', 'J&T Express Bengkalis');

        // Buyer confirms receipt (deliver)
        $deliverResponse = $this->withHeader('Authorization', 'Bearer '.$buyerToken)
            ->postJson("/api/v1/orders/{$orderId}/deliver");

        $deliverResponse->assertStatus(200)
            ->assertJsonPath('success', true);

        // Verify seller received escrow funds in Rekber wallet
        $this->assertGreaterThan(0, $seller->fresh()->saldo_rekber);
    }

    public function test_wallet_bjpay_and_rekber_via_api(): void
    {
        $user = User::factory()->create(['saldo_bjpay' => 100000, 'saldo_rekber' => 200000]);
        $token = $user->createToken('mobile-app')->plainTextToken;

        // Topup BJPay
        $topupResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/wallet/bjpay/topup', [
                'nominal' => 50000,
            ]);

        $topupResponse->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.saldo', 150000);

        // Add bank account
        $bankResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/wallet/rekber/bank-accounts', [
                'bank_name' => 'Bank Riau Kepri Syariah',
                'account_number' => '1029384756',
                'account_name' => 'BUDI SANTOSO',
            ]);

        $bankResponse->assertStatus(201)
            ->assertJsonPath('success', true);

        $bankId = $bankResponse->json('data.id');

        // Withdraw from Rekber
        $withdrawResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/wallet/rekber/withdraw', [
                'amount' => 50000,
                'bank_account_id' => $bankId,
            ]);

        $withdrawResponse->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertEquals(150000, $user->fresh()->saldo_rekber);
    }

    public function test_chat_messaging_via_api(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        $token = $sender->createToken('mobile-app')->plainTextToken;

        // Send normal chat message
        $sendResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/chats/send', [
                'receiver_id' => $receiver->id,
                'message' => 'Halo apakah barang ini masih ada?',
            ]);

        $sendResponse->assertStatus(201)
            ->assertJsonPath('success', true);

        // Send phone number (anti-phone sensor test)
        $phoneResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/chats/send', [
                'receiver_id' => $receiver->id,
                'message' => 'Hubungi nomor saya di 081234567890 ya mas',
            ]);

        $phoneResponse->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.decrypted_message', 'pesan ini tidak dapat dikirim');
    }
}
