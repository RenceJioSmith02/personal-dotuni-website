<?php

namespace App\Services\Academic;

use App\Models\Course;
use Illuminate\Support\Facades\DB;
use DomainException;
use Exception;
use Illuminate\Http\Request;

class CourseService
{

    public function list()
    {
        return Course::all();
    }
    /**
     * Get all courses
     */

    public function datatable(Request $request)
    {
        $query = Course::query();

        $total = $query->count();

        /* ======================
         * SEARCH
         * ====================== */
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('prerequisite', 'like', "%{$search}%");
            });
        }

        $filtered = $query->count();

        /* ======================
         * ORDERING
         * ====================== */
        $columns = [
            'code',
            'title',
            'description',
            'units',
            'prerequisite',
            'created_at',
            'updated_at',
            'is_active'
        ];

        $orderColumn = $columns[$request->input('order.0.column', 0)] ?? 'code';
        $orderDir = $request->input('order.0.dir', 'asc');

        $query->orderBy($orderColumn, $orderDir);

        /* ======================
         * PAGINATION
         * ====================== */
        $data = $query
            ->skip($request->start)
            ->take($request->length)
            ->get();

        /* ======================
         * RESPONSE
         * ====================== */
        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data->map(fn($c) => [
                'code' => $c->code,
                'title' => $c->title,
                'description' => $c->description,
                'units' => $c->units,
                'prerequisite' => $c->prerequisite,
                'created_at' => $c->created_at->toDateTimeString(),
                'updated_at' => $c->updated_at->toDateTimeString(),
                'status' => $c->is_active,
                'actions' => view(
                    'admin.academic.courses.partials.actions',
                    compact('c')
                )->render()
            ])
        ]);
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

                // Check if used in program_courses
                if ($course->programCourses()->exists()) {
                    throw new DomainException(
                        "Cannot delete '{$course->code} - {$course->title}'. It is already assigned to a program."
                    );
                }

                $course->delete();
            });
        } catch (DomainException $e) {
            throw $e;
        } catch (Exception $e) {
            report($e);
            throw new DomainException('Failed to delete course: ' . $e->getMessage());
        }
    }

}
