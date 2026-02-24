<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agent extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'real_estate_license_number',
        'prc_id_number',
        'accreditation',
        'company_name',
        'company_address',
        'bio',
        'verification_status',
        'verified_at',
        'verified_by_admin_id',
        'rejection_reason',
        'license_expiry_date',
        'listings_count',
        'average_rating',
        'total_reviews',
        'last_listing_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'verified_at' => 'datetime',
        'license_expiry_date' => 'date',
        'last_listing_date' => 'date',
        'average_rating' => 'decimal:2',
    ];

    /**
     * Verification status enumeration
     */
    const VERIFICATION_PENDING = 'pending';
    const VERIFICATION_VERIFIED = 'verified';
    const VERIFICATION_REJECTED = 'rejected';

    /**
     * Accreditation enumeration
     */
    const ACCREDITATION_REIN = 'REIN';
    const ACCREDITATION_PAMI = 'PAMI';
    const ACCREDITATION_OTHER = 'OTHER';

    /**
     * Relationships
     */

    /**
     * Get the user associated with this agent
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who verified this agent
     */
    public function verifiedByAdmin()
    {
        return $this->belongsTo(User::class, 'verified_by_admin_id');
    }

    /**
     * Get all properties listed by this agent
     */
    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    /**
     * Get all viewings for this agent's properties
     */
    public function viewings()
    {
        return $this->hasMany(Viewing::class);
    }

    /**
     * Get all documents uploaded by this agent
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get agent's availability calendar
     */
    public function availability()
    {
        return $this->hasMany(AgentAvailability::class);
    }

    /**
     * Get reviews for this agent
     */
    public function reviews()
    {
        return $this->hasMany(AgentReview::class);
    }

    /**
     * Accessors & Mutators
     */

    /**
     * Get verification status display
     */
    public function getVerificationStatusNameAttribute()
    {
        return match ($this->verification_status) {
            self::VERIFICATION_PENDING => 'Pending',
            self::VERIFICATION_VERIFIED => 'Verified',
            self::VERIFICATION_REJECTED => 'Rejected',
            default => 'Unknown',
        };
    }

    /**
     * Check if agent is verified
     */
    public function isVerified()
    {
        return $this->verification_status === self::VERIFICATION_VERIFIED;
    }

    /**
     * Check if verification is pending
     */
    public function isPending()
    {
        return $this->verification_status === self::VERIFICATION_PENDING;
    }

    /**
     * Check if verification was rejected
     */
    public function isRejected()
    {
        return $this->verification_status === self::VERIFICATION_REJECTED;
    }

    /**
     * Check if agent can post a new listing
     * 
     * Requirements:
     * - Agent must be verified
     * - License must not be expired
     * - Maximum 4 listings per day
     */
    public function canPostListing()
    {
        // Check if verified
        if ($this->verification_status !== self::VERIFICATION_VERIFIED) {
            return false;
        }

        // Check license expiry
        if ($this->license_expiry_date && $this->license_expiry_date->isPast()) {
            return false;
        }

        // Check daily limit (4 listings per day)
        $todayCount = $this->properties()
            ->whereDate('created_at', today())
            ->count();

        return $todayCount < 4;
    }

    /**
     * Get today's listing count
     */
    public function getTodayListingCountAttribute()
    {
        return $this->properties()
            ->whereDate('created_at', today())
            ->count();
    }

    /**
     * Verify the agent
     */
    public function verify($adminId)
    {
        $this->update([
            'verification_status' => self::VERIFICATION_VERIFIED,
            'verified_at' => now(),
            'verified_by_admin_id' => $adminId,
            'rejection_reason' => null,
        ]);

        // Log to audit
        AuditLog::create([
            'user_id' => $adminId,
            'action' => 'agent_verified',
            'action_type' => 'verify',
            'model_type' => 'Agent',
            'model_id' => $this->id,
            'description' => "Agent {$this->user->name} verified",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Reject agent verification
     */
    public function reject($adminId, $reason)
    {
        $this->update([
            'verification_status' => self::VERIFICATION_REJECTED,
            'rejection_reason' => $reason,
            'verified_by_admin_id' => $adminId,
        ]);

        // Log to audit
        AuditLog::create([
            'user_id' => $adminId,
            'action' => 'agent_rejected',
            'action_type' => 'reject',
            'model_type' => 'Agent',
            'model_id' => $this->id,
            'description' => "Agent {$this->user->name} rejected. Reason: {$reason}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Check if license is expiring soon (within 30 days)
     */
    public function isLicenseExpiringSoon()
    {
        return $this->license_expiry_date->diffInDays(now()) <= 30;
    }

    /**
     * Check if license has expired
     */
    public function isLicenseExpired()
    {
        return $this->license_expiry_date->isPast();
    }

    /**
     * Calculate and update average rating
     */
    public function updateAverageRating()
    {
        $reviews = $this->reviews()->get();

        if ($reviews->isEmpty()) {
            $this->update([
                'average_rating' => 0,
                'total_reviews' => 0,
            ]);
        } else {
            $this->update([
                'average_rating' => $reviews->avg('rating'),
                'total_reviews' => $reviews->count(),
            ]);
        }
    }

    /**
     * Update listings count
     */
    public function updateListingCount()
    {
        $this->update([
            'listings_count' => $this->properties()->count(),
        ]);
    }
}