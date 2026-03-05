<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RuleSubSection extends Model
{

    protected $fillable = [
        'section_id',
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

    public function section()
    {
        return $this->belongsTo(RuleSection::class, 'section_id');
    }

    public function clauses()
    {
        return $this->hasMany(RuleClause::class, 'sub_section_id')->orderBy('sort_order');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
