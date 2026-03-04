<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{

    protected $table = 'forms';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'form_category_id',
        'asset_id',
        'sort_order',
        'deleted_at',
        'is_active',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Append computed attributes
     */
    protected $appends = [
        'file_url',
    ];

    /**
     * Accessors
     */

    // Get full public URL of the attached file
    public function getFileUrlAttribute()
    {
        return $this->asset?->getPublicUrl();
    }

    /**
     * Relationships
     */

    // Form belongs to a category
    public function category()
    {
        return $this->belongsTo(FormCategory::class, 'form_category_id');
    }

    // File asset (PDF, DOCX, etc.)
    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    // Who last updated this form
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
