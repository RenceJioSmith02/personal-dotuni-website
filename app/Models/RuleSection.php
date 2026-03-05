<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RuleSection extends Model
{

    protected $fillable = [
        'article_id',
        'number',
        'body',
        'sort_order',
        'is_active',
        'deleted_at', 
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function article()
    {
        return $this->belongsTo(RuleArticle::class, 'article_id');
    }

    public function subSections()
    {
        return $this->hasMany(RuleSubSection::class, 'section_id')->orderBy('sort_order');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
