<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'property_id',
        'sender_id',
        'recipient_id',
        'message_content',
        'attachment_path',
        'attachment_name',
        'attachment_type',
        'is_read',
        'read_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationships
     */

    /**
     * Get the property associated with this message
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the sender of this message
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the recipient of this message
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    /**
     * Scopes
     */

    /**
     * Scope to get unread messages
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope to get read messages
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope to get messages between two users
     */
    public function scopeBetweenUsers($query, $userId1, $userId2)
    {
        return $query->where(function ($q) use ($userId1, $userId2) {
            $q->where('sender_id', $userId1)
              ->where('recipient_id', $userId2);
        })->orWhere(function ($q) use ($userId1, $userId2) {
            $q->where('sender_id', $userId2)
              ->where('recipient_id', $userId1);
        });
    }

    /**
     * Methods
     */

    /**
     * Mark message as read
     */
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Mark message as unread
     */
    public function markAsUnread()
    {
        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Check if message is sent by user
     */
    public function isSentBy($userId)
    {
        return $this->sender_id === $userId;
    }

    /**
     * Check if message is received by user
     */
    public function isReceivedBy($userId)
    {
        return $this->recipient_id === $userId;
    }
}