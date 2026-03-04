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
        return Course::whereNull('deleted_at')->get();
    }

    public function datatable(Request $request)
    {
        // ✅ Show ALL records (active, inactive, archived)
        $query = Course::query();

        $total = $query->count();

        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('prerequisite', 'like', "%{$search}%");
            });
        }

        $filtered = $query->count();

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

        $data = $query
            ->skip($request->start)
            ->take($request->length)
            ->get();

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
                'archived' => !is_null($c->deleted_at), // ✅ Pass archive state
                'actions' => view(
                    'admin.academic.courses.partials.actions',
                    compact('c')
                )->render()
            ])
        ]);
    }

    public function create(array $data): Course
    {
        try {
            return DB::transaction(function () use ($data) {
                // ✅ Check for manually archived record with same code and restore it
                $existing = Course::where('code', $data['code'])->first();

                if ($existing && !is_null($existing->deleted_at)) {
                    $existing->update(array_merge($data, ['deleted_at' => null]));
                    return $existing;
                }

                return Course::create($data);
            });
        } catch (Exception $e) {
            report($e);
            throw new DomainException('Failed to create course: ' . $e->getMessage());
        }
    }

    public function update(Course $course, array $data): Course
    {
        try {
            return DB::transaction(function () use ($course, $data) {
                $conflict = Course::where('code', $data['code'])
                    ->where('id', '!=', $course->id)
                    ->first();

                if ($conflict) {
                    throw new DomainException(
                        'A course with this code already exists.'
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
     * ✅ Hard delete — permanently removes the record
     */
    public function delete(Course $course): void
    {
        try {
            DB::transaction(function () use ($course) {

                // ✅ Must be inactive before deleting
                if ($course->is_active) {
                    throw new DomainException(
                        "Cannot delete '{$course->code} - {$course->title}'. Please deactivate it before deleting."
                    );
                }

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

    /**
     * ✅ Archive — sets is_active = false + deleted_at = now()
     */
    public function archive(Course $course): Course
    {
        try {
            return DB::transaction(function () use ($course) {
                $course->update([
                    'is_active' => false,
                    'deleted_at' => now(),
                ]);
                return $course;
            });
        } catch (Exception $e) {
            report($e);
            throw new DomainException('Failed to archive course: ' . $e->getMessage());
        }
    }

    /**
     * ✅ Unarchive — sets is_active = true + deleted_at = null
     */
    public function unarchive(Course $course): Course
    {
        try {
            return DB::transaction(function () use ($course) {
                $course->update([
                    'is_active' => true,
                    'deleted_at' => null,
                ]);
                return $course;
            });
        } catch (Exception $e) {
            report($e);
            throw new DomainException('Failed to unarchive course: ' . $e->getMessage());
        }
    }
}