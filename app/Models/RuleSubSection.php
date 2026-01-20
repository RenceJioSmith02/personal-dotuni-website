<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RuleSubSection extends Model
{
    use SoftDeletes;

    protected $fillable = ['section_id', 'number', 'body', 'sort_order', 'updated_by'];

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
