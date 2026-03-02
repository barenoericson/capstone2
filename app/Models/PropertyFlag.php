<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyFlag extends Model
{
    const STATUS_PENDING = 'pending_review';
    const STATUS_APPROVED = 'approved';
    const STATUS_DISMISSED = 'dismissed';

    protected $fillable = [
        'property_id',
        'flag_reason',
        'ai_raw_response',
        'ai_confidence',
        'status',
        'flagged_at',
        'reviewed_at',
        'reviewed_by',
        'admin_notes',
    ];

    protected $casts = [
        'ai_raw_response' => 'json',
        'ai_confidence' => 'decimal:2',
        'flagged_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}
