<?php

namespace App\Http\Controllers\Admin\prospective_student;

use App\Http\Controllers\Controller;
use App\Models\ProspectiveStudentItem;
use App\Models\ProspectiveStudentCategory;
use App\Services\ProspectiveStudent\ProspectiveStudentItemService;
use Illuminate\Http\Request;

class ProspectiveStudentItemController extends Controller
{
    public function __construct(
        protected ProspectiveStudentItemService $service
    ) {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->service->datatable($request);
        }

        $categories = ProspectiveStudentCategory::orderBy('name')->get();

        return view(
            'admin.prospective_student.items.index',
            compact('categories')
        );
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:prospective_student_categories,id',
            'content' => 'required|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'required|boolean',
        ]);

        $item = $this->service->create($validated);

        return response()->json([
            'message' => 'Item created successfully',
            'data' => $item,
        ], 201);
    }

    public function edit(ProspectiveStudentItem $prospectiveStudentItem)
    {
        return response()->json(
            $prospectiveStudentItem->load('category')
        );
    }

    public function update(
        Request $request,
        ProspectiveStudentItem $prospectiveStudentItem
    ) {
        $validated = $request->validate([
            'category_id' => 'required|exists:prospective_student_categories,id',
            'content' => 'required|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'required|boolean',
        ]);

        $this->service->update($prospectiveStudentItem, $validated);

        return response()->json([
            'message' => 'Item updated successfully',
        ]);
    }

    public function destroy(ProspectiveStudentItem $prospectiveStudentItem)
    {
        $this->service->delete($prospectiveStudentItem);

        return response()->json([
            'message' => 'Item deleted successfully',
        ]);
    }
}

