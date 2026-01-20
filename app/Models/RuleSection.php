<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RuleSection extends Model
{
    use SoftDeletes;

    protected $fillable = ['article_id', 'number', 'body', 'sort_order', 'updated_by'];

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
