<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaqAnswer extends Model
{

    protected $table = 'faqs_answers';

    protected $fillable = [
        'faq_id',
        'answer',
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

    public function question()
    {
        return $this->belongsTo(FaqQuestion::class, 'faq_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
