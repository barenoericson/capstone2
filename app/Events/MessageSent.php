<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $payload;

    public function __construct(Message $message)
    {
        $message->loadMissing(['sender', 'recipient', 'property']);

        $this->payload = [
            'id'              => $message->id,
            'sender_id'       => $message->sender_id,
            'recipient_id'    => $message->recipient_id,
            'property_id'     => $message->property_id,
            'message_content' => $message->message_content,
            'attachment_path' => $message->attachment_path,
            'attachment_name' => $message->attachment_name,
            'attachment_type' => $message->attachment_type,
            'is_read'         => $message->is_read,
            'created_at'      => $message->created_at->toISOString(),
            'sender' => [
                'id'   => $message->sender->id,
                'name' => $message->sender->name,
            ],
        ];
    }

    public function broadcastOn(): Channel
    {
        return new PrivateChannel('chat.' . $this->payload['recipient_id']);
    }

    public function broadcastAs(): string
    {
        return 'MessageSent';
    }

    public function broadcastWith(): array
    {
        return $this->payload;
    }
}
