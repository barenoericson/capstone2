<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageRead implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $readerId;
    public int $conversationWith;
    public string $readAt;

    public function __construct(int $readerId, int $conversationWith)
    {
        $this->readerId = $readerId;
        $this->conversationWith = $conversationWith;
        $this->readAt = now()->toISOString();
    }

    public function broadcastOn(): PrivateChannel
    {
        // Notify the original sender that their messages have been read
        return new PrivateChannel('chat.' . $this->conversationWith);
    }

    public function broadcastAs(): string
    {
        return 'MessageRead';
    }

    public function broadcastWith(): array
    {
        return [
            'reader_id' => $this->readerId,
            'conversation_with' => $this->conversationWith,
            'read_at' => $this->readAt,
        ];
    }
}
