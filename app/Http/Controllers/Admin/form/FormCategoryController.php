<?php

namespace App\Http\Controllers\Admin\Form;

use App\Http\Controllers\Controller;
use App\Models\FormCategory;
use App\Services\Form\FormCategoryService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use DomainException;

class FormCategoryController extends Controller
{
    public function __construct(protected FormCategoryService $service)
    {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->service->datatable($request);
        }

        return view('admin.form.categories.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('form_categories', 'name')->whereNull('deleted_at')
            ],
            'sort_order' => 'nullable|integer',
        ]);

        try {
            $category = $this->service->create($validated);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Category saved successfully', 'data' => $category], 201);
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
                'string',
                Rule::unique('form_categories', 'name')
                    ->ignore($formCategory->id)
                    ->whereNull('deleted_at')
            ],
            'sort_order' => 'nullable|integer',
        ]);

        $this->service->update($formCategory, $validated);

        return response()->json(['message' => 'Category updated successfully']);
    }

    public function destroy(FormCategory $formCategory)
    {
        try {
            $this->service->delete($formCategory);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Category deleted successfully']);
    }

    // ✅ New
    public function archive(FormCategory $formCategory)
    {
        try {
            $category = $this->service->archive($formCategory);

            return response()->json([
                'message' => 'Category archived successfully',
                'category' => $category,
            ]);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    // ✅ New
    public function unarchive(FormCategory $formCategory)
    {
        try {
            $category = $this->service->unarchive($formCategory);

            return response()->json([
                'message' => 'Category unarchived successfully',
                'category' => $category,
            ]);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}   