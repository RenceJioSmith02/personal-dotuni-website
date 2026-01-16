<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LinkageCategory extends Model
{
    use SoftDeletes;

    protected $table = 'linkage_categories';

    protected $fillable = [
        'name',
        'sort_order',
        'updated_by',
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
