<?php

namespace App\Http\Controllers\Admin\prospective_student;

use App\Http\Controllers\Controller;
use App\Models\ProspectiveStudentCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ProspectiveStudentCategoryController extends Controller
{
    public function index()
    {
        $categories = ProspectiveStudentCategory::orderBy('sort_order')->get();

        return view(
            'admin.prospective_student.categories.index',
            compact('categories')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'required|boolean',
        ]);

        $validated['updated_by'] = Auth::id();

        // Check if soft-deleted category exists
        $existing = ProspectiveStudentCategory::withTrashed()
            ->where('name', $validated['name'])
            ->first();

        if ($existing) {
            if ($existing->trashed()) {
                // Restore soft-deleted category
                $existing->restore();
            }
            $existing->update($validated);

            $category = $existing;
        } else {
            $category = ProspectiveStudentCategory::create($validated);
        }

        return response()->json([
            'message' => 'Prospective student category saved successfully',
            'data' => $category,
        ], 201);
    }

    public function edit(ProspectiveStudentCategory $prospectiveStudentCategory)
    {
        return response()->json($prospectiveStudentCategory);
    }

    public function update(Request $request, ProspectiveStudentCategory $prospectiveStudentCategory)
    {
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

        $validated['updated_by'] = Auth::id();

        $prospectiveStudentCategory->update($validated);

        return response()->json([
            'message' => 'Prospective student category updated successfully',
        ]);
    }

    public function destroy(ProspectiveStudentCategory $prospectiveStudentCategory)
    {
        if ($prospectiveStudentCategory->items()->exists()) {
            return response()->json([
                'message' => 'Category has items and cannot be deleted.',
            ], 422);
        }

        $prospectiveStudentCategory->delete();

        return response()->json([
            'message' => 'Prospective student category deleted successfully',
        ]);
    }
}
