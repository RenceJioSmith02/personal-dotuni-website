<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type',
        'title',
        'description',
        'total_units',
        'is_active',
        'updated_by',
        'program_asset_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // A program has many requirement summaries
    public function requirements()
    {
        return $this->hasMany(ProgramRequirement::class);
    }

    public function requirementCategories()
    {
        // Get categories through program_requirements
        return $this->hasManyThrough(
            ProgramRequirementCategory::class,
            ProgramRequirement::class,
            'program_id', // Foreign key on program_requirements
            'id',         // Foreign key on program_requirement_categories
            'id',         // Local key on programs
            'requirement_category_id' // Local key on program_requirements pointing to category
        );
    }


    // Program structure (courses)
    public function programCourses()
    {
        return $this->hasMany(ProgramCourse::class);
    }

    // Many-to-many shortcut
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'program_courses')
            ->withPivot('requirement_category_id', 'sort_order');
    }

    public function activeCourses()
    {
        return $this->courses()->where('courses.is_active', true);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'program_asset_id');
    }


}
