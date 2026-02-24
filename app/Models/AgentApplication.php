<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentApplication extends Model
{
    protected $fillable = [
        'user_id',
        'applicant_name',
        'license_number',
        'prc_id',
        'prc_license_photo',
        'license_expiry_date',
        'company_name',
        'company_address',
        'accreditation',
        'bio',
        'status', // pending, approved, rejected
        'rejection_reason',
        'ai_verification_result',
        'ai_decision',
        'applied_at',
        'verified_at',
        'rejected_at',
    ];

    protected $casts = [
        'applied_at' => 'datetime',
        'verified_at' => 'datetime',
        'rejected_at' => 'datetime',
        'license_expiry_date' => 'date',
    ];

    /**
     * Relationship: Application belongs to User
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * Scope: Get pending applications
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Get verified applications
     */
    public function scopeVerified($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope: Get rejected applications
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Approve the application
     */
    public function approve()
    {
        $this->update([
            'status' => 'approved',
            'verified_at' => now(),
        ]);

        // Update user role to agent
        $this->user->update(['role' => 'agent']);

        return $this;
    }

    /**
     * Reject the application
     */
    public function reject($reason)
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'rejected_at' => now(),
        ]);

        return $this;
    }
}