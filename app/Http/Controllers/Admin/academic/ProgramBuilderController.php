<?php

namespace App\Http\Controllers\Admin\academic;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\ProgramRequirement;
use App\Models\ProgramCourse;
use Illuminate\Http\Request;

use Illuminate\Validation\Rule;

class ProgramBuilderController extends Controller
{
    public function show(Program $program)
    {
        $program->load([
            'requirements.category',
            'programCourses.course',
            'programCourses.category',
        ]);

        $categories = $program->requirementCategories()->orderBy('sort_order')->get();

        $breadcrumbs = [
            // ['name' => 'Home', 'url' => route('admin.dashboard')],
            ['name' => 'Programs', 'url' => route('admin.programs.index')],
            ['name' => 'Program Builder', 'url' => null],
        ];

        return view('admin.academic.programs.builder', compact('program', 'categories', 'breadcrumbs'));
    }



    /** AJAX: Add / Update Requirement */
    // public function storeRequirement(Request $request, Program $program)
    // {
    //     $request->validate([
    //         'requirement_category_id' => 'required|exists:program_requirement_categories,id',
    //         'required_units' => 'required|integer|min:0',
    //         'ms' => 'nullable|integer|min:0',
    //         'mps' => 'nullable|integer|min:0',
    //     ]);

    //     $requirement = $program->requirements()
    //         ->withTrashed()
    //         ->where('requirement_category_id', $request->requirement_category_id)
    //         ->first();

    //     if ($requirement) {
    //         // Restore if soft-deleted
    //         if ($requirement->trashed()) {
    //             $requirement->restore();
    //         }

    //         $requirement->update([
    //             'required_units' => $request->required_units,
    //             'updated_by' => auth()->id(),
    //             'ms' => $request->ms,
    //             'mps' => $request->mps,
    //         ]);
    //     } else {
    //         // Create if it never existed
    //         $requirement = $program->requirements()->create([
    //             'requirement_category_id' => $request->requirement_category_id,
    //             'required_units' => $request->required_units,
    //             'ms' => $request->ms,
    //             'mps' => $request->mps,
    //             'updated_by' => auth()->id(),
    //         ]);
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'requirement' => $requirement->load('category'),
    //     ]);
    // }


    public function storeRequirement(Request $request, Program $program)
    {
        $validated = $request->validate([
            'requirement_category_id' => [
                'required',
                'exists:program_requirement_categories,id',
                Rule::unique('program_requirements')
                    ->where(
                        fn($q) =>
                        $q->where('program_id', $program->id)
                            ->whereNull('deleted_at')
                    ),
            ],
            'required_units' => 'required|integer|min:0',
            'ms' => 'nullable|integer|min:0',
            'mps' => 'nullable|integer|min:0',
        ]);

        $requirement = ProgramRequirement::withTrashed()
            ->where('program_id', $program->id)
            ->where('requirement_category_id', $validated['requirement_category_id'])
            ->first();

        if ($requirement) {

            if ($requirement->trashed()) {
                $requirement->restore();
            }

            $requirement->update([
                'required_units' => $validated['required_units'],
                'ms' => $validated['ms'],
                'mps' => $validated['mps'],
                'updated_by' => auth()->id(),
            ]);

            $message = 'Requirement updated successfully';
        } else {

            $requirement = $program->requirements()->create([
                'requirement_category_id' => $validated['requirement_category_id'],
                'required_units' => $validated['required_units'],
                'ms' => $validated['ms'],
                'mps' => $validated['mps'],
                'updated_by' => auth()->id(),
            ]);

            $message = 'Requirement added successfully';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
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
    // public function storeCourse(Request $request, Program $program)
    // {
    //     $request->validate([
    //         'course_id' => 'required|exists:courses,id',
    //         'requirement_category_id' => 'required|exists:program_requirement_categories,id',
    //         'sort_order' => 'nullable|integer',
    //     ]);

    //     $programCourse = $program->programCourses()
    //         ->withTrashed()
    //         ->where('course_id', $request->course_id)
    //         ->where('requirement_category_id', $request->requirement_category_id)
    //         ->first();

    //     if ($programCourse) {
    //         // Restore if soft-deleted
    //         if ($programCourse->trashed()) {
    //             $programCourse->restore();
    //         }

    //         $programCourse->update([
    //             'sort_order' => $request->sort_order ?? 0,
    //         ]);
    //     } else {
    //         // Create if it never existed
    //         $programCourse = $program->programCourses()->create([
    //             'course_id' => $request->course_id,
    //             'requirement_category_id' => $request->requirement_category_id,
    //             'sort_order' => $request->sort_order ?? 0,
    //         ]);
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'course' => $programCourse->load('course', 'category'),
    //     ]);
    // }

    public function storeCourse(Request $request, Program $program)
    {
        $validated = $request->validate([
            'course_id' => [
                'required',
                'exists:courses,id',
                Rule::unique('program_courses')
                    ->where(
                        fn($q) =>
                        $q->where('program_id', $program->id)
                            ->where('requirement_category_id', $request->requirement_category_id)
                            ->whereNull('deleted_at')
                    ),
            ],
            'requirement_category_id' => 'required|exists:program_requirement_categories,id',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $programCourse = ProgramCourse::withTrashed()
            ->where('program_id', $program->id)
            ->where('course_id', $validated['course_id'])
            ->where('requirement_category_id', $validated['requirement_category_id'])
            ->first();

        if ($programCourse) {

            if ($programCourse->trashed()) {
                $programCourse->restore();
            }

            $programCourse->update([
                'sort_order' => $validated['sort_order'] ?? 0,
            ]);

            $message = 'Course updated successfully';
        } else {

            $programCourse = $program->programCourses()->create([
                'course_id' => $validated['course_id'],
                'requirement_category_id' => $validated['requirement_category_id'],
                'sort_order' => $validated['sort_order'] ?? 0,
            ]);

            $message = 'Course added successfully';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
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
