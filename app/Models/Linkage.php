<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Linkage extends Model
{

    protected $table = 'linkages';

    protected $fillable = [
        'category_id',
        'title',
        'url',
        'description',
        'logo_asset_id',
        'sort_order',
        'is_active',
        'deleted_at',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Append computed attributes
     */
    protected $appends = [
        'image_url',
    ];

    /**
     * Accessors
     */

    // Get full public URL of logo image
    public function getImageUrlAttribute()
    {
        return $this->logo?->getPublicUrl();
    }


    /**
     * Relationships
     */

    // Linkage belongs to a category
    public function category()
    {
        return $this->belongsTo(LinkageCategory::class, 'category_id');
    }

    // Optional logo asset
    public function logo()
    {
        return $this->belongsTo(Asset::class, 'logo_asset_id');
    }

    // Who last updated this linkage
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
