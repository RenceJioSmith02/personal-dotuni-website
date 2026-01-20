<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProspectiveStudentCategory extends Model
{
    use SoftDeletes;

    protected $table = 'prospective_student_categories';

    protected $fillable = [
        'name',
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
