<?php

namespace App\Events;

use App\Models\Viewing;
use App\Models\ViewingNegotiation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ViewingNegotiationProposed implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $payload;
    public int $targetUserId;

    public function __construct(Viewing $viewing, ViewingNegotiation $negotiation, int $targetUserId)
    {
        $viewing->loadMissing(['property', 'buyer', 'agent.user']);
        $negotiation->loadMissing('proposedBy');

        $this->targetUserId = $targetUserId;

        $this->payload = [
            'id'            => $viewing->id,
            'type'          => 'viewing_negotiation_proposed',
            'status'        => $viewing->status,
            'viewing_date'  => $viewing->viewing_date?->format('Y-m-d'),
            'viewing_time'  => $viewing->viewing_time
                ? (is_string($viewing->viewing_time) ? $viewing->viewing_time : $viewing->viewing_time->format('H:i'))
                : null,
            'proposal'      => [
                'id'            => $negotiation->id,
                'proposed_date' => $negotiation->proposed_date->format('Y-m-d'),
                'proposed_time' => is_string($negotiation->proposed_time)
                    ? $negotiation->proposed_time
                    : $negotiation->proposed_time->format('H:i'),
                'note'          => $negotiation->note,
                'proposed_by'   => $negotiation->proposedBy->name ?? 'Unknown',
                'proposed_by_role' => $negotiation->proposed_by_role,
            ],
            'property'      => [
                'id'    => $viewing->property->id,
                'title' => $viewing->property->title,
                'city'  => $viewing->property->city,
            ],
            'buyer'         => [
                'id'   => $viewing->buyer->id,
                'name' => $viewing->buyer->name,
            ],
            'agent'         => [
                'id'   => $viewing->agent->id,
                'name' => $viewing->agent->user->name ?? 'Agent',
            ],
        ];
    }

    public function broadcastOn(): Channel
    {
        return new PrivateChannel('notifications.' . $this->targetUserId);
    }

    public function broadcastAs(): string
    {
        return 'ViewingNegotiationProposed';
    }

    public function broadcastWith(): array
    {
        return $this->payload;
    }
}
