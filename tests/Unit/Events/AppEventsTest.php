<?php

namespace Tests\Unit\Events;

use App\Events\PostinganFavoritToggled;
use App\Events\PostinganStatusChanged;
use App\Events\UlasanAdded;
use App\Models\Postingan;
use App\Models\Ulasan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppEventsTest extends TestCase
{
    use RefreshDatabase;

    public function test_postingan_favorit_toggled_event(): void
    {
        $event = new PostinganFavoritToggled(1, 2, true);

        $this->assertEquals(1, $event->userId);
        $this->assertEquals(2, $event->postinganId);
        $this->assertTrue($event->favorited);
        $this->assertEquals('favorit.toggled', $event->broadcastAs());

        $channels = $event->broadcastOn();
        $this->assertCount(1, $channels);
        $this->assertEquals('postingan-favorit', $channels[0]->name);
    }

    public function test_postingan_status_changed_event(): void
    {
        $event = new PostinganStatusChanged(5, true);

        $this->assertEquals(5, $event->postinganId);
        $this->assertTrue($event->isSold);
        $this->assertEquals('status.changed', $event->broadcastAs());

        $channels = $event->broadcastOn();
        $this->assertCount(1, $channels);
        $this->assertEquals('postingan-status', $channels[0]->name);
    }

    public function test_ulasan_added_event(): void
    {
        $user = User::factory()->create(['name' => 'Reviewer Name']);
        $postingan = Postingan::factory()->create();
        $ulasan = Ulasan::factory()->create([
            'user_id' => $user->id,
            'postingan_id' => $postingan->id,
            'rating' => 5,
            'komentar' => 'Mantap sekali',
        ]);

        $event = new UlasanAdded($ulasan);

        $this->assertEquals($postingan->id, $event->postinganId);
        $this->assertEquals(5, $event->rating);
        $this->assertEquals('Mantap sekali', $event->komentar);
        $this->assertEquals('Reviewer Name', $event->userName);
        $this->assertEquals('ulasan.added', $event->broadcastAs());

        $channels = $event->broadcastOn();
        $this->assertCount(1, $channels);
        $this->assertEquals('postingan-ulasan.'.$postingan->id, $channels[0]->name);
    }
}
