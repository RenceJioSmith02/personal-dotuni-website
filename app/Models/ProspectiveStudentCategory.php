<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProspectiveStudentCategory extends Model
{

    protected $table = 'prospective_student_categories';

    protected $fillable = [
        'name',
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
       RELATIONSHIPS
    =============================== */

    public function items()
    {
        return $this->hasMany(ProspectiveStudentItem::class, 'category_id')
            ->orderBy('sort_order');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
