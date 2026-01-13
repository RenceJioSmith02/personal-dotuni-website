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
        'updated_by',
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
}
