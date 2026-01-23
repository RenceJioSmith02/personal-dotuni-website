<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormCategory extends Model
{
    use SoftDeletes;

    protected $table = 'form_categories';

    protected $fillable = [
        'name',
        'slug',
        'sort_order',
        'is_active',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */

    // Category has many forms
    public function forms()
    {
        return $this->hasMany(Form::class, 'form_category_id');
    }

    // Who last updated this category
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
