<?php

namespace App\Events;

use App\Models\Ulasan;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UlasanAdded implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $postinganId;

    public int $rating;

    public string $komentar;

    public string $userName;

    public string $userAvatar;

    public string $createdAt;

    public function __construct(Ulasan $ulasan)
    {
        $this->postinganId = $ulasan->postingan_id;
        $this->rating = (int) $ulasan->rating;
        $this->komentar = (string) $ulasan->komentar;
        $this->userName = $ulasan->user->name ?? 'Warga';
        $this->userAvatar = $ulasan->user->profile_picture_url ?? asset('images/bjbo.png');
        $this->createdAt = $ulasan->created_at ? $ulasan->created_at->format('d M Y') : 'Baru saja';
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('postingan-ulasan.'.$this->postinganId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'ulasan.added';
    }
}
