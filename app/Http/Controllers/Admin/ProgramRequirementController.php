<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;

class ProgramRequirementController extends Controller
{
    public function index(Program $program)
    {
        $requirements = $program->requirements()->with('category')->get();
        return view('admin.programs.requirements', compact('program', 'requirements'));
    }

    public function store(Request $request, Program $program)
    {
        $request->validate([
            'requirement_category_id' => 'required|exists:program_requirement_categories,id',
            'required_units' => 'required|integer',
        ]);

        $program->requirements()->updateOrCreate(
            ['requirement_category_id' => $request->requirement_category_id],
            [
                'required_units' => $request->required_units,
                'updated_by' => auth()->id(),
            ]
        );

        return response()->json(['success' => true]);
    }

    public function destroy(Program $program, $id)
    {
        $program->requirements()->where('id', $id)->delete();
        return response()->json(['success' => true]);
    }
}
