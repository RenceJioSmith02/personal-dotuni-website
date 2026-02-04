<?php

namespace App\Http\Controllers\Admin\linkage;

use App\Http\Controllers\Controller;
use App\Models\Linkage;
use App\Models\LinkageCategory;
use App\Services\Linkage\LinkageService;
use Illuminate\Http\Request;

class LinkageController extends Controller
{
    public function __construct(protected LinkageService $service)
    {
    }


    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->service->datatable($request);
        }

        $categories = LinkageCategory::orderBy('name')->get();
        return view('admin.linkage.linkages.index', compact('categories'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:linkage_categories,id',
            'title' => 'required|string|max:150',
            'url' => 'required|url|max:1000',
            'description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
            'is_active' => 'required|boolean',
            'logo' => 'nullable|image|max:2048',
        ]);

        $this->service->create($validated, $request->file('logo'));

        return response()->json(['message' => 'Linkage created successfully']);
    }

    public function edit(Linkage $linkage)
    {
        return response()->json($linkage->load(['category', 'logo']));
    }

    public function update(Request $request, Linkage $linkage)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:linkage_categories,id',
            'title' => 'required|string|max:150',
            'url' => 'required|url|max:1000',
            'description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
            'is_active' => 'required|boolean',
            'logo' => 'nullable|image|max:2048',
        ]);

        $this->service->update($linkage, $validated, $request->file('logo'));

        return response()->json(['message' => 'Linkage updated successfully']);
    }

    public function destroy(Linkage $linkage)
    {
        $this->service->delete($linkage);

        return response()->json(['message' => 'Linkage deleted successfully']);
    }
}

