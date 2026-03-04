<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{

    protected $fillable = [
        'code',
        'title',
        'units',
        'description',
        'prerequisite',
        'is_active',
        'deleted_at',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
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
