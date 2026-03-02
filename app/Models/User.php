<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'profile_photo_path',
        'address',                 // ← ADD
        'city',                    // ← ADD
        'province',                // ← ADD
        'postal_code',             // ← ADD
        'date_of_birth',           // ← ADD
        'role',
        'status',
        'preferences',             // ← ADD (JSON)
        'two_factor_secret',       // ← ADD
        'last_login_at',
        'last_seen_at',
        'email_verified_at',
        'email_verification_token',
        'google_access_token',
        'google_refresh_token',
        'google_token_expires_at',
        'google_calendar_id',
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',       // ← ADD
        'google_access_token',
        'google_refresh_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'preferences' => 'json',   // ← ADD
        'google_token_expires_at' => 'datetime',
    ];

    /**
     * Relationships
     */

    public function agent()
    {
        return $this->hasOne('App\Models\Agent');
    }

    public function sentMessages()
    {
        return $this->hasMany('App\Models\Message', 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany('App\Models\Message', 'recipient_id');
    }

    public function viewings()
    {
        return $this->hasMany('App\Models\Viewing', 'buyer_id');
    }

    public function documents()
    {
        return $this->hasMany('App\Models\Document');
    }

    public function documentSignings()
    {
        return $this->hasMany('App\Models\DocumentSigning', 'signer_id');
    }

    public function auditLogs()
    {
        return $this->hasMany('App\Models\AuditLog');
    }

    public function notifications()
    {
        return $this->hasMany('App\Models\Notification');
    }

    public function savedProperties()
    {
        return $this->belongsToMany('App\Models\Property', 'saved_properties', 'user_id', 'property_id')
                    ->withPivot('created_at');
    }

    /**
     * Helper Methods
     */

    public function isBuyer()
    {
        return $this->role === 'buyer';
    }

    public function isAgent()
    {
        return $this->role === 'agent';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isSuspended()
    {
        return $this->status === 'suspended';
    }

    public function updateLastLogin()
    {
        $this->update(['last_login_at' => now()]);
    }

    public function suspend($reason = null)
    {
        $this->update(['status' => 'suspended']);
    }

    public function activate()
    {
        $this->update(['status' => 'active']);
    }

    /**
     * Send the password reset notification with branded email.
     */
    public function sendPasswordResetNotification($token)
    {
        Mail::to($this->email)->send(new ResetPasswordMail($token, $this->email, $this->name));
    }
}