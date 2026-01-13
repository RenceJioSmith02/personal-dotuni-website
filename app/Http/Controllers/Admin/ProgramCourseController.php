<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;

class ProgramCourseController extends Controller
{
    public function index(Program $program)
    {
        $courses = $program->programCourses()
            ->with(['course', 'category'])
            ->orderBy('sort_order')
            ->get();

        return view('admin.programs.courses', compact('program', 'courses'));
    }

    public function store(Request $request, Program $program)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'requirement_category_id' => 'required|exists:program_requirement_categories,id',
            'sort_order' => 'nullable|integer',
        ]);

        $program->programCourses()->create([
            'course_id' => $request->course_id,
            'requirement_category_id' => $request->requirement_category_id,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(Program $program, $id)
    {
        $program->programCourses()->where('id', $id)->delete();
        return response()->json(['success' => true]);
    }
}
