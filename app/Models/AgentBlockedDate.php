<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentBlockedDate extends Model
{
    protected $fillable = ['agent_id', 'blocked_date', 'reason'];

    protected $casts = [
        'blocked_date' => 'date',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
