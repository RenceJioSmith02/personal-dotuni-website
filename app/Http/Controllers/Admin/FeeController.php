<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use DomainException;
use Illuminate\Http\Request;
use App\Services\FeeService;
use Illuminate\Validation\Rule;

class FeeController extends Controller
{
    public function __construct(protected FeeService $service)
    {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->service->datatable($request);
        }

        return view('admin.fees.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:250',
                Rule::unique('fees', 'title')->whereNull('deleted_at'),
            ],
            'caption' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
            'is_active' => 'required|boolean', // ✅ Add
            'image' => 'nullable|image|max:2048',
        ]);

        $fee = $this->service->create($validated, $request->file('image'));

        return response()->json(['message' => 'Fee created successfully', 'fee' => $fee]);
    }

    public function edit(Fee $fee)
    {
        return response()->json($fee->load('asset'));
    }

    public function update(Request $request, Fee $fee)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:250',
                Rule::unique('fees', 'title')->ignore($fee->id)->whereNull('deleted_at'),
            ],
            'caption' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
            'is_active' => 'required|boolean', // ✅ Add
            'image' => 'nullable|image|max:2048',
        ]);

        $fee = $this->service->update($fee, $validated, $request->file('image'));

        return response()->json(['message' => 'Fee updated successfully', 'fee' => $fee]);
    }

    public function destroy(Fee $fee)
    {
        try {
            $this->service->delete($fee);

            return response()->json(['message' => 'Fee deleted successfully']);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    // ✅ New
    public function archive(Fee $fee)
    {
        try {
            $fee = $this->service->archive($fee);

            return response()->json(['message' => 'Fee archived successfully', 'fee' => $fee]);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    // ✅ New
    public function unarchive(Fee $fee)
    {
        try {
            $fee = $this->service->unarchive($fee);

            return response()->json(['message' => 'Fee unarchived successfully', 'fee' => $fee]);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}