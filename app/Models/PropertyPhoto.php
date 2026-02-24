<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyPhoto extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'property_id',
        'photo_path',
        'photo_url',
        'quality_score',
        'is_cover',
        'approval_status',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_cover' => 'boolean',
        'quality_score' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Approval status enumeration
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * Relationships
     */

    /**
     * Get the property associated with this photo
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Accessors & Mutators
     */

    /**
     * Get approval status display
     */
    public function getApprovalStatusNameAttribute()
    {
        return match ($this->approval_status) {
            self::STATUS_PENDING => 'Pending Review',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            default => 'Unknown',
        };
    }

    /**
     * Check if photo is approved
     */
    public function isApproved()
    {
        return $this->approval_status === self::STATUS_APPROVED;
    }

    /**
     * Check if photo is pending
     */
    public function isPending()
    {
        return $this->approval_status === self::STATUS_PENDING;
    }

    /**
     * Check if photo is rejected
     */
    public function isRejected()
    {
        return $this->approval_status === self::STATUS_REJECTED;
    }

    /**
     * Set this photo as cover
     */
    public function setCover()
    {
        // Remove cover from other photos in this property
        PropertyPhoto::where('property_id', $this->property_id)
            ->where('id', '!=', $this->id)
            ->update(['is_cover' => false]);

        // Set this as cover
        $this->update(['is_cover' => true]);
    }

    /**
     * Approve the photo
     */
    public function approve()
    {
        $this->update(['approval_status' => self::STATUS_APPROVED]);
    }

    /**
     * Reject the photo
     */
    public function reject()
    {
        $this->update(['approval_status' => self::STATUS_REJECTED]);
    }
}