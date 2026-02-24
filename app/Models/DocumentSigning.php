<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentSigning extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'document_id',
        'signer_id',
        'signature_path',
        'signature_url',
        'signature_type',
        'signed_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'signed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationships
     */

    /**
     * Get the document that was signed
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Get the user who signed the document
     */
    public function signer()
    {
        return $this->belongsTo(User::class, 'signer_id');
    }

    /**
     * Scopes
     */

    /**
     * Scope to filter by document
     */
    public function scopeByDocument($query, $documentId)
    {
        return $query->where('document_id', $documentId);
    }

    /**
     * Scope to filter by signer
     */
    public function scopeBySigner($query, $userId)
    {
        return $query->where('signer_id', $userId);
    }

    /**
     * Scope to order by most recent
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('signed_at', 'desc');
    }

    /**
     * Accessors & Mutators
     */

    /**
     * Get signer's full name
     */
    public function getSignerNameAttribute()
    {
        return $this->signer?->name ?? 'Unknown';
    }

    /**
     * Get signing time formatted
     */
    public function getSignedAtFormattedAttribute()
    {
        return $this->signed_at?->format('M d, Y H:i A') ?? 'Not signed';
    }

    /**
     * Methods
     */

    /**
     * Get device information
     */
    public function getDeviceDetails()
    {
        if (!$this->device_info) {
            return null;
        }

        return json_decode($this->device_info, true);
    }

    /**
     * Check if signature was done from mobile
     */
    public function isFromMobile()
    {
        $deviceInfo = $this->getDeviceDetails();
        if (!$deviceInfo) {
            return false;
        }

        return isset($deviceInfo['mobile']) && $deviceInfo['mobile'] === true;
    }

    /**
     * Get IP address location (if available from future implementation)
     */
    public function getIpLocation()
    {
        // TODO: Implement IP location lookup
        return $this->ip_address;
    }

    /**
     * Get document name
     */
    public function getDocumentNameAttribute()
    {
        return $this->document?->document_name ?? 'Unknown Document';
    }
}