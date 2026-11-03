<?php

namespace Tests\Feature;

use App\Models\Pesanan;
use App\Models\Postingan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserEcommerceFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_toggle_favorit(): void
    {
        $user = User::factory()->create();
        $postingan = Postingan::factory()->create(['status' => 'disetujui']);

        $response = $this->actingAs($user)->postJson(route('user.favorit.toggle', $postingan->id));
        $response->assertStatus(200);
        $response->assertJson(['favorited' => true]);

        $this->assertDatabaseHas('favorits', [
            'user_id' => $user->id,
            'postingan_id' => $postingan->id,
        ]);

        // Toggle again to remove
        $response2 = $this->actingAs($user)->postJson(route('user.favorit.toggle', $postingan->id));
        $response2->assertStatus(200);
        $response2->assertJson(['favorited' => false]);
    }

    public function test_user_can_create_and_simulate_order(): void
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();
        $postingan = Postingan::factory()->create([
            'user_id' => $seller->id,
            'status' => 'disetujui',
            'price' => 75000,
        ]);

        // Access checkout page
        $checkoutResponse = $this->actingAs($buyer)->get(route('user.pesanan.checkout', $postingan->id));
        $checkoutResponse->assertStatus(200);

        // Store order
        $storeResponse = $this->actingAs($buyer)->post(route('user.pesanan.store', $postingan->id), [
            'catatan' => 'Mohon dikirim sore ini',
        ]);

        $pesanan = Pesanan::where('user_id', $buyer->id)->first();
        $this->assertNotNull($pesanan);
        $storeResponse->assertRedirect(route('user.pesanan.detail', $pesanan->id));

        // Simulate QRIS payment
        $simulateResponse = $this->actingAs($buyer)->post(route('user.pesanan.simulate-qris', $pesanan->id));
        $simulateResponse->assertRedirect(route('user.pesanan.detail', $pesanan->id));
        $this->assertEquals('paid', $pesanan->fresh()->status);
    }

    public function test_user_can_toggle_sold_product(): void
    {
        $user = User::factory()->create();
        $postingan = Postingan::factory()->create([
            'user_id' => $user->id,
            'is_sold' => false,
        ]);

        $response = $this->actingAs($user)->postJson(route('user.profil.toggleSold', $postingan->id));
        $response->assertStatus(200);
        $this->assertTrue($postingan->fresh()->is_sold);
    }

    public function test_user_without_store_cannot_access_create_product(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('user.postingan.create'));
        $response->assertRedirect(route('user.toko.daftar'));

        // Attempting to post even with valid payload
        $dummyImage = \Illuminate\Http\UploadedFile::fake()->image('produk.jpg');
        $postResponse = $this->actingAs($user)->post(route('user.postingan.store'), [
            'name' => 'Barang Tanpa Toko',
            'price' => 50000,
            'category' => 'Lainnya',
            'description' => 'Mencoba jual tanpa buka toko.',
            'lokasi' => 'Bengkalis Kota',
            'image' => $dummyImage,
        ]);
        $postResponse->assertRedirect(route('user.toko.daftar'));
    }

    public function test_user_with_active_store_can_access_create_product(): void
    {
        $user = User::factory()->create();
        \App\Models\Store::create([
            'user_id' => $user->id,
            'store_name' => 'Toko Resmi Sandy',
            'store_slug' => 'toko-resmi-sandy',
            'store_type' => 'perorangan',
            'phone_number' => '081234567890',
            'email' => $user->email,
            'ktp_number' => '1403011234560001',
            'address' => 'Bengkalis Kota',
            'status' => 'aktif',
            'verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('user.postingan.create'));
        $response->assertStatus(200);
    }

    public function test_user_without_store_does_not_see_kelola_toko_button(): void
    {
        $user = User::factory()->create(['name' => 'Pengguna Baru']);

        $response = $this->actingAs($user)->get(route('user.dashboard'));
        $response->assertStatus(200);
        $response->assertDontSee('Kelola Toko Saya');
        $response->assertSee('Buka / Daftar Toko');
    }
}
