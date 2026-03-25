<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorLog extends Model
{
    protected $fillable = [
        'ip_address',
        'country',
        'city',
        'region',
        'url',
        'page_name',
        'user_agent',
        'browser',
        'os',
        'device_type',
        'referer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

