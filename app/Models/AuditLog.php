<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'audit_logs';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'action',
        'action_type',
        'model_type',
        'model_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Action type enumeration
     */
    const ACTION_CREATE = 'create';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';
    const ACTION_VERIFY = 'verify';
    const ACTION_APPROVE = 'approve';
    const ACTION_REJECT = 'reject';

    /**
     * Relationships
     */

    /**
     * Get the user who performed the action
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
     * Scope to filter by action type
     */
    public function scopeByActionType($query, $actionType)
    {
        return $query->where('action_type', $actionType);
    }

    /**
     * Scope to filter by model type
     */
    public function scopeByModelType($query, $modelType)
    {
        return $query->where('model_type', $modelType);
    }

    /**
     * Scope to filter by model ID
     */
    public function scopeByModelId($query, $modelId)
    {
        return $query->where('model_id', $modelId);
    }

    /**
     * Scope to filter by action
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to get recent logs
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days))
                     ->orderBy('created_at', 'desc');
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
     * Get action type display
     */
    public function getActionTypeNameAttribute()
    {
        return match ($this->action_type) {
            self::ACTION_CREATE => 'Created',
            self::ACTION_UPDATE => 'Updated',
            self::ACTION_DELETE => 'Deleted',
            self::ACTION_VERIFY => 'Verified',
            self::ACTION_APPROVE => 'Approved',
            self::ACTION_REJECT => 'Rejected',
            default => 'Unknown',
        };
    }

    /**
     * Get user name who performed action
     */
    public function getUserNameAttribute()
    {
        return $this->user?->name ?? 'System';
    }

    /**
     * Methods
     */

    /**
     * Get what changed (comparison of old and new values)
     */
    public function getChanges()
    {
        if (!$this->old_values && !$this->new_values) {
            return null;
        }

        $changes = [];

        if ($this->new_values) {
            foreach ($this->new_values as $key => $newValue) {
                $oldValue = $this->old_values[$key] ?? null;
                if ($oldValue !== $newValue) {
                    $changes[$key] = [
                        'old' => $oldValue,
                        'new' => $newValue,
                    ];
                }
            }
        }

        return $changes ?: null;
    }

    /**
     * Get formatted log message
     */
    public function getFormattedMessage()
    {
        return "{$this->user_name} {$this->action_type_name} {$this->model_type} #{$this->model_id}";
    }

    /**
     * Check if action is a creation
     */
    public function isCreate()
    {
        return $this->action_type === self::ACTION_CREATE;
    }

    /**
     * Check if action is an update
     */
    public function isUpdate()
    {
        return $this->action_type === self::ACTION_UPDATE;
    }

    /**
     * Check if action is a deletion
     */
    public function isDelete()
    {
        return $this->action_type === self::ACTION_DELETE;
    }

    /**
     * Check if action is a verification
     */
    public function isVerify()
    {
        return $this->action_type === self::ACTION_VERIFY;
    }

    /**
     * Get browser info from user agent
     */
    public function getBrowserInfo()
    {
        if (!$this->user_agent) {
            return null;
        }

        // Simple browser detection
        if (strpos($this->user_agent, 'Chrome') !== false) {
            return 'Chrome';
        } elseif (strpos($this->user_agent, 'Firefox') !== false) {
            return 'Firefox';
        } elseif (strpos($this->user_agent, 'Safari') !== false) {
            return 'Safari';
        } elseif (strpos($this->user_agent, 'Edge') !== false) {
            return 'Edge';
        }

        return 'Unknown Browser';
    }
}