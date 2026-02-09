<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gallery extends Model
{
    use SoftDeletes;

    protected $table = 'gallery';

    protected $fillable = [
        'asset_id',
        'thumbnail_path',
        'sort_order',
        'updated_by',
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
