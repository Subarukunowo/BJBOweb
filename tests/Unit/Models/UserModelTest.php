<?php

namespace Tests\Unit\Models;

use App\Models\Favorit;
use App\Models\Laporan;
use App\Models\Message;
use App\Models\Pesanan;
use App\Models\Postingan;
use App\Models\Ulasan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_creation_and_attributes(): void
    {
        $user = User::factory()->create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'is_blocked' => false,
        ]);

        $this->assertEquals('Budi Santoso', $user->name);
        $this->assertEquals('budi@example.com', $user->email);
        $this->assertFalse($user->is_blocked);
        $this->assertNotEmpty($user->password);
    }

    public function test_user_relationships(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $postingan = Postingan::factory()->create(['user_id' => $user->id]);
        $this->assertTrue($user->postingan->contains($postingan));

        $sentMsg = Message::factory()->create(['sender_id' => $user->id, 'receiver_id' => $otherUser->id]);
        $this->assertTrue($user->sentMessages->contains($sentMsg));

        $recvMsg = Message::factory()->create(['sender_id' => $otherUser->id, 'receiver_id' => $user->id]);
        $this->assertTrue($user->receivedMessages->contains($recvMsg));

        $laporan = Laporan::factory()->create(['user_id' => $user->id, 'postingan_id' => $postingan->id]);
        $this->assertTrue($user->laporan->contains($laporan));

        $ulasan = Ulasan::factory()->create(['user_id' => $user->id, 'postingan_id' => $postingan->id]);
        $this->assertTrue($user->ulasan->contains($ulasan));

        $favorit = Favorit::factory()->create(['user_id' => $user->id, 'postingan_id' => $postingan->id]);
        $this->assertTrue($user->favorits->contains($favorit));
        $this->assertTrue($user->favoritePostings->contains($postingan));

        $pesanan = Pesanan::factory()->create(['user_id' => $user->id, 'postingan_id' => $postingan->id, 'penjual_id' => $otherUser->id]);
        $this->assertTrue($user->pesanans->contains($pesanan));
    }

    public function test_profile_picture_url_accessor(): void
    {
        $userWithoutAvatar = User::factory()->make(['name' => 'John Doe', 'profile_picture' => null]);
        $this->assertStringContainsString('ui-avatars.com', $userWithoutAvatar->profile_picture_url);

        $userWithUrlAvatar = User::factory()->make(['profile_picture' => 'https://example.com/avatar.png']);
        $this->assertEquals('https://example.com/avatar.png', $userWithUrlAvatar->profile_picture_url);

        $userWithLocalAvatar = User::factory()->make(['profile_picture' => 'avatars/sample.jpg']);
        $this->assertStringContainsString('storage/avatars/sample.jpg', $userWithLocalAvatar->profile_picture_url);
    }
}
