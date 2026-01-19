<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FaqAnswer extends Model
{
    use SoftDeletes;

    protected $table = 'faqs_answers';

    protected $fillable = [
        'faq_id',
        'answer',
        'is_active',
        'updated_by',
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
