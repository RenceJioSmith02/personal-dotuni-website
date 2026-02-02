<?php

namespace App\Services\Academic;

use App\Models\Program;
use App\Models\ProgramRequirement;
use App\Models\ProgramCourse;
use Illuminate\Support\Facades\DB;
use DomainException;
use Exception;

class ProgramBuilderService
{
    /**
     * Load program with requirements and courses
     */
    public function loadProgram(Program $program): Program
    {
        return $program->load([
            'requirements.category',
            'programCourses.course',
            'programCourses.category',
        ]);
    }

    /**
     * Save or restore a program requirement
     */
    public function saveRequirement(Program $program, array $data): ProgramRequirement
    {
        try {
            return DB::transaction(function () use ($program, $data) {
                $requirement = ProgramRequirement::withTrashed()
                    ->where('program_id', $program->id)
                    ->where('requirement_category_id', $data['requirement_category_id'])
                    ->first();

                if ($requirement) {
                    $requirement->restore();
                    $requirement->update([
                        'required_units' => $data['required_units'],
                        'ms' => $data['ms'] ?? null,
                        'mps' => $data['mps'] ?? null,
                        'updated_by' => auth()->id(),
                    ]);
                } else {
                    $requirement = $program->requirements()->create([
                        ...$data,
                        'updated_by' => auth()->id(),
                    ]);
                }

                return $requirement;
            });
        } catch (Exception $e) {
            report($e);
            throw new DomainException('Failed to save requirement: ' . $e->getMessage());
        }
    }

    /**
     * Delete a program requirement
     */
    public function deleteRequirement(ProgramRequirement $requirement): void
    {
        try {
            DB::transaction(function () use ($requirement) {
                $requirement->delete();
            });
        } catch (Exception $e) {
            report($e);
            throw new DomainException('Failed to delete requirement: ' . $e->getMessage());
        }
    }

    /**
     * Save or restore a program course
     */
    public function saveCourse(Program $program, array $data): ProgramCourse
    {
        try {
            return DB::transaction(function () use ($program, $data) {
                $course = ProgramCourse::withTrashed()
                    ->where('program_id', $program->id)
                    ->where('course_id', $data['course_id'])
                    ->where('requirement_category_id', $data['requirement_category_id'])
                    ->first();

                if ($course) {
                    $course->restore();
                    $course->update(['sort_order' => $data['sort_order'] ?? 0]);
                } else {
                    $course = $program->programCourses()->create($data);
                }

                return $course;
            });
        } catch (Exception $e) {
            report($e);
            throw new DomainException('Failed to save course: ' . $e->getMessage());
        }
    }

    /**
     * Delete a program course
     */
    public function deleteCourse(ProgramCourse $programCourse): void
    {
        try {
            DB::transaction(function () use ($programCourse) {
                $programCourse->delete();
            });
        } catch (Exception $e) {
            report($e);
            throw new DomainException('Failed to delete course: ' . $e->getMessage());
        }
    }
}
