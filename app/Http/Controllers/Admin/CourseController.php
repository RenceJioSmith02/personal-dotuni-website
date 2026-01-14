<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class CourseController extends Controller
{
    /**
     * Display a listing of courses.
     */
    public function index()
    {
        $courses = Course::all();
        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new course (AJAX modal).
     */
    // public function create()
    // {
    //     if (request()->ajax()) {
    //         return view('admin.courses.partials.course-form'); // form only
    //     }

    //     return redirect()->route('admin.courses.index');
    // }

    /**
     * Store a newly created course.
     */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                Rule::unique('courses')->whereNull('deleted_at')
            ],
            'code' => [
                'required',
                'string',
                Rule::unique('courses')->whereNull('deleted_at')
            ],
            'description' => 'required|string',
            'units' => 'required|integer|min:0',
            'prerequisite' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        // CHECK SOFT-DELETED RECORD
        $existing = Course::withTrashed()
            ->where('code', $validated['code'])
            ->first();

        if ($existing) {
            $existing->restore();
            $existing->update($validated);

            return response()->json([
                'message' => 'Course restored successfully',
                'course' => $existing
            ]);
        }

        $course = Course::create($validated);

        return response()->json([
            'message' => 'Course created successfully',
            'course' => $course
        ], 201);
    }


    /**
     * Show the form for editing the specified course (AJAX modal).
     */
    public function edit(Course $course)
    {
        return response()->json($course);
    }


    /**
     * Update the specified course.
     */
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                Rule::unique('courses')
                    ->ignore($course->id)
                    ->whereNull('deleted_at'),
            ],
            'code' => [
                'required',
                'string',
                Rule::unique('courses')
                    ->ignore($course->id)
                    ->whereNull('deleted_at'),
            ],
            'description' => 'required|string',
            'units' => 'required|integer|min:0',
            'prerequisite' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        // HARD SAFETY CHECK AGAINST DB UNIQUE INDEX
        $conflict = Course::withTrashed()
            ->where('code', $validated['code'])
            ->where('id', '!=', $course->id)
            ->first();

        if ($conflict) {
            return response()->json([
                'message' => 'A course with this code already exists (including archived records) please add it again to restore.'
            ], 422);
        }

        $course->update($validated);

        return response()->json([
            'message' => 'Course updated successfully',
            'course' => $course
        ]);
    }

    /**
     * Remove the specified course.
     */


    public function destroy(Course $course, Request $request)
    {
        $course->delete();

        return response()->json([
            'message' => 'Course deleted successfully',
            'id' => $course->id
        ]);
    }

    
}
