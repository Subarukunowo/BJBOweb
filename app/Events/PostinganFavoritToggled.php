<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostinganFavoritToggled implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $userId;

    public int $postinganId;

    public bool $favorited;

    public function __construct(int $userId, int $postinganId, bool $favorited)
    {
        $this->userId = $userId;
        $this->postinganId = $postinganId;
        $this->favorited = $favorited;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('postingan-favorit'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'favorit.toggled';
    }
}
