<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProspectiveStudentItem extends Model
{
    use SoftDeletes;

    protected $table = 'prospective_student_items';

    protected $fillable = [
        'category_id',
        'content',
        'sort_order',
        'is_active',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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
