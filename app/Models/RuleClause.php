<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RuleClause extends Model
{

    protected $fillable = [
        'sub_section_id',
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

    public function subSection()
    {
        return $this->belongsTo(RuleSubSection::class, 'sub_section_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
