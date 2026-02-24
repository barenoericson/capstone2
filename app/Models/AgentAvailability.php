<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgentAvailability extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'agent_availability';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'agent_id',
        'available_date',
        'is_available',
        'morning_available',
        'afternoon_available',
        'evening_available',
        'max_viewings_per_day',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'available_date' => 'date',
        'is_available' => 'boolean',
        'morning_available' => 'boolean',
        'afternoon_available' => 'boolean',
        'evening_available' => 'boolean',
        'max_viewings_per_day' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationships
     */

    /**
     * Get the agent associated with this availability
     */
    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Scopes
     */

    /**
     * Scope to filter by agent
     */
    public function scopeByAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    /**
     * Scope to filter available dates
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope to filter unavailable dates
     */
    public function scopeUnavailable($query)
    {
        return $query->where('is_available', false);
    }

    /**
     * Scope to filter future dates
     */
    public function scopeFuture($query)
    {
        return $query->where('available_date', '>=', today())
                     ->orderBy('available_date', 'asc');
    }

    /**
     * Scope to filter past dates
     */
    public function scopePast($query)
    {
        return $query->where('available_date', '<', today())
                     ->orderBy('available_date', 'desc');
    }

    /**
     * Scope to filter by date range
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('available_date', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by time slot
     */
    public function scopeWithTimeSlot($query, $timeSlot)
    {
        $column = match ($timeSlot) {
            'morning' => 'morning_available',
            'afternoon' => 'afternoon_available',
            'evening' => 'evening_available',
            default => null,
        };

        if ($column) {
            return $query->where($column, true);
        }

        return $query;
    }

    /**
     * Accessors & Mutators
     */

    /**
     * Get available time slots
     */
    public function getAvailableSlotsAttribute()
    {
        $slots = [];

        if ($this->morning_available) {
            $slots[] = 'morning';
        }
        if ($this->afternoon_available) {
            $slots[] = 'afternoon';
        }
        if ($this->evening_available) {
            $slots[] = 'evening';
        }

        return $slots;
    }

    /**
     * Get available slots display
     */
    public function getAvailableSlotsDisplayAttribute()
    {
        $slots = [];

        if ($this->morning_available) {
            $slots[] = '8:00 AM - 12:00 PM';
        }
        if ($this->afternoon_available) {
            $slots[] = '12:00 PM - 4:00 PM';
        }
        if ($this->evening_available) {
            $slots[] = '4:00 PM - 8:00 PM';
        }

        return implode(', ', $slots);
    }

    /**
     * Get formatted date
     */
    public function getFormattedDateAttribute()
    {
        return $this->available_date->format('M d, Y (l)');
    }

    /**
     * Methods
     */

    /**
     * Check if date is available
     */
    public function isAvailableDate()
    {
        return $this->is_available === true;
    }

    /**
     * Check if agent can view in morning slot
     */
    public function canViewInMorning()
    {
        return $this->is_available && $this->morning_available;
    }

    /**
     * Check if agent can view in afternoon slot
     */
    public function canViewInAfternoon()
    {
        return $this->is_available && $this->afternoon_available;
    }

    /**
     * Check if agent can view in evening slot
     */
    public function canViewInEvening()
    {
        return $this->is_available && $this->evening_available;
    }

    /**
     * Check if agent can accommodate a viewing on this date/time
     */
    public function canAccommodateViewing($timeSlot)
    {
        return match ($timeSlot) {
            'morning' => $this->canViewInMorning(),
            'afternoon' => $this->canViewInAfternoon(),
            'evening' => $this->canViewInEvening(),
            default => false,
        };
    }

    /**
     * Get number of available slots
     */
    public function getAvailableSlotCount()
    {
        return count($this->available_slots);
    }

    /**
     * Make agent available for all day
     */
    public function setAvailableAllDay()
    {
        $this->update([
            'is_available' => true,
            'morning_available' => true,
            'afternoon_available' => true,
            'evening_available' => true,
        ]);
    }

    /**
     * Make agent unavailable
     */
    public function setUnavailable()
    {
        $this->update([
            'is_available' => false,
            'morning_available' => false,
            'afternoon_available' => false,
            'evening_available' => false,
        ]);
    }

    /**
     * Get current viewings count for this date
     */
    public function getCurrentViewingCount()
    {
        // TODO: Implement when Viewing model is created
        return 0;
    }

    /**
     * Check if agent can accept more viewings on this date
     */
    public function canAcceptMoreViewings()
    {
        return $this->getCurrentViewingCount() < $this->max_viewings_per_day;
    }
}