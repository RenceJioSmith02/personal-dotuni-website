<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CoursePrerequisiteController extends Controller
{
    /**
     * List prerequisites of a course (DataTables)
     */
    public function index(Course $course, Request $request)
    {
        if ($request->ajax()) {
            return datatables()
                ->of($course->prerequisites()->select('courses.*'))
                ->addColumn('action', function ($c) use ($course) {
                    return view(
                        'admin.courses.prerequisites.partials.actions',
                        compact('course', 'c')
                    );
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.courses.prerequisites.index', compact('course'));
    }

    /**
     * Attach a prerequisite
     */
    public function store(Request $request, Course $course)
    {
        $request->validate([
            'prerequisite_course_id' => 'required|exists:courses,id|different:' . $course->id,
        ]);

        // Prevent duplicates
        if ($course->prerequisites()->where('prerequisite_course_id', $request->prerequisite_course_id)->exists()) {
            return response()->json(['message' => 'Already added'], 422);
        }

        $course->prerequisites()->attach($request->prerequisite_course_id);

        return response()->json(['success' => true]);
    }

    /**
     * Remove a prerequisite
     */
    public function destroy(Course $course, $prerequisiteId)
    {
        $course->prerequisites()->detach($prerequisiteId);
        return response()->json(['success' => true]);
    }
}
