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
    public function __construct(
        protected ProgramRequirementCategoryService $service
    ) {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return response()->json($this->service->datatable($request));
        }

        return view('admin.academic.program_requirement_categories.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('program_requirement_categories')->whereNull('deleted_at')
            ],
            'sort_order' => 'required|integer',
            'is_active' => 'sometimes|boolean', // ✅ Add
        ]);

        try {
            $category = $this->service->create($validated);

            return response()->json(['message' => 'Category created successfully', 'category' => $category], 201);
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
                    ->whereNull('deleted_at')
            ],
            'sort_order' => 'required|integer',
            'is_active' => 'sometimes|boolean', // ✅ Add
        ]);

        try {
            $category = $this->service->update($program_requirement_category, $validated);

            return response()->json(['message' => 'Category updated successfully', 'category' => $category]);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function destroy(ProgramRequirementCategory $program_requirement_category)
    {
        try {
            $this->service->delete($program_requirement_category);

            return response()->json(['message' => 'Category deleted successfully']);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function archive(ProgramRequirementCategory $program_requirement_category)
    {
        try {
            $category = $this->service->archive($program_requirement_category);

            return response()->json(['message' => 'Category archived successfully', 'category' => $category]);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function unarchive(ProgramRequirementCategory $program_requirement_category)
    {
        try {
            $category = $this->service->unarchive($program_requirement_category);

            return response()->json(['message' => 'Category unarchived successfully', 'category' => $category]);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}