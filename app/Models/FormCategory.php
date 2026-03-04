<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormCategory extends Model
{

    protected $table = 'form_categories';

    protected $fillable = [
        'name',
        'slug',
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

    // Category has many forms
    // public function forms()
    // {
    //     return $this->hasMany(Form::class, 'form_category_id');
    // }

    public function forms()
    {
        return $this->hasMany(Form::class, 'form_category_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }


    // Who last updated this category
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
