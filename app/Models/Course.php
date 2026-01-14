<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'title',
        'units',
        'description',
        'prerequisite',
        'is_active',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];


    // Appears in many programs
    public function programCourses()
    {
        return $this->hasMany(ProgramCourse::class);
    }

    public function programs()
    {
        return $this->belongsToMany(Program::class, 'program_courses')
            ->withPivot('requirement_category_id', 'sort_order');
    }

}
