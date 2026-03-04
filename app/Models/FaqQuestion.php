<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaqQuestion extends Model
{

    protected $table = 'faqs_questions';

    protected $fillable = [
        'question',
        'sort_order',
        'is_active',
        'updated_by',
        'deleted_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
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
