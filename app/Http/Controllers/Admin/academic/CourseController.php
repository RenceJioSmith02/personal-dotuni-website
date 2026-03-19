<?php

namespace App\Http\Controllers\Admin\Academic;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\Academic\CourseService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use DomainException;

class CourseController extends Controller
{
    public function __construct(
        protected CourseService $service
    ) {
    }


    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->service->datatable($request);
        }

        return view('admin.academic.courses.index');
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                // Rule::unique('courses', 'title')->whereNull('deleted_at'),
            ],
            'code' => [
                'required',
                'string',
                // Rule::unique('courses', 'code')->whereNull('deleted_at'),
            ],
            'description' => 'required|string',
            'units' => 'required|integer|min:0',
            'prerequisite' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);


        $course = $this->service->create($validated);

        return response()->json([
            'message' => 'Course saved successfully',
            'course' => $course
        ]);
    }

    public function edit(Course $course)
    {
        return response()->json($course);
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                // Rule::unique('courses', 'title')
                //     ->ignore($course->id)
                //     ->whereNull('deleted_at'),
            ],
            'code' => [
                'required',
                'string',
                // Rule::unique('courses', 'code')
                //     ->ignore($course->id)
                //     ->whereNull('deleted_at'),
            ],
            'description' => 'required|string',
            'units' => 'required|integer|min:0',
            'prerequisite' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        try {
            $course = $this->service->update($course, $validated);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Course updated successfully',
            'course' => $course
        ]);
    }

    public function destroy(Course $course)
    {
        try {
            $this->service->delete($course);
        } catch (DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }

        return response()->json([
            'message' => 'Course deleted successfully',
            'id' => $course->id
        ]);
    }


    public function archive(Course $course)
    {
        try {
            $course = $this->service->archive($course);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Course archived successfully',
            'course' => $course
        ]);
    }

    public function unarchive(Course $course)
    {
        try {
            $course = $this->service->unarchive($course);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Course unarchived successfully',
            'course' => $course
        ]);
    }


}

