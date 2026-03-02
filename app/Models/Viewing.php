<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Viewing extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'property_id',
        'agent_id',
        'buyer_id',
        'viewing_date',
        'viewing_time',
        'viewing_time_slot',
        'status',
        'rejection_reason',
        'agent_notes',
        'buyer_notes',
        'reminder_sent',
        'reminder_sent_at',
        'google_calendar_event_id',
        'approved_at',
        'approved_by_user_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'viewing_date' => 'date',
        'viewing_time' => 'datetime:H:i:s',
        'reminder_sent' => 'boolean',
        'reminder_sent_at' => 'datetime',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Status enumeration
     */
    const STATUS_REQUESTED = 'requested';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_COMPLETED = 'completed';
    const STATUS_NEGOTIATING = 'negotiating';

    /**
     * Time slot enumeration
     */
    const SLOT_MORNING = 'morning';
    const SLOT_AFTERNOON = 'afternoon';
    const SLOT_EVENING = 'evening';

    /**
     * Relationships
     */

    /**
     * Get the property for this viewing
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the agent managing this viewing
     */
    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Get the buyer requesting this viewing
     */
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function negotiations()
    {
        return $this->hasMany(ViewingNegotiation::class);
    }

    public function latestProposal()
    {
        return $this->negotiations()
            ->where('status', ViewingNegotiation::STATUS_PENDING)
            ->latest()
            ->first();
    }

    /**
     * Scopes
     */

    /**
     * Scope to get pending viewings
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_REQUESTED);
    }

    /**
     * Scope to get approved viewings
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope to get upcoming viewings
     */
    public function scopeUpcoming($query)
    {
        return $query->where('viewing_date', '>=', today())
                     ->orderBy('viewing_date', 'asc')
                     ->orderBy('viewing_time', 'asc');
    }

    /**
     * Scope to get past viewings
     */
    public function scopePast($query)
    {
        return $query->where('viewing_date', '<', today());
    }

    /**
     * Scope to filter by agent
     */
    public function scopeByAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    /**
     * Scope to filter by buyer
     */
    public function scopeByBuyer($query, $buyerId)
    {
        return $query->where('buyer_id', $buyerId);
    }

    /**
     * Scope to filter by date
     */
    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('viewing_date', $date);
    }

    /**
     * Accessors & Mutators
     */

    /**
     * Get status display
     */
    public function getStatusNameAttribute()
    {
        return match ($this->status) {
            self::STATUS_REQUESTED => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_NEGOTIATING => 'Negotiating',
            default => 'Unknown',
        };
    }

    /**
     * Get time slot display
     */
    public function getTimeSlotNameAttribute()
    {
        return match ($this->viewing_time_slot) {
            self::SLOT_MORNING => '8:00 AM - 12:00 PM',
            self::SLOT_AFTERNOON => '12:00 PM - 4:00 PM',
            self::SLOT_EVENING => '4:00 PM - 8:00 PM',
            default => $this->viewing_time,
        };
    }

    /**
     * Methods
     */

    /**
     * Check if viewing is pending
     */
    public function isPending()
    {
        return $this->status === self::STATUS_REQUESTED;
    }

    /**
     * Check if viewing is approved
     */
    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if viewing is rejected
     */
    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Check if viewing is completed
     */
    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isNegotiating()
    {
        return $this->status === self::STATUS_NEGOTIATING;
    }

    /**
     * Check if viewing is in the future
     */
    public function isUpcoming()
    {
        return $this->viewing_date >= today();
    }

    /**
     * Check if viewing is in the past
     */
    public function isPast()
    {
        return $this->viewing_date < today();
    }

    /**
     * Approve the viewing
     */
    public function approve()
    {
        $this->update(['status' => self::STATUS_APPROVED]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'viewing_approved',
            'action_type' => 'approve',
            'model_type' => 'Viewing',
            'model_id' => $this->id,
            'description' => "Viewing for property {$this->property->title} approved",
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Reject the viewing
     */
    public function reject($reason = null)
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'rejection_reason' => $reason,
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'viewing_rejected',
            'action_type' => 'reject',
            'model_type' => 'Viewing',
            'model_id' => $this->id,
            'description' => "Viewing for property {$this->property->title} rejected",
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Mark viewing as completed
     */
    public function markAsCompleted()
    {
        $this->update(['status' => self::STATUS_COMPLETED]);
    }

    /**
     * Send reminder notification
     */
    public function sendReminder()
    {
        if (!$this->reminder_sent) {
            // TODO: Implement reminder notification
            $this->update([
                'reminder_sent' => true,
                'reminder_sent_at' => now(),
            ]);
        }
    }
}