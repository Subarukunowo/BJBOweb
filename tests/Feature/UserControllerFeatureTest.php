<?php

namespace Tests\Feature;

use App\Models\Postingan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserControllerFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_access_dashboard_and_postingan_detail(): void
    {
        $user = User::factory()->create();
        $postingan = Postingan::factory()->create([
            'status' => 'disetujui',
            'category' => 'Elektronik',
        ]);

        $dashboardResponse = $this->actingAs($user)->get(route('user.dashboard'));
        $dashboardResponse->assertStatus(200);

        $detailResponse = $this->actingAs($user)->get(route('user.detail.postingan', $postingan->id));
        $detailResponse->assertStatus(200);
    }

    public function test_user_can_search_and_filter_postings(): void
    {
        $user = User::factory()->create();
        Postingan::factory()->create([
            'name' => 'Meja Komputer',
            'category' => 'Furnitur',
            'status' => 'disetujui',
        ]);

        $searchResponse = $this->actingAs($user)->get(route('postingan.cari', ['keyword' => 'Komputer']));
        $searchResponse->assertStatus(200);

        $categoryResponse = $this->actingAs($user)->get(route('postingan.kategori', 'Furnitur'));
        $categoryResponse->assertStatus(200);
    }

    public function test_user_can_create_edit_and_delete_postingan(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        \App\Models\Store::create([
            'user_id' => $user->id,
            'store_name' => 'Toko Perabot',
            'store_slug' => 'toko-perabot',
            'store_type' => 'perorangan',
            'phone_number' => '081234567890',
            'email' => $user->email,
            'status' => 'aktif',
            'verified_at' => now(),
        ]);

        $createPageResponse = $this->actingAs($user)->get(route('user.postingan.create'));
        $createPageResponse->assertStatus(200);

        $image = UploadedFile::fake()->image('produk.jpg');
        $storeResponse = $this->actingAs($user)->post(route('user.postingan.store'), [
            'name' => 'Kursi Ergonomis',
            'price' => 250000,
            'category' => 'Furnitur',
            'description' => 'Sangat nyaman',
            'lokasi' => 'Bandung Kota',
            'image' => $image,
        ]);

        $storeResponse->assertRedirect(route('user.profil'));
        $postingan = Postingan::where('name', 'Kursi Ergonomis')->first();
        $this->assertNotNull($postingan);

        // Edit page
        $editResponse = $this->actingAs($user)->get(route('user.profil.edit', $postingan->id));
        $editResponse->assertStatus(200);

        // Update
        $updateResponse = $this->actingAs($user)->post(route('profil.update', $postingan->id), [
            'name' => 'Kursi Ergonomis Gaming',
            'price' => 270000,
            'category' => 'Furnitur',
            'description' => 'Sangat nyaman dan empuk',
            'lokasi' => 'Bandung Kota',
        ]);
        $updateResponse->assertRedirect(route('user.profil'));
        $this->assertEquals('Kursi Ergonomis Gaming', $postingan->fresh()->name);

        // Delete
        $deleteResponse = $this->actingAs($user)->delete(route('user.profil.delete', $postingan->id));
        $deleteResponse->assertRedirect();
        $this->assertNull(Postingan::find($postingan->id));
    }

    public function test_user_can_submit_ulasan_and_laporan(): void
    {
        $user = User::factory()->create();
        $postingan = Postingan::factory()->create();

        // Submit ulasan
        $ulasanResponse = $this->actingAs($user)->postJson(route('ulasan.store'), [
            'postingan_id' => $postingan->id,
            'rating' => 5,
            'komentar' => 'Barang sesuai deskripsi!',
        ]);
        $ulasanResponse->assertStatus(200);
        $ulasanResponse->assertJson(['success' => true]);

        // Submit laporan
        $laporanResponse = $this->actingAs($user)->post(route('laporan.submit'), [
            'jenis_laporan' => 'Penipuan',
            'teks' => 'Pelapak tidak merespons',
            'postingan_id' => $postingan->id,
        ]);
        $laporanResponse->assertRedirect();
    }

    public function test_user_profil_account_update_and_remove_avatar(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['name' => 'Original Name']);

        $profilResponse = $this->actingAs($user)->get(route('user.profil'));
        $profilResponse->assertStatus(200);

        $avatar = UploadedFile::fake()->image('user_avatar.jpg');
        $updateAccResponse = $this->actingAs($user)->post(route('user.profil.updateAccount'), [
            'name' => 'Updated Name',
            'email' => $user->email,
            'profile_picture' => $avatar,
        ]);
        $updateAccResponse->assertRedirect(route('user.profil'));
        $this->assertEquals('Updated Name', $user->fresh()->name);

        // Remove avatar
        $removeResponse = $this->actingAs($user)->delete(route('user.profil.removeAvatar'));
        $removeResponse->assertRedirect(route('user.profil'));
        $this->assertNull($user->fresh()->profile_picture);
    }
}
