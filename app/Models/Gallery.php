<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{

    protected $table = 'gallery';

    protected $fillable = [
        'asset_id',
        'thumbnail_path',
        'sort_order',
        'is_active',
        'is_homepage_banner',
        'deleted_at', 
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_homepage_banner',
        'deleted_at' => 'datetime',
    ];


    protected $appends = [
        'image_url',
    ];

    /**
     * Accessors
     */
    public function getImageUrlAttribute()
    {
        return $this->asset?->getPublicUrl();
    }

    /**
     * Relationships
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
