<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinkageCategory extends Model
{

    protected $table = 'linkage_categories';

    protected $fillable = [
        'name',
        'sort_order',
        'is_active',
        'deleted_at', 
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relationships
     */

    // A category has many linkages
    public function linkages()
    {
        return $this->hasMany(Linkage::class, 'category_id');
    }

    // Who last updated this category
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
