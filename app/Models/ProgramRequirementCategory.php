<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramRequirementCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'sort_order',
        'updated_by',
    ];

    public function programRequirements()
    {
        return $this->hasMany(ProgramRequirement::class, 'requirement_category_id');
    }

    public function programCourses()
    {
        return $this->hasMany(ProgramCourse::class, 'requirement_category_id');
    }

    public function programs()
    {
        return $this->belongsToMany(
            Program::class,
            'program_requirements'
        );
    }

}
