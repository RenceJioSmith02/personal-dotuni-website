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

    public function index()
    {
        $categories = $this->service->list();
        return view('admin.linkage.categories.index', compact('categories'));
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



// namespace App\Http\Controllers\Admin\linkage;

// use App\Http\Controllers\Controller;
// use App\Models\LinkageCategory;
// use Illuminate\Http\Request;
// use Illuminate\Validation\Rule;

// class LinkageCategoryController extends Controller
// {
//     public function index()
//     {
//         $categories = LinkageCategory::orderBy('sort_order')->get();
//         return view('admin.linkage.categories.index', compact('categories'));
//     }

//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'name' => [
//                 'required',
//                 'string',
//                 Rule::unique('linkage_categories')->whereNull('deleted_at'),
//             ],
//             'sort_order' => 'nullable|integer',
//         ]);

//         $category = LinkageCategory::create($validated);

//         return response()->json([
//             'message' => 'Category created successfully',
//             'data' => $category,
//         ], 201);
//     }

//     public function edit(LinkageCategory $linkageCategory)
//     {
//         return response()->json($linkageCategory);
//     }

//     public function update(Request $request, LinkageCategory $linkageCategory)
//     {
//         $validated = $request->validate([
//             'name' => [
//                 'required',
//                 Rule::unique('linkage_categories')
//                     ->ignore($linkageCategory->id)
//                     ->whereNull('deleted_at'),
//             ],
//             'sort_order' => 'nullable|integer',
//         ]);

//         $linkageCategory->update($validated);

//         return response()->json([
//             'message' => 'Category updated successfully',
//         ]);
//     }

//     public function destroy(LinkageCategory $linkageCategory)
//     {
//         if ($linkageCategory->linkages()->exists()) {
//             return response()->json([
//                 'message' => 'Category has linkages and cannot be deleted'
//             ], 422);
//         }

//         $linkageCategory->delete();

//         return response()->json([
//             'message' => 'Category deleted successfully',
//         ]);
//     }

// }
