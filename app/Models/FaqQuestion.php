<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FaqQuestion extends Model
{
    use SoftDeletes;

    protected $table = 'faqs_questions';

    protected $fillable = [
        'question',
        'sort_order',
        'is_active',
        'updated_by',
    ];

    /* ===============================
       Relationships
    =============================== */

    public function answers()
    {
        return $this->hasMany(FaqAnswer::class, 'faq_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
