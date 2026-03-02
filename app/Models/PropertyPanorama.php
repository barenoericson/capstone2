<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyPanorama extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id',
        'image_path',
        'image_url',
        'room_name',
        'description',
        'sort_order',
        'hotspots',
        'default_yaw',
        'default_pitch',
        'hfov',
    ];

    protected $casts = [
        'sort_order'    => 'integer',
        'hotspots'      => 'array',
        'default_yaw'   => 'decimal:4',
        'default_pitch' => 'decimal:4',
        'hfov'          => 'decimal:4',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
