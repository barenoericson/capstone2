<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'agent_id',
        'title',
        'description',
        'address',
        'city',
        'province',
        'postal_code',
        'latitude',
        'longitude',
        'property_type',
        'price',
        'bedrooms',
        'bathrooms',
        'lot_size',
        'floor_area',
        'year_built',
        'status',
        'featured',
        'view_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'integer',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'lot_size' => 'decimal:2',
        'floor_area' => 'decimal:2',
        'year_built' => 'integer',
        'featured' => 'boolean',
        'view_count' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',

    ];

    /**
     * Property type enumeration
     */
    const TYPE_HOUSE = 'house';
    const TYPE_CONDO = 'condo';
    const TYPE_APARTMENT = 'apartment';
    const TYPE_COMMERCIAL = 'commercial';

    /**
     * Status enumeration
     */
    const STATUS_AVAILABLE = 'available';
    const STATUS_SOLD = 'sold';
    const STATUS_RENTED = 'rented';
    const STATUS_UNLISTED = 'unlisted';

    /**
     * Relationships
     */

    /**
     * Get the agent who listed this property
     */
    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Get all photos for this property
     */
    public function photos()
    {
        return $this->hasMany(PropertyPhoto::class);
    }

    /**
     * Get the cover photo
     */
    public function coverPhoto()
    {
        return $this->hasOne(PropertyPhoto::class)->where('is_cover', true);
    }

    /**
     * Get all messages for this property
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get all viewings for this property
     */
    public function viewings()
    {
        return $this->hasMany(Viewing::class);
    }

    /**
     * Get all documents for this property
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get all reviews for this property
     */
    public function reviews()
    {
        return $this->hasMany(AgentReview::class);
    }

    /**
     * Accessors & Mutators
     */

    /**
     * Get property type display
     */
    public function getPropertyTypeNameAttribute()
    {
        return match ($this->property_type) {
            self::TYPE_HOUSE => 'House',
            self::TYPE_CONDO => 'Condo',
            self::TYPE_APARTMENT => 'Apartment',
            self::TYPE_COMMERCIAL => 'Commercial',
            default => 'Unknown',
        };
    }

    /**
     * Get status display
     */
    public function getStatusNameAttribute()
    {
        return match ($this->status) {
            self::STATUS_AVAILABLE => 'Available',
            self::STATUS_SOLD => 'Sold',
            self::STATUS_RENTED => 'Rented',
            self::STATUS_UNLISTED => 'Unlisted',
            default => 'Unknown',
        };
    }

    /**
     * Format price as currency
     */
    public function getFormattedPriceAttribute()
    {
        return '₱' . number_format($this->price, 0);
    }

    /**
     * Get featured badge
     */
    public function getFeaturedBadgeAttribute()
    {
        return $this->featured ? 'Featured' : null;
    }

    /**
     * Scopes
     */

    /**
     * Scope to filter by city
     */
    public function scopeByCity($query, $city)
    {
        return $query->where('city', $city);
    }

    /**
     * Scope to filter by property type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('property_type', $type);
    }

    /**
     * Scope to filter by price range
     */
    public function scopeByPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('price', [$minPrice, $maxPrice]);
    }

    /**
     * Scope to filter available properties
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    /**
     * Scope to get featured properties
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    /**
     * Scope to get newest listings first
     */
    public function scopeNewest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope to search by keyword
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where('title', 'like', "%{$keyword}%")
            ->orWhere('description', 'like', "%{$keyword}%")
            ->orWhere('address', 'like', "%{$keyword}%");
    }

    /**
     * Methods
     */

    /**
     * Increment view count
     */
    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    /**
     * Check if property is available
     */
    public function isAvailable()
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    /**
     * Mark property as sold
     */
    public function markAsSold()
    {
        $this->update(['status' => self::STATUS_SOLD]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'property_sold',
            'action_type' => 'update',
            'model_type' => 'Property',
            'model_id' => $this->id,
            'description' => "Property {$this->title} marked as sold",
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Mark property as rented
     */
    public function markAsRented()
    {
        $this->update(['status' => self::STATUS_RENTED]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'property_rented',
            'action_type' => 'update',
            'model_type' => 'Property',
            'model_id' => $this->id,
            'description' => "Property {$this->title} marked as rented",
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Unlist property
     */
    public function unlist()
    {
        $this->update(['status' => self::STATUS_UNLISTED]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'property_unlisted',
            'action_type' => 'update',
            'model_type' => 'Property',
            'model_id' => $this->id,
            'description' => "Property {$this->title} unlisted",
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Get all approved photos
     */
    public function approvedPhotos()
    {
        return $this->photos()->where('approval_status', PropertyPhoto::STATUS_APPROVED);
    }
}