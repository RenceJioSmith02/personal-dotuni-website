<?php

namespace App\Services\Academic;

use App\Models\Course;
use Illuminate\Support\Facades\DB;
use DomainException;
use Exception;

class CourseService
{
    /**
     * Get all courses
     */
    public function list()
    {
        return Course::all();
    }

    /**
     * Create a new course or restore soft-deleted one
     */
    public function create(array $data): Course
    {
        try {
            return DB::transaction(function () use ($data) {
                $existing = Course::withTrashed()
                    ->where('code', $data['code'])
                    ->first();

                if ($existing) {
                    $existing->restore();
                    $existing->update($data);
                    return $existing;
                }

                return Course::create($data);
            });
        } catch (Exception $e) {
            // Log the error for debugging or monitoring
            report($e);

            // Convert to domain-specific exception
            throw new DomainException('Failed to create course: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing course
     */
    public function update(Course $course, array $data): Course
    {
        try {
            return DB::transaction(function () use ($course, $data) {
                $conflict = Course::withTrashed()
                    ->where('code', $data['code'])
                    ->where('id', '!=', $course->id)
                    ->first();

                if ($conflict) {
                    throw new DomainException(
                        'A course with this code already exists (including archived records).'
                    );
                }

                $course->update($data);
                return $course;
            });
        } catch (Exception $e) {
            report($e);
            throw new DomainException('Failed to update course: ' . $e->getMessage());
        }
    }

    /**
     * Soft-delete a course
     */
    public function delete(Course $course): void
    {
        try {
            DB::transaction(function () use ($course) {
                $course->delete();
            });
        } catch (Exception $e) {
            report($e);
            throw new DomainException('Failed to delete course: ' . $e->getMessage());
        }
    }
}
