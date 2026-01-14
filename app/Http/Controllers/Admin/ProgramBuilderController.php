<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\ProgramRequirement;
use App\Models\ProgramCourse;
use Illuminate\Http\Request;

class ProgramBuilderController extends Controller
{
    public function show(Program $program)
    {
        $program->load([
            'requirements.category',
            'programCourses.course',
            'programCourses.category',
        ]);

        return view('admin.programs.builder', compact('program'));
    }

    /** AJAX: Add / Update Requirement */
    public function storeRequirement(Request $request, Program $program)
    {
        $request->validate([
            'requirement_category_id' => 'required|exists:program_requirement_categories,id',
            'required_units' => 'required|integer|min:0',
        ]);

        $requirement = $program->requirements()
            ->withTrashed()
            ->where('requirement_category_id', $request->requirement_category_id)
            ->first();

        if ($requirement) {
            // Restore if soft-deleted
            if ($requirement->trashed()) {
                $requirement->restore();
            }

            $requirement->update([
                'required_units' => $request->required_units,
                'updated_by' => auth()->id(),
            ]);
        } else {
            // Create if it never existed
            $requirement = $program->requirements()->create([
                'requirement_category_id' => $request->requirement_category_id,
                'required_units' => $request->required_units,
                'updated_by' => auth()->id(),
            ]);
        }

        return response()->json([
            'success' => true,
            'requirement' => $requirement->load('category'),
        ]);
    }


    /** AJAX: Delete Requirement */
    public function destroyRequirement(Program $program, ProgramRequirement $requirement)
    {
        $requirement->delete();
        return response()->json(['success' => true]);
    }




    /** AJAX: Add Course */
    public function storeCourse(Request $request, Program $program)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'requirement_category_id' => 'required|exists:program_requirement_categories,id',
            'sort_order' => 'nullable|integer',
        ]);

        $programCourse = $program->programCourses()
            ->withTrashed()
            ->where('course_id', $request->course_id)
            ->where('requirement_category_id', $request->requirement_category_id)
            ->first();

        if ($programCourse) {
            // Restore if soft-deleted
            if ($programCourse->trashed()) {
                $programCourse->restore();
            }

            $programCourse->update([
                'sort_order' => $request->sort_order ?? 0,
            ]);
        } else {
            // Create if it never existed
            $programCourse = $program->programCourses()->create([
                'course_id' => $request->course_id,
                'requirement_category_id' => $request->requirement_category_id,
                'sort_order' => $request->sort_order ?? 0,
            ]);
        }

        return response()->json([
            'success' => true,
            'course' => $programCourse->load('course', 'category'),
        ]);
    }


    /** AJAX: Delete Course */
    public function destroyCourse(Program $program, ProgramCourse $programCourse)
    {
        $programCourse->delete();
        return response()->json(['success' => true]);
    }
}
