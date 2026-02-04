<?php

namespace App\Http\Controllers\Admin\academic;

use App\Models\Program;
use Illuminate\Http\Request;
use App\Models\ProgramCourse;
use Illuminate\Validation\Rule;
use App\Models\ProgramRequirement;

use App\Http\Controllers\Controller;
use App\Services\Academic\ProgramBuilderService;

class ProgramBuilderController extends Controller
{
    public function __construct(
        protected ProgramBuilderService $service
    ) {
    }

    public function show(Program $program)
    {
        $program = $this->service->loadProgram($program);


        $categories = $program->requirementCategories()->orderBy('sort_order')->get();

        $breadcrumbs = [
            // ['name' => 'Home', 'url' => route('admin.dashboard')],
            ['name' => 'Programs', 'url' => route('admin.programs.index')],
            ['name' => 'Program Builder', 'url' => null],
        ];

        return view('admin.academic.programs.builder', compact('program', 'categories', 'breadcrumbs'));
    }

    public function storeRequirement(Request $request, Program $program)
    {
        $validated = $request->validate([
            'requirement_category_id' => 'required|exists:program_requirement_categories,id',
            'required_units' => 'required|integer|min:0',
            'ms' => 'nullable|integer|min:0',
            'mps' => 'nullable|integer|min:0',
        ]);

        $requirement = $this->service->saveRequirement($program, $validated);

        return response()->json([
            'success' => true,
            'requirement' => $requirement->load('category'),
        ]);
    }

    public function destroyRequirement(Program $program, ProgramRequirement $requirement)
    {
        abort_unless(
            $requirement->program_id === $program->id,
            403,
            'Invalid program requirement'
        );

        try {
            $this->service->deleteRequirement($requirement);

            return response()->json([
                'success' => true,
                'message' => 'Requirement deleted successfully.'
            ]);
        } catch (\DomainException $e) {
            // Return the message from the service to the frontend
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }



    public function storeCourse(Request $request, Program $program)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'requirement_category_id' => 'required|exists:program_requirement_categories,id',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $course = $this->service->saveCourse($program, $validated);

        return response()->json([
            'success' => true,
            'course' => $course->load('course', 'category')
        ]);
    }

    public function destroyCourse(Program $program, ProgramCourse $programCourse)
    {
        abort_unless(
            $programCourse->program_id === $program->id,
            403,
            'Invalid program course'
        );

        $this->service->deleteCourse($programCourse);

        return response()->json(['success' => true]);
    }


}
