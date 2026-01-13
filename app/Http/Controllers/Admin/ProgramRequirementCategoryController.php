<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramRequirementCategory;
use Illuminate\Http\Request;

class ProgramRequirementCategoryController extends Controller
{
    public function index()
    {
        $categories = ProgramRequirementCategory::all();
        return view('admin.program_requirement_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.program_requirement_categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'sort_order' => 'required|integer',
        ]);

        ProgramRequirementCategory::create($request->all());

        return redirect()->route('admin.program_requirement_categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(ProgramRequirementCategory $program_requirement_category)
    {
        return view('admin.program_requirement_categories.edit', [
            'category' => $program_requirement_category
        ]);
    }

    public function update(Request $request, ProgramRequirementCategory $program_requirement_category)
    {
        $request->validate([
            'name' => 'required|string|unique:program_requirement_categories,name,' . $program_requirement_category->id,
            'sort_order' => 'required|integer',
        ]);

        $program_requirement_category->update($request->all());

        return redirect()->route('admin.program_requirement_categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(ProgramRequirementCategory $program_requirement_category)
    {
        $program_requirement_category->delete();

        return back()->with('success', 'Category deleted successfully.');
    }
}
