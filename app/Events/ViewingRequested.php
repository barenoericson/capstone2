<?php

namespace App\Events;

use App\Models\Viewing;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ViewingRequested implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $payload;
    public int $agentUserId;

    public function __construct(Viewing $viewing)
    {
        $viewing->loadMissing(['property', 'buyer', 'agent.user']);

        $this->agentUserId = $viewing->agent->user_id;

        $this->payload = [
            'id'           => $viewing->id,
            'type'         => 'viewing_requested',
            'viewing_date' => $viewing->viewing_date?->format('Y-m-d'),
            'viewing_time' => $viewing->viewing_time
                                ? (is_string($viewing->viewing_time)
                                    ? $viewing->viewing_time
                                    : $viewing->viewing_time->format('H:i'))
                                : null,
            'status'       => $viewing->status,
            'buyer_notes'  => $viewing->buyer_notes,
            'property'     => [
                'id'    => $viewing->property->id,
                'title' => $viewing->property->title,
                'city'  => $viewing->property->city,
            ],
            'buyer'        => [
                'id'   => $viewing->buyer->id,
                'name' => $viewing->buyer->name,
            ],
            'created_at'   => $viewing->created_at->toISOString(),
        ];
    }

    public function broadcastOn(): Channel
    {
        return new PrivateChannel('notifications.' . $this->agentUserId);
    }

    public function broadcastAs(): string
    {
        return 'ViewingRequested';
    }

    public function broadcastWith(): array
    {
        return $this->payload;
    }
}
