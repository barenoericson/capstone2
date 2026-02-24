<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $payload;
    public int $userId;

    public function __construct(Notification $notification)
    {
        $this->userId = $notification->user_id;

        $this->payload = [
            'id' => $notification->id,
            'notification_type' => $notification->notification_type,
            'title' => $notification->title,
            'message' => $notification->message,
            'related_model_type' => $notification->related_model_type,
            'related_model_id' => $notification->related_model_id,
            'is_read' => false,
            'created_at' => $notification->created_at->toISOString(),
            'icon' => $notification->icon,
            'type_display' => $notification->type_display,
        ];
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('notifications.' . $this->userId);
    }

    public function broadcastAs(): string
    {
        return 'NotificationSent';
    }

    public function broadcastWith(): array
    {
        return $this->payload;
    }
}
