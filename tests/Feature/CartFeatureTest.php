<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Postingan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartFeatureTest extends TestCase
{
    use RefreshDatabase;

    private const ROUTE_CART_ADD = 'user.cart.add';

    protected User $buyer;

    protected User $seller;

    protected Postingan $postingan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = User::factory()->create([
            'email' => 'seller@example.com',
            'saldo_bjpay' => 100000,
        ]);

        $this->buyer = User::factory()->create([
            'email' => 'buyer@example.com',
            'saldo_bjpay' => 200000,
        ]);

        $this->postingan = Postingan::create([
            'name' => 'Kamera Mirrorless Sony A6000 Bekas Mulus',
            'price' => 4500000,
            'category' => 'Elektronik',
            'description' => 'Sensor bersih, shutter count rendah, lengkap lensa kit 16-50mm.',
            'image' => 'sony.jpg',
            'lokasi' => 'Bengkalis Kota',
            'user_id' => $this->seller->id,
            'username' => $this->seller->name,
            'status' => 'disetujui',
            'is_sold' => false,
            'views_count' => 10,
        ]);
    }

    /**
     * Test menampilkan halaman keranjang belanja yang masih kosong.
     */
    public function test_user_can_view_empty_cart_page(): void
    {
        $response = $this->actingAs($this->buyer)->get(route('user.cart.index'));

        $response->assertStatus(200);
        $response->assertSee('Keranjang Belanja');
        $response->assertSee('Keranjang Belanja Masih Kosong');
    }

    /**
     * Test menambahkan barang ke keranjang belanja.
     */
    public function test_user_can_add_item_to_cart(): void
    {
        $response = $this->actingAs($this->buyer)->post(route(self::ROUTE_CART_ADD, $this->postingan->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('carts', [
            'user_id' => $this->buyer->id,
            'postingan_id' => $this->postingan->id,
            'quantity' => 1,
        ]);

        // Tambah lagi untuk memastikan kuantitas naik
        $this->actingAs($this->buyer)->post(route(self::ROUTE_CART_ADD, $this->postingan->id));
        $this->assertDatabaseHas('carts', [
            'user_id' => $this->buyer->id,
            'postingan_id' => $this->postingan->id,
            'quantity' => 2,
        ]);
    }

    /**
     * Test penjual dilarang menambahkan barang miliknya sendiri ke keranjang.
     */
    public function test_seller_cannot_add_own_item_to_cart(): void
    {
        $response = $this->actingAs($this->seller)->post(route(self::ROUTE_CART_ADD, $this->postingan->id));

        $this->assertDatabaseMissing('carts', [
            'user_id' => $this->seller->id,
            'postingan_id' => $this->postingan->id,
        ]);
    }

    /**
     * Test memperbarui jumlah barang di keranjang.
     */
    public function test_user_can_update_cart_quantity(): void
    {
        $cart = Cart::create([
            'user_id' => $this->buyer->id,
            'postingan_id' => $this->postingan->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($this->buyer)->post(route('user.cart.update', $cart->id), [
            'quantity' => 3,
        ]);

        $response->assertRedirect();
        $cart->refresh();
        $this->assertEquals(3, $cart->quantity);
    }

    /**
     * Test menghapus barang dari keranjang belanja.
     */
    public function test_user_can_remove_item_from_cart(): void
    {
        $cart = Cart::create([
            'user_id' => $this->buyer->id,
            'postingan_id' => $this->postingan->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($this->buyer)->delete(route('user.cart.remove', $cart->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('carts', [
            'id' => $cart->id,
        ]);
    }

    /**
     * Test API endpoint jumlah keranjang belanja.
     */
    public function test_user_can_query_cart_count_endpoint(): void
    {
        Cart::create([
            'user_id' => $this->buyer->id,
            'postingan_id' => $this->postingan->id,
            'quantity' => 4,
        ]);

        $response = $this->actingAs($this->buyer)->get(route('user.cart.count'));

        $response->assertStatus(200);
        $response->assertJson([
            'count' => 4,
        ]);
    }

    /**
     * Test navbar pengguna merender ikon notifikasi, wishlist, favorit, chat, keranjang,
     * dan tidak menampilkan pil saldo BJPay di navbar utama.
     */
    public function test_navbar_user_renders_all_required_icons_and_no_bjpay_pill(): void
    {
        $response = $this->actingAs($this->buyer)->get(route('user.dashboard'));

        $response->assertStatus(200);
        $response->assertSee(route('user.notifikasi.index'));
        $response->assertSee(route('user.wishlist.index'));
        $response->assertSee(route('user.favorit'));
        $response->assertSee(route('user.chat.index'));
        $response->assertSee(route('user.cart.index'));
        $response->assertSee('Muat Lebih Banyak');
    }
}
