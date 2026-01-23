<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DotuniNewsAsset extends Model
{
    protected $table = 'dotuni_news_assets';

    protected $fillable = [
        'news_id',
        'asset_id',
        'caption',
        'is_thumbnail',
        'is_cover',
        'sort_order',
    ];

    protected $casts = [
        'is_thumbnail' => 'boolean',
        'is_cover' => 'boolean',
    ];

    /* =========================
     | Relationships
     ========================= */

    public function news()
    {
        return $this->belongsTo(DotuniNews::class, 'news_id');
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }
}
