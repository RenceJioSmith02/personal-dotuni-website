<?php

namespace App\Http\Controllers\Admin\prospective_student;

use App\Http\Controllers\Controller;
use App\Models\ProspectiveStudentItem;
use App\Models\ProspectiveStudentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProspectiveStudentItemController extends Controller
{
    /**
     * Display a listing of prospective student items.
     */
    public function index()
    {
        $items = ProspectiveStudentItem::with('category')
            ->orderBy('sort_order')
            ->get();

        $categories = ProspectiveStudentCategory::orderBy('name')->get();

        return view(
            'admin.prospective_student.items.index',
            compact('items', 'categories')
        );
    }

    /**
     * Store a newly created item.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:prospective_student_categories,id',
            'content' => 'required|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'required|boolean',
        ]);

        $item = ProspectiveStudentItem::create([
            'category_id' => $validated['category_id'],
            'content' => $validated['content'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'],
            'updated_by' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Item created successfully',
            'data' => $item,
        ], 201);
    }

    /**
     * Get item data for editing.
     */
    public function edit(ProspectiveStudentItem $prospectiveStudentItem)
    {
        return response()->json(
            $prospectiveStudentItem->load('category')
        );
    }

    /**
     * Update the specified item.
     */
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

        $prospectiveStudentItem->update([
            'category_id' => $validated['category_id'],
            'content' => $validated['content'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'],
            'updated_by' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Item updated successfully',
        ]);
    }

    /**
     * Soft delete the specified item.
     */
    public function destroy(ProspectiveStudentItem $prospectiveStudentItem)
    {
        $prospectiveStudentItem->delete();

        return response()->json([
            'message' => 'Item deleted successfully',
        ]);
    }
}
