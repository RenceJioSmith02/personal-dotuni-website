<?php

namespace App\Http\Controllers\Admin\linkage;

use App\Http\Controllers\Controller;
use App\Models\LinkageCategory;
use App\Services\Linkage\LinkageCategoryService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use DomainException;

class LinkageCategoryController extends Controller
{
    public function __construct(protected LinkageCategoryService $service)
    {
    }


    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->service->datatable($request);
        }

        return view('admin.linkage.categories.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('linkage_categories')->whereNull('deleted_at'),
            ],
            'sort_order' => 'nullable|integer',
        ]);

        $category = $this->service->create($validated);

        return response()->json([
            'message' => 'Category created successfully',
            'data' => $category,
        ], 201);
    }

    public function edit(LinkageCategory $linkageCategory)
    {
        return response()->json($linkageCategory);
    }

    public function update(Request $request, LinkageCategory $linkageCategory)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                Rule::unique('linkage_categories')
                    ->ignore($linkageCategory->id)
                    ->whereNull('deleted_at'),
            ],
            'sort_order' => 'nullable|integer',
        ]);

        $this->service->update($linkageCategory, $validated);

        return response()->json(['message' => 'Category updated successfully']);
    }

    public function destroy(LinkageCategory $linkageCategory)
    {
        try {
            $this->service->delete($linkageCategory);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Category deleted successfully']);
    }
}

