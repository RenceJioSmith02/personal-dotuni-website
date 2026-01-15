<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramRequirementCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProgramRequirementCategoryController extends Controller
{
    public function index()
    {
        $categories = ProgramRequirementCategory::orderBy('sort_order')->get();
        return view('admin.program_requirement_categories.index', compact('categories'));
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

        // CHECK FOR SOFT-DELETED RECORD
        $existing = ProgramRequirementCategory::withTrashed()
            ->where('name', $validated['name'])
            ->first();

        if ($existing) {
            $existing->restore();
            $existing->update($validated);

            return response()->json([
                'message' => 'Category restored successfully',
                'category' => $existing
            ]);
        }

        $category = ProgramRequirementCategory::create($validated);

        return response()->json([
            'message' => 'Category created successfully',
            'category' => $category
        ], 201);
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

        // SAFETY CHECK AGAINST ARCHIVED DUPLICATES
        $conflict = ProgramRequirementCategory::withTrashed()
            ->where('name', $validated['name'])
            ->where('id', '!=', $program_requirement_category->id)
            ->first();

        if ($conflict) {
            return response()->json([
                'message' =>
                    'A category with this name already exists (including archived records). Please add it again to restore.'
            ], 422);
        }

        $program_requirement_category->update($validated);

        return response()->json([
            'message' => 'Category updated successfully',
            'category' => $program_requirement_category
        ]);
    }

    public function destroy(ProgramRequirementCategory $program_requirement_category)
    {
        $program_requirement_category->delete();

        return response()->json([
            'message' => 'Category deleted successfully',
            'id' => $program_requirement_category->id
        ]);
    }
}
