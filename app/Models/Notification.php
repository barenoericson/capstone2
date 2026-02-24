<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'notification_type',
        'title',
        'message',
        'related_model_type',
        'related_model_id',
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
     * Notification type enumeration
     */
    const TYPE_VIEWING_REQUEST = 'viewing_request';
    const TYPE_MESSAGE = 'message';
    const TYPE_DOCUMENT = 'document';
    const TYPE_SYSTEM = 'system';
    const TYPE_PROPERTY_UPDATE = 'property_update';
    const TYPE_AGENT_VERIFICATION = 'agent_verification';

    /**
     * Relationships
     */

    /**
     * Get the user who receives this notification
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scopes
     */

    /**
     * Scope to filter by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by notification type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('notification_type', $type);
    }

    /**
     * Scope to filter unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope to filter read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope to order by most recent
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope to get recent notifications
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days))
                     ->orderBy('created_at', 'desc');
    }

    /**
     * Scope to filter by date range
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Accessors & Mutators
     */

    /**
     * Get notification type display
     */
    public function getTypeDisplayAttribute()
    {
        return match ($this->notification_type) {
            self::TYPE_VIEWING_REQUEST => 'Viewing Request',
            self::TYPE_MESSAGE => 'New Message',
            self::TYPE_DOCUMENT => 'Document',
            self::TYPE_SYSTEM => 'System',
            self::TYPE_PROPERTY_UPDATE => 'Property Update',
            self::TYPE_AGENT_VERIFICATION => 'Agent Verification',
            default => 'Notification',
        };
    }

    /**
     * Get notification icon
     */
    public function getIconAttribute()
    {
        return match ($this->notification_type) {
            self::TYPE_VIEWING_REQUEST => '📅',
            self::TYPE_MESSAGE => '💬',
            self::TYPE_DOCUMENT => '📄',
            self::TYPE_SYSTEM => '⚙️',
            self::TYPE_PROPERTY_UPDATE => '🏠',
            self::TYPE_AGENT_VERIFICATION => '✓',
            default => '🔔',
        };
    }

    /**
     * Get formatted creation date
     */
    public function getFormattedDateAttribute()
    {
        return $this->created_at?->format('M d, Y H:i A') ?? 'Unknown';
    }

    /**
     * Get relative time (e.g., "2 hours ago")
     */
    public function getRelativeTimeAttribute()
    {
        return $this->created_at?->diffForHumans() ?? 'Unknown';
    }

    /**
     * Methods
     */

    /**
     * Check if notification is unread
     */
    public function isUnread()
    {
        return $this->is_read === false;
    }

    /**
     * Check if notification is read
     */
    public function isRead()
    {
        return $this->is_read === true;
    }

    /**
     * Mark as read
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
     * Mark as unread
     */
    public function markAsUnread()
    {
        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Check if notification is about a viewing
     */
    public function isViewingNotification()
    {
        return $this->notification_type === self::TYPE_VIEWING_REQUEST;
    }

    /**
     * Check if notification is about a message
     */
    public function isMessageNotification()
    {
        return $this->notification_type === self::TYPE_MESSAGE;
    }

    /**
     * Check if notification is about a document
     */
    public function isDocumentNotification()
    {
        return $this->notification_type === self::TYPE_DOCUMENT;
    }

    /**
     * Check if notification is a system notification
     */
    public function isSystemNotification()
    {
        return $this->notification_type === self::TYPE_SYSTEM;
    }

    /**
     * Check if notification is old (more than 7 days)
     */
    public function isOld()
    {
        return $this->created_at->diffInDays(now()) > 7;
    }

    /**
     * Get related model instance (if available)
     */
    public function getRelatedModel()
    {
        if (!$this->related_model_type || !$this->related_model_id) {
            return null;
        }

        $modelClass = 'App\\Models\\' . $this->related_model_type;

        if (!class_exists($modelClass)) {
            return null;
        }

        return $modelClass::find($this->related_model_id);
    }

    /**
     * Create notification for viewing request
     */
    public static function createViewingNotification($userId, $viewingId)
    {
        return self::create([
            'user_id' => $userId,
            'notification_type' => self::TYPE_VIEWING_REQUEST,
            'title' => 'New Viewing Request',
            'message' => 'You have a new property viewing request',
            'related_model_type' => 'Viewing',
            'related_model_id' => $viewingId,
        ]);
    }

    /**
     * Create notification for new message
     */
    public static function createMessageNotification($userId, $messageId)
    {
        return self::create([
            'user_id' => $userId,
            'notification_type' => self::TYPE_MESSAGE,
            'title' => 'New Message',
            'message' => 'You have received a new message',
            'related_model_type' => 'Message',
            'related_model_id' => $messageId,
        ]);
    }

    /**
     * Create notification for document
     */
    public static function createDocumentNotification($userId, $documentId, $action = 'uploaded')
    {
        return self::create([
            'user_id' => $userId,
            'notification_type' => self::TYPE_DOCUMENT,
            'title' => 'Document ' . ucfirst($action),
            'message' => "A document has been {$action}",
            'related_model_type' => 'Document',
            'related_model_id' => $documentId,
        ]);
    }

    /**
     * Create system notification
     */
    public static function createSystemNotification($userId, $title, $message)
    {
        return self::create([
            'user_id' => $userId,
            'notification_type' => self::TYPE_SYSTEM,
            'title' => $title,
            'message' => $message,
        ]);
    }
}