<?php

namespace App\Http\Controllers\Admin\Academic;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Services\Academic\ProgramService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use DomainException;

class ProgramController extends Controller
{
    public function __construct(
        protected ProgramService $service
    ) {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return response()->json($this->service->datatable($request));
        }

        return view('admin.academic.programs.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:250',
                Rule::unique('programs', 'title')->whereNull('deleted_at'),
            ],
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
            'title' => [
                'required',
                'string',
                'max:250',
                Rule::unique('programs', 'title')
                    ->ignore($program->id)
                    ->whereNull('deleted_at'),
            ],
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

            return response()->json(['message' => 'Program deleted successfully']);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    // ✅ New
    public function archive(Program $program)
    {
        try {
            $program = $this->service->archive($program);

            return response()->json([
                'message' => 'Program archived successfully',
                'program' => $program,
            ]);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    // ✅ New
    public function unarchive(Program $program)
    {
        try {
            $program = $this->service->unarchive($program);

            return response()->json([
                'message' => 'Program unarchived successfully',
                'program' => $program,
            ]);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}