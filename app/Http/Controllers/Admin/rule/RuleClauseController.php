<?php

namespace App\Http\Controllers\Admin\Rule;

use App\Http\Controllers\Controller;
use App\Models\RuleClause;
use App\Models\RuleSubSection;
use App\Services\Rule\RuleClauseService;
use Illuminate\Http\Request;

class RuleClauseController extends Controller
{
    protected RuleClauseService $service;

    public function __construct(RuleClauseService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->service->datatable($request);
        }

        $subSections = RuleSubSection::with('section.article')->orderBy('sort_order')->get();

        return view(
            'admin.rules_and_regulations.clauses.index',
            compact('subSections')
        );
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'sub_section_id' => 'required|exists:rule_sub_sections,id',
            'number' => 'required|string|max:20',
            'body' => 'required|string',
            'sort_order' => 'nullable|integer',
        ]);

        $clause = $this->service->storeOrRestore($validated);

        return response()->json([
            'message' => 'Clause saved successfully',
            'data' => $clause,
        ], 201);
    }

    public function edit(RuleClause $ruleClause)
    {
        return response()->json($ruleClause);
    }

    public function update(Request $request, RuleClause $ruleClause)
    {
        $validated = $request->validate([
            'sub_section_id' => 'required|exists:rule_sub_sections,id',
            'number' => 'required|string|max:20',
            'body' => 'required|string',
            'sort_order' => 'nullable|integer',
        ]);

        $clause = $this->service->updateOrRestore($ruleClause, $validated);

        return response()->json([
            'message' => 'Clause updated successfully',
            'data' => $clause,
        ]);
    }

    public function destroy(RuleClause $ruleClause)
    {
        $this->service->delete($ruleClause);

        return response()->json([
            'message' => 'Clause deleted successfully',
        ]);
    }
}

