<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgentReview extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'agent_id',
        'buyer_id',
        'property_id',
        'rating',
        'review_title',
        'review_content',
        'is_verified_transaction',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'rating' => 'integer',
        'is_verified_transaction' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Rating constants
     */
    const RATING_MIN = 1;
    const RATING_MAX = 5;

    /**
     * Relationships
     */

    /**
     * Get the agent being reviewed
     */
    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Get the buyer who left the review
     */
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Get the property related to this review
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
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
     * Scope to filter by buyer
     */
    public function scopeByBuyer($query, $buyerId)
    {
        return $query->where('buyer_id', $buyerId);
    }

    /**
     * Scope to filter by rating
     */
    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Scope to filter by minimum rating
     */
    public function scopeMinimumRating($query, $minRating)
    {
        return $query->where('rating', '>=', $minRating);
    }

    /**
     * Scope to filter by maximum rating
     */
    public function scopeMaximumRating($query, $maxRating)
    {
        return $query->where('rating', '<=', $maxRating);
    }

    /**
     * Scope to filter by rating range
     */
    public function scopeRatingRange($query, $minRating, $maxRating)
    {
        return $query->whereBetween('rating', [$minRating, $maxRating]);
    }

    /**
     * Scope to filter verified reviews
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified_transaction', true);
    }

    /**
     * Scope to filter reviews with content
     */
    public function scopeWithContent($query)
    {
        return $query->whereNotNull('review_content');
    }

    /**
     * Scope to order by rating
     */
    public function scopeOrderByRating($query, $direction = 'desc')
    {
        return $query->orderBy('rating', $direction);
    }

    /**
     * Scope to order by most recent
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Accessors & Mutators
     */

    /**
     * Get rating as stars
     */
    public function getRatingStarsAttribute()
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    /**
     * Get rating display text
     */
    public function getRatingDisplayAttribute()
    {
        return match ($this->rating) {
            5 => 'Excellent',
            4 => 'Very Good',
            3 => 'Good',
            2 => 'Fair',
            1 => 'Poor',
            default => 'No Rating',
        };
    }

    /**
     * Get buyer name
     */
    public function getBuyerNameAttribute()
    {
        return $this->buyer?->name ?? 'Anonymous';
    }

    /**
     * Get formatted review date
     */
    public function getFormattedDateAttribute()
    {
        return $this->created_at?->format('M d, Y') ?? 'Unknown';
    }

    /**
     * Methods
     */

    /**
     * Check if review is positive (4-5 stars)
     */
    public function isPositive()
    {
        return $this->rating >= 4;
    }

    /**
     * Check if review is negative (1-2 stars)
     */
    public function isNegative()
    {
        return $this->rating <= 2;
    }

    /**
     * Check if review is neutral (3 stars)
     */
    public function isNeutral()
    {
        return $this->rating === 3;
    }

    /**
     * Check if review is verified transaction
     */
    public function isVerifiedTransaction()
    {
        return $this->is_verified_transaction === true;
    }

    /**
     * Get review sentiment (positive, neutral, negative)
     */
    public function getSentiment()
    {
        if ($this->isPositive()) {
            return 'positive';
        } elseif ($this->isNegative()) {
            return 'negative';
        } else {
            return 'neutral';
        }
    }

    /**
     * Check if review has content
     */
    public function hasContent()
    {
        return !empty($this->review_content);
    }

    /**
     * Get truncated review content
     */
    public function getTruncatedContent($length = 100)
    {
        if (!$this->review_content) {
            return null;
        }

        if (strlen($this->review_content) <= $length) {
            return $this->review_content;
        }

        return substr($this->review_content, 0, $length) . '...';
    }

    /**
     * Mark as verified transaction
     */
    public function markAsVerified()
    {
        $this->update(['is_verified_transaction' => true]);
    }

    /**
     * Get helpful percentage (for future implementation)
     */
    public function getHelpfulPercentage()
    {
        // TODO: Implement when review_helpful table is created
        return 0;
    }
}