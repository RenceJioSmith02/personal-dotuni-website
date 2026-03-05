<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RuleArticle extends Model
{

    protected $fillable = [
        'number',
        'title',
        'sort_order',
        'is_active',
        'deleted_at', 
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function sections()
    {
        return $this->hasMany(RuleSection::class, 'article_id')->orderBy('sort_order');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
