<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    use HasFactory;

    protected $table = 'fees';

    protected $fillable = [
        'asset_id',
        'title',
        'caption',
        'sort_order',
        'is_active',   
        'deleted_at', 
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}