<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uploaded_by',
        'property_id',
        'buyer_id',
        'agent_id',
        'document_name',
        'document_type',
        'document_path',
        'document_url',
        'file_size',
        'mime_type',
        'annotations',
        'status',
        'signed_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'file_size' => 'integer',
        'annotations' => 'array',
        'signed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_SIGNED  = 'signed';

    /**
     * Document type enumeration
     */
    const TYPE_CONTRACT   = 'contract';
    const TYPE_AGREEMENT  = 'agreement';
    const TYPE_DISCLOSURE = 'disclosure';
    const TYPE_OTHER      = 'other';

    /**
     * Relationships
     */

    /**
     * Get the user who uploaded this document
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the property associated with this document
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the buyer associated with this document
     */
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Get the agent who uploaded this document
     */
    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Get all signings for this document
     */
    public function signings()
    {
        return $this->hasMany(DocumentSigning::class);
    }

    /**
     * Scopes
     */

    /**
     * Scope to filter by document type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('document_type', $type);
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
     * Scope to filter by property
     */
    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    /**
     * Scope to filter encrypted documents
     */
    public function scopeEncrypted($query)
    {
        return $query->where('encrypted', true);
    }

    /**
     * Accessors & Mutators
     */

    /**
     * Get document type display
     */
    public function getDocumentTypeNameAttribute()
    {
        return match ($this->document_type) {
            self::TYPE_CONTRACT => 'Contract',
            self::TYPE_AGREEMENT => 'Agreement',
            self::TYPE_DISCLOSURE => 'Disclosure',
            self::TYPE_OTHER => 'Other',
            default => 'Unknown',
        };
    }

    /**
     * Get file size in human readable format
     */
    public function getFileSizeFormattedAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Methods
     */

    /**
     * Check if document is encrypted
     */
    public function isEncrypted()
    {
        return $this->encrypted === true;
    }

    /**
     * Check if all required parties have signed
     */
    public function isFullySigned()
    {
        $requiredSigners = [
            $this->buyer_id,
            $this->agent_id,
        ];

        $signedBy = $this->signings()->pluck('signer_id')->unique()->toArray();

        return count(array_intersect($requiredSigners, $signedBy)) === count($requiredSigners);
    }

    /**
     * Get signatures
     */
    public function getSignatures()
    {
        return $this->signings()->with('signer')->get();
    }

    /**
     * Check if user has signed
     */
    public function isSignedBy($userId)
    {
        return $this->signings()->where('signer_id', $userId)->exists();
    }

    /**
     * Get signature for user
     */
    public function getSignatureByUser($userId)
    {
        return $this->signings()->where('signer_id', $userId)->first();
    }
}