<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewingNegotiation extends Model
{
    protected $fillable = [
        'viewing_id',
        'proposed_by_user_id',
        'proposed_by_role',
        'proposed_date',
        'proposed_time',
        'note',
        'status',
    ];

    protected $casts = [
        'proposed_date' => 'date',
        'proposed_time' => 'datetime:H:i:s',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_SUPERSEDED = 'superseded';

    public function viewing()
    {
        return $this->belongsTo(Viewing::class);
    }

    public function proposedBy()
    {
        return $this->belongsTo(User::class, 'proposed_by_user_id');
    }
}
