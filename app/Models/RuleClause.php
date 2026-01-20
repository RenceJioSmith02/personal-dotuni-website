<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RuleClause extends Model
{
    use SoftDeletes;

    protected $fillable = ['sub_section_id', 'number', 'body', 'sort_order', 'updated_by'];

    public function subSection()
    {
        return $this->belongsTo(RuleSubSection::class, 'sub_section_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
