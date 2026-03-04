<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EResource extends Model
{

    protected $table = 'e_resources';

    protected $fillable = [
        'name',
        'description',
        'link_url',
        'sort_order',
        'is_active',
        'updated_by',
        'deleted_at',
    ];

    // Optional: cast is_active to boolean
    protected $casts = [
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    // Relation to user who updated
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
