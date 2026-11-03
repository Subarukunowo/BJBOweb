<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Laporan;
use App\Models\Postingan;
use App\Models\Ulasan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminControllersFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_and_delete_users(): void
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();

        $indexResponse = $this->actingAs($admin, 'admin')->get(route('admin.users'));
        $indexResponse->assertStatus(200);

        $deleteResponse = $this->actingAs($admin, 'admin')->delete(route('pengguna.delete', $user->id));
        $deleteResponse->assertRedirect(route('admin.users'));
        $this->assertNull(User::find($user->id));
    }

    public function test_admin_can_view_and_delete_postings(): void
    {
        $admin = Admin::factory()->create();
        $postingan = Postingan::factory()->create();

        $indexResponse = $this->actingAs($admin, 'admin')->get(route('admin.postings'));
        $indexResponse->assertStatus(200);
    }

    public function test_admin_can_view_ulasans_and_laporans(): void
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $postingan = Postingan::factory()->create();

        Ulasan::factory()->create(['user_id' => $user->id, 'postingan_id' => $postingan->id]);
        Laporan::factory()->create(['user_id' => $user->id, 'postingan_id' => $postingan->id]);

        $ulasanResponse = $this->actingAs($admin, 'admin')->get(route('admin.ulasans'));
        $ulasanResponse->assertStatus(200);

        $laporanResponse = $this->actingAs($admin, 'admin')->get(route('admin.laporans'));
        $laporanResponse->assertStatus(200);
    }
}
