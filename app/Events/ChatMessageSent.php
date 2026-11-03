<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;
    public string $decryptedMessage;
    public bool $wasCensored;

    public function __construct(Message $message, string $decryptedMessage, bool $wasCensored = false)
    {
        $this->message = $message;
        $this->decryptedMessage = $decryptedMessage;
        $this->wasCensored = $wasCensored;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        $min = min($this->message->sender_id, $this->message->receiver_id);
        $max = max($this->message->sender_id, $this->message->receiver_id);

        return [
            new Channel("chat-room.{$min}.{$max}"),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'chat.sent';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'sender_id' => $this->message->sender_id,
            'receiver_id' => $this->message->receiver_id,
            'postingan_id' => $this->message->postingan_id,
            'decrypted_message' => $this->decryptedMessage,
            'was_censored' => $this->wasCensored,
            'created_at' => $this->message->created_at ? $this->message->created_at->format('H:i') : now()->format('H:i'),
        ];
    }
}

