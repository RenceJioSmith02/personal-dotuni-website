<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AnnouncementAsset extends Model
{
    use HasFactory;

    protected $table = 'announcement_assets';

    protected $fillable = [
        'announcement_id',
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

    public function announcement()
    {
        return $this->belongsTo(Announcement::class);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
