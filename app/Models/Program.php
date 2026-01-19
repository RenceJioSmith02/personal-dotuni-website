<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Append computed attributes
     */
    protected $appends = [
        'image_url',
    ];

    /**
     * Accessors
     */

    public function getImageUrlAttribute()
    {
        return $this->asset?->getPublicUrl();
    }


    /**
     * Relationships
     */

    // Program image asset
    public function asset()
    {
        return $this->belongsTo(Asset::class, 'program_asset_id');
    }

    // A program has many requirement summaries
    public function requirements()
    {
        return $this->hasMany(ProgramRequirement::class);
    }

    public function requirementCategories()
    {
        return $this->hasManyThrough(
            ProgramRequirementCategory::class,
            ProgramRequirement::class,
            'program_id',
            'id',
            'id',
            'requirement_category_id'
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
}
