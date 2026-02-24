<?php

namespace App\Events;

use App\Models\Document;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentSigned implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $payload;
    public int $agentUserId;

    public function __construct(Document $document)
    {
        $document->loadMissing(['buyer', 'agent.user', 'signings']);

        $this->agentUserId = $document->agent->user_id;

        $this->payload = [
            'id'            => $document->id,
            'type'          => 'document_signed',
            'document_name' => $document->document_name,
            'document_type' => $document->document_type,
            'signed_at'     => $document->signed_at?->toISOString(),
            'buyer'         => [
                'id'   => $document->buyer->id,
                'name' => $document->buyer->name,
            ],
        ];
    }

    public function broadcastOn(): Channel
    {
        return new PrivateChannel('notifications.' . $this->agentUserId);
    }

    public function broadcastAs(): string
    {
        return 'DocumentSigned';
    }

    public function broadcastWith(): array
    {
        return $this->payload;
    }
}
