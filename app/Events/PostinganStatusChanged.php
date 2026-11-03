<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostinganStatusChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $postinganId;

    public bool $isSold;

    public function __construct(int $postinganId, bool $isSold)
    {
        $this->postinganId = $postinganId;
        $this->isSold = $isSold;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('postingan-status'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'status.changed';
    }
}
