<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClsuNews extends Model
{

    protected $table = 'clsu_news';

    protected $fillable = [
        'title',
        'url',
        'description',
        'thumbnail_asset_id',
        'sort_order',
        'is_active',
        'updated_by',
        'deleted_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function thumbnail()
    {
        return $this->belongsTo(Asset::class, 'thumbnail_asset_id');
    }
}
