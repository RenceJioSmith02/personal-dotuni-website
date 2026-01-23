<?php

namespace App\Http\Controllers\Admin\Form;

use App\Http\Controllers\Controller;
use App\Models\FormCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class FormCategoryController extends Controller
{
    public function index()
    {
        // Include soft-deleted categories for display if needed, or just active ones
        $categories = FormCategory::orderBy('sort_order')->get();
        return view('admin.form.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'sort_order' => 'nullable|integer',
        ]);

        // Check if a soft-deleted category with the same name exists
        $existing = FormCategory::withTrashed()
            ->where('name', $validated['name'])
            ->first();

        if ($existing) {
            if ($existing->trashed()) {
                // Restore the soft-deleted category
                $existing->restore();
                $existing->update([
                    'sort_order' => $validated['sort_order'] ?? 0,
                    'slug' => Str::slug($validated['name']),
                ]);

                return response()->json([
                    'message' => 'Category restored successfully',
                    'data' => $existing,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Category already exists',
                ], 422);
            }
        }

        // Otherwise, create a new category
        $category = FormCategory::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return response()->json([
            'message' => 'Category created successfully',
            'data' => $category,
        ], 201);
    }

    public function edit(FormCategory $formCategory)
    {
        return response()->json($formCategory);
    }

    public function update(Request $request, FormCategory $formCategory)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                Rule::unique('form_categories')
                    ->ignore($formCategory->id)
                    ->whereNull('deleted_at'),
            ],
            'sort_order' => 'nullable|integer',
        ]);

        $formCategory->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return response()->json([
            'message' => 'Category updated successfully',
        ]);
    }

    public function destroy(FormCategory $formCategory)
    {
        // Prevent deletion if the category has forms
        if ($formCategory->forms()->exists()) {
            return response()->json([
                'message' => 'Category has forms and cannot be deleted'
            ], 422);
        }

        // Soft delete
        $formCategory->delete();

        return response()->json([
            'message' => 'Category deleted successfully',
        ]);
    }
}
