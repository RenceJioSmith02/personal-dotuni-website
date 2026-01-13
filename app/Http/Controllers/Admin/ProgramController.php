<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::all();
        return view('admin.programs.index', compact('programs'));
    }

    public function create()
    {
        return view('admin.programs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'type' => 'required|string',
            'total_units' => 'required|numeric',
        ]);

        Program::create([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'total_units' => $request->total_units,
            'is_active' => 1,
        ]);

        return redirect()
            ->route('admin.programs.index')
            ->with('success', 'Program created successfully');
    }

    public function edit(Program $program)
    {
        return view('admin.programs.edit', compact('program'));
    }

    public function update(Request $request, Program $program)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'type' => 'required|string',
            'total_units' => 'required|numeric',
        ]);

        $program->update([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'total_units' => $request->total_units,
            'is_active' => $request->is_active,
        ]);

        return redirect()
            ->route('admin.programs.index')
            ->with('success', 'Program updated successfully');
    }

    public function destroy(Program $program)
    {
        $program->delete();
        return back()->with('success', 'Program deleted');
    }
}
