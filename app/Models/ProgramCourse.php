<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramCourse extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'program_id',
        'requirement_category_id',
        'course_id',
        'sort_order',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function category()
    {
        return $this->belongsTo(
            ProgramRequirementCategory::class,
            'requirement_category_id'
        );
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
