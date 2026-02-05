<?php

namespace App\Http\Controllers\Admin\Academic;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Services\Academic\ProgramService;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function __construct(
        protected ProgramService $service
    ) {
    }

    public function index(Request $request)
    {
        // If AJAX request (DataTables server-side)
        if ($request->ajax()) {
            return response()->json($this->service->datatable($request));
        }

        // Normal page load
        return view('admin.academic.programs.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:250',
            'description' => 'required|string',
            'type' => 'required|string|max:50',
            'total_units' => 'required|numeric',
            'is_active' => 'required|boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        $this->service->create($validated, $request->file('image'));

        return response()->json(['message' => 'Program created successfully']);
    }

    public function edit(Program $program)
    {
        return response()->json($program->load('asset'));
    }

    public function update(Request $request, Program $program)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:250',
            'description' => 'required|string',
            'type' => 'required|string|max:50',
            'total_units' => 'required|numeric',
            'is_active' => 'required|boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        $this->service->update($program, $validated, $request->file('image'));

        return response()->json(['message' => 'Program updated successfully']);
    }

    public function destroy(Program $program)
    {
        try {
            $this->service->delete($program);

            return response()->json([
                'message' => 'Program deleted successfully'
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

}
