<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Announcement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'article_body',
        'slug',
        'seo_title',
        'seo_description',
        'status',
        'visibility',
        'layout',
        'author_id',
        'publish_start',
        'publish_end',
        'updated_by',
    ];

    protected $casts = [
        'publish_start' => 'datetime',
        'publish_end'   => 'datetime',
    ];

    /* =====================
       RELATIONSHIPS
    ===================== */

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function assets()
    {
        return $this->belongsToMany(Asset::class, 'announcement_assets')
                    ->withPivot([
                        'id',
                        'caption',
                        'is_thumbnail',
                        'is_cover',
                        'sort_order',
                    ])
                    ->withTimestamps()
                    ->orderBy('announcement_assets.sort_order');
    }

    public function attachmentRows()
    {
        return $this->hasMany(AnnouncementAsset::class)
            ->orderBy('sort_order');
    }


    public function thumbnail()
    {
        return $this->assets()
                    ->wherePivot('is_thumbnail', true)
                    ->first();
    }

    public function cover()
    {
        return $this->assets()
                    ->wherePivot('is_cover', true)
                    ->first();
    }
}
