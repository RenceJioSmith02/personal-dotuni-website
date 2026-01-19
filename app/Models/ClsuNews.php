<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClsuNews extends Model
{
    use SoftDeletes;

    protected $table = 'clsu_news';

    protected $fillable = [
        'title',
        'url',
        'description',
        'thumbnail_asset_id',
        'sort_order',
        'is_active',
        'updated_by',
    ];

    public function thumbnail()
    {
        return $this->belongsTo(Asset::class, 'thumbnail_asset_id');
    }
}
