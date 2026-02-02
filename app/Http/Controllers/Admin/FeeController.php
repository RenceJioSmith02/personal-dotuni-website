<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use Illuminate\Http\Request;
use App\Services\FeeService;

class FeeController extends Controller
{
    protected FeeService $service;

    public function __construct(FeeService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $fees = $this->service->list();
        return view('admin.fees.index', compact('fees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:250',
            'caption' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
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
            'title' => 'required|string|max:250',
            'caption' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
            'image' => 'nullable|image|max:2048',
        ]);

        $fee = $this->service->update($fee, $validated, $request->file('image'));

        return response()->json(['message' => 'Fee updated successfully', 'fee' => $fee]);
    }

    public function destroy(Fee $fee)
    {
        $this->service->delete($fee);

        return response()->json(['message' => 'Fee deleted successfully']);
    }
}
