<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProspectiveStudentItem extends Model
{

    protected $table = 'prospective_student_items';

    protected $fillable = [
        'category_id',
        'content',
        'sort_order',
        'is_active',
        'deleted_at',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /* ===============================
       RELATIONSHIPS
    =============================== */

    public function category()
    {
        return $this->belongsTo(ProspectiveStudentCategory::class, 'category_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
