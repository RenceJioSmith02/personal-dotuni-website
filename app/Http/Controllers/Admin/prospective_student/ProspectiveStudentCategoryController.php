<?php

namespace App\Http\Controllers\Admin\prospective_student;

use App\Http\Controllers\Controller;
use App\Models\ProspectiveStudentCategory;
use App\Services\ProspectiveStudent\ProspectiveStudentCategoryService;
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
            'name' => 'required|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'required|boolean',
        ]);

        $category = $this->service->createOrRestore($validated);

        return response()->json([
            'message' => 'Prospective student category saved successfully',
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
                    ->whereNull('deleted_at'),
            ],
            'sort_order' => 'nullable|integer',
            'is_active' => 'required|boolean',
        ]);

        $category = $this->service->updateOrRestore(
            $prospectiveStudentCategory,
            $validated
        );

        return response()->json([
            'message' => 'Prospective student category updated successfully',
            'data' => $category,
        ]);
    }


    public function destroy(ProspectiveStudentCategory $prospectiveStudentCategory)
    {
        $this->service->delete($prospectiveStudentCategory);

        return response()->json([
            'message' => 'Prospective student category deleted successfully',
        ]);
    }
}

