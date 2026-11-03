<?php

namespace Tests\Unit\Models;

use App\Models\Admin;
use App\Models\Favorit;
use App\Models\Laporan;
use App\Models\Message;
use App\Models\Postingan;
use App\Models\Ulasan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OtherModelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_model(): void
    {
        $admin = Admin::factory()->create([
            'username' => 'superadmin',
            'email' => 'admin@bjbo.local',
        ]);

        $this->assertEquals('superadmin', $admin->username);
        $this->assertEquals('admin@bjbo.local', $admin->email);
        $this->assertNotEmpty($admin->password);
    }

    public function test_laporan_model(): void
    {
        $user = User::factory()->create();
        $postingan = Postingan::factory()->create();

        $laporan = Laporan::factory()->create([
            'user_id' => $user->id,
            'postingan_id' => $postingan->id,
            'jenis_laporan' => 'Penipuan',
            'status' => 'Pending',
        ]);

        $this->assertEquals($user->id, $laporan->user->id);
        $this->assertEquals($postingan->id, $laporan->postingan->id);
        $this->assertEquals('Penipuan', $laporan->jenis_laporan);
    }

    public function test_ulasan_model(): void
    {
        $user = User::factory()->create();
        $postingan = Postingan::factory()->create();

        $ulasan = Ulasan::factory()->create([
            'user_id' => $user->id,
            'postingan_id' => $postingan->id,
            'rating' => 4,
            'komentar' => 'Bagus sekali',
        ]);

        $this->assertEquals($user->id, $ulasan->user->id);
        $this->assertEquals($postingan->id, $ulasan->postingan->id);
        $this->assertEquals(4, $ulasan->rating);
        $this->assertEquals('Bagus sekali', $ulasan->komentar);
    }

    public function test_favorit_model(): void
    {
        $user = User::factory()->create();
        $postingan = Postingan::factory()->create();

        $favorit = Favorit::factory()->create([
            'user_id' => $user->id,
            'postingan_id' => $postingan->id,
        ]);

        $this->assertEquals($user->id, $favorit->user->id);
        $this->assertEquals($postingan->id, $favorit->postingan->id);
    }

    public function test_message_model(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $message = Message::factory()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message' => 'Halo apakah barang masih ada?',
        ]);

        $this->assertEquals($sender->id, $message->sender->id);
        $this->assertEquals($receiver->id, $message->receiver->id);
        $this->assertEquals('Halo apakah barang masih ada?', $message->message);
    }
}
