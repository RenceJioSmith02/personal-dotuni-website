<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RuleArticle extends Model
{
    use SoftDeletes;

    protected $fillable = ['number', 'title', 'sort_order', 'updated_by'];

    public function sections()
    {
        return $this->hasMany(RuleSection::class, 'article_id')->orderBy('sort_order');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
