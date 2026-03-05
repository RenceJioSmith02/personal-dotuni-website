<?php

namespace App\Http\Controllers\Admin\Rule;

use App\Http\Controllers\Controller;
use App\Models\RuleClause;
use App\Models\RuleSubSection;
use App\Services\Rule\RuleClauseService;
use DomainException;
use Illuminate\Http\Request;

class RuleClauseController extends Controller
{
    public function __construct(protected RuleClauseService $service)
    {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->service->datatable($request);
        }

        // ✅ Exclude archived sub-sections from dropdown
        $subSections = RuleSubSection::with('section.article')
            ->whereNull('deleted_at')
            ->orderBy('sort_order')
            ->get();

        return view('admin.rules_and_regulations.clauses.index', compact('subSections'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sub_section_id' => 'required|exists:rule_sub_sections,id',
            'number' => 'required|string|max:20',
            'body' => 'required|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'sometimes|boolean', // ✅ Add
        ]);

        try {
            $clause = $this->service->create($validated);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Clause saved successfully', 'data' => $clause], 201);
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
            'is_active' => 'sometimes|boolean', // ✅ Add
        ]);

        try {
            $clause = $this->service->update($ruleClause, $validated);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Clause updated successfully', 'data' => $clause]);
    }

    public function destroy(RuleClause $ruleClause)
    {
        try {
            $this->service->delete($ruleClause);

            return response()->json(['message' => 'Clause deleted successfully']);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    // ✅ New
    public function archive(RuleClause $ruleClause)
    {
        try {
            $clause = $this->service->archive($ruleClause);

            return response()->json([
                'message' => 'Clause archived successfully',
                'clause' => $clause,
            ]);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    // ✅ New
    public function unarchive(RuleClause $ruleClause)
    {
        try {
            $clause = $this->service->unarchive($ruleClause);

            return response()->json([
                'message' => 'Clause unarchived successfully',
                'clause' => $clause,
            ]);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}