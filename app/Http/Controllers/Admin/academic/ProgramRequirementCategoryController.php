<?php

namespace App\Http\Controllers\Admin\Academic;

use App\Http\Controllers\Controller;
use App\Models\ProgramRequirementCategory;
use App\Services\Academic\ProgramRequirementCategoryService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use DomainException;

class ProgramRequirementCategoryController extends Controller
{
    protected ProgramRequirementCategoryService $service;

    public function __construct(ProgramRequirementCategoryService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        // Server-side DataTable AJAX request
        if ($request->ajax()) {
            $data = $this->service->datatable($request);

            return response()->json($data);
        }

        // Normal page load
        return view('admin.academic.program_requirement_categories.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('program_requirement_categories')
                    ->whereNull('deleted_at'),
            ],
            'sort_order' => 'required|integer',
        ]);

        try {
            $category = $this->service->createOrRestore($validated);

            return response()->json([
                'message' => 'Category created successfully',
                'category' => $category,
            ], 201);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function edit(ProgramRequirementCategory $program_requirement_category)
    {
        return response()->json($program_requirement_category);
    }

    public function update(Request $request, ProgramRequirementCategory $program_requirement_category)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('program_requirement_categories')
                    ->ignore($program_requirement_category->id)
                    ->whereNull('deleted_at'),
            ],
            'sort_order' => 'required|integer',
        ]);

        try {
            $category = $this->service->updateOrRestore($program_requirement_category, $validated);

            return response()->json([
                'message' => 'Category updated successfully',
                'category' => $category,
            ]);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function destroy(ProgramRequirementCategory $program_requirement_category)
    {
        try {
            $this->service->delete($program_requirement_category);

            return response()->json([
                'message' => 'Category deleted successfully',
                'id' => $program_requirement_category->id,
            ]);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}

