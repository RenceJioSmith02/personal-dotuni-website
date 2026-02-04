<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramRequirement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'program_id',
        'requirement_category_id',
        'required_units',
        'ms',
        'mps',
        'updated_by',
    ];


    public function category()
    {
        return $this->belongsTo(ProgramRequirementCategory::class, 'requirement_category_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function programCourses()
    {
        // Get courses linked to this requirement in the same program
        return $this->hasMany(\App\Models\ProgramCourse::class, 'requirement_category_id', 'requirement_category_id')
            ->where('program_id', $this->program_id);
    }



}
