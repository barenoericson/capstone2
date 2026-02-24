<?php

namespace App\Events;

use App\Models\Viewing;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ViewingStatusChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $payload;
    public int $buyerId;

    public function __construct(Viewing $viewing)
    {
        $viewing->loadMissing(['property', 'buyer', 'agent.user']);

        $this->buyerId = $viewing->buyer_id;

        $this->payload = [
            'id'               => $viewing->id,
            'type'             => 'viewing_status_changed',
            'status'           => $viewing->status,
            'rejection_reason' => $viewing->rejection_reason,
            'viewing_date'     => $viewing->viewing_date?->format('Y-m-d'),
            'viewing_time'     => $viewing->viewing_time
                                    ? (is_string($viewing->viewing_time)
                                        ? $viewing->viewing_time
                                        : $viewing->viewing_time->format('H:i'))
                                    : null,
            'property'         => [
                'id'    => $viewing->property->id,
                'title' => $viewing->property->title,
                'city'  => $viewing->property->city,
            ],
            'agent'            => [
                'id'   => $viewing->agent->id,
                'name' => $viewing->agent->user->name ?? 'Agent',
            ],
        ];
    }

    public function broadcastOn(): Channel
    {
        return new PrivateChannel('notifications.' . $this->buyerId);
    }

    public function broadcastAs(): string
    {
        return 'ViewingStatusChanged';
    }

    public function broadcastWith(): array
    {
        return $this->payload;
    }
}
