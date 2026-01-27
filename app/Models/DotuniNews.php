<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DotuniNews extends Model
{
    use SoftDeletes;

    protected $table = 'dotuni_news';

    protected $fillable = [
        'title',
        'headline',
        'slug',
        'seo_title',
        'seo_description',
        'status',
        'visibility',
        'layout',
        'article_body', 
        'author_id',
        'published_at',
        'updated_by',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    protected $appends = [
        'thumbnail_url',
        'cover_url',
    ];

    /* =========================
     | Relationships
     ========================= */

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Pivot rows
     */
    public function attachments()
    {
        return $this->hasMany(DotuniNewsAsset::class, 'news_id')
            ->orderBy('sort_order');
    }

    /**
     * Assets (many-to-many)
     */
    public function assets()
    {
        return $this->belongsToMany(
            Asset::class,
            'dotuni_news_assets',
            'news_id',
            'asset_id'
        )
            ->withPivot(['caption', 'is_thumbnail', 'is_cover', 'sort_order'])
            ->orderBy('dotuni_news_assets.sort_order');
    }

    /**
     * Thumbnail asset (pivot-based)
     */
    public function thumbnail()
    {
        return $this->belongsToMany(
            Asset::class,
            'dotuni_news_assets',
            'news_id',
            'asset_id'
        )->wherePivot('is_thumbnail', true);
    }

    /**
     * Cover asset (pivot-based)
     */
    public function cover()
    {
        return $this->belongsToMany(
            Asset::class,
            'dotuni_news_assets',
            'news_id',
            'asset_id'
        )->wherePivot('is_cover', true);
    }

    /* =========================
     | Accessors
     ========================= */

    public function getThumbnailUrlAttribute()
    {
        return optional($this->thumbnail->first())?->getPublicUrl();
    }

    public function getCoverUrlAttribute()
    {
        return optional($this->cover->first())?->getPublicUrl();
    }
}
