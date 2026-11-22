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

    public function test_admin_dashboard_renders_category_analytics_chart(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('categoryAnalyticsData');
        $response->assertSee('Analisis Kategori Barang Terlaris');
        $response->assertSee('Batang');
        $response->assertSee('Lingkaran');
        $response->assertSee('1 Minggu');
        $response->assertSee('1 Bulan');
        $response->assertSee('1 Tahun');
        $response->assertSee('All Time');
    }

    public function test_admin_can_fetch_category_analytics_json_api(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->getJson(route('admin.analytics.categories', ['period' => '1_minggu']));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'period',
            'period_label',
            'total_sold',
            'total_revenue',
            'formatted_revenue',
            'top_category',
            'categories',
            'counts',
            'revenues',
            'percentages',
            'items',
        ]);
    }
}
