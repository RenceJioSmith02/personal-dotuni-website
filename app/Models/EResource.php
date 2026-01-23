<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EResource extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'e_resources';

    protected $fillable = [
        'name',
        'description',
        'link_url',
        'sort_order',
        'is_active',
        'updated_by',
    ];

    // Optional: cast is_active to boolean
    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relation to user who updated
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
