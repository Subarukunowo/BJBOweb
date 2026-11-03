<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Postingan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_requires_admin_guard(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('splash'));

        $admin = Admin::factory()->create();
        $authResponse = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));
        $authResponse->assertStatus(200);
    }

    public function test_admin_can_block_and_unblock_user(): void
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create(['is_blocked' => false]);

        $blockResponse = $this->actingAs($admin, 'admin')->get(route('pengguna.block', $user->id));
        $blockResponse->assertRedirect();
        $this->assertTrue($user->fresh()->is_blocked);

        $unblockResponse = $this->actingAs($admin, 'admin')->get(route('pengguna.unblock', $user->id));
        $unblockResponse->assertRedirect();
        $this->assertFalse($user->fresh()->is_blocked);
    }

    public function test_admin_can_update_postingan_status(): void
    {
        $admin = Admin::factory()->create();
        $postingan = Postingan::factory()->create(['status' => 'belum disetujui']);

        $response = $this->actingAs($admin, 'admin')->post(route('posting.updateStatus', $postingan->id), [
            'status' => 'disetujui',
        ]);

        $response->assertRedirect();
        $this->assertEquals('disetujui', $postingan->fresh()->status);
    }
}
