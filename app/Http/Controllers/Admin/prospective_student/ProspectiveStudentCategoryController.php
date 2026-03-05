<?php

namespace App\Http\Controllers\Admin\prospective_student;

use App\Http\Controllers\Controller;
use App\Models\ProspectiveStudentCategory;
use App\Services\ProspectiveStudent\ProspectiveStudentCategoryService;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProspectiveStudentCategoryController extends Controller
{
    public function __construct(
        protected ProspectiveStudentCategoryService $service
    ) {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->service->datatable($request);
        }

        return view('admin.prospective_student.categories.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('prospective_student_categories')->whereNull('deleted_at')
            ],
            'sort_order' => 'nullable|integer',
            'is_active' => 'required|boolean',
        ]);

        try {
            $category = $this->service->create($validated);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Category saved successfully',
            'data' => $category,
        ], 201);
    }

    public function edit(ProspectiveStudentCategory $prospectiveStudentCategory)
    {
        return response()->json($prospectiveStudentCategory);
    }

    public function update(
        Request $request,
        ProspectiveStudentCategory $prospectiveStudentCategory
    ) {
        $validated = $request->validate([
            'name' => [
                'required',
                Rule::unique('prospective_student_categories')
                    ->ignore($prospectiveStudentCategory->id)
                    ->whereNull('deleted_at')
            ],
            'sort_order' => 'nullable|integer',
            'is_active' => 'required|boolean',
        ]);

        try {
            $category = $this->service->update($prospectiveStudentCategory, $validated);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Category updated successfully',
            'data' => $category,
        ]);
    }

    public function destroy(ProspectiveStudentCategory $prospectiveStudentCategory)
    {
        try {
            $this->service->delete($prospectiveStudentCategory);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Category deleted successfully']);
    }

    // ✅ New
    public function archive(ProspectiveStudentCategory $prospectiveStudentCategory)
    {
        try {
            $category = $this->service->archive($prospectiveStudentCategory);

            return response()->json([
                'message' => 'Category archived successfully',
                'category' => $category,
            ]);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    // ✅ New
    public function unarchive(ProspectiveStudentCategory $prospectiveStudentCategory)
    {
        try {
            $category = $this->service->unarchive($prospectiveStudentCategory);

            return response()->json([
                'message' => 'Category unarchived successfully',
                'category' => $category,
            ]);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}