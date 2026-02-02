<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fee extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fees';

    // Columns that can be mass-assigned
    protected $fillable = [
        'asset_id',
        'title',
        'caption',
        'sort_order',
        'updated_by',
    ];

    /**
     * Relationship: Fee belongs to an Asset
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Relationship: Fee updated by a User
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
