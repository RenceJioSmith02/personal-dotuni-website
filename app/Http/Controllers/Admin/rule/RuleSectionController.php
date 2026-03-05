<?php

namespace App\Http\Controllers\Admin\Rule;

use App\Http\Controllers\Controller;
use App\Models\RuleSection;
use App\Models\RuleArticle;
use App\Services\Rule\RuleSectionService;
use DomainException;
use Illuminate\Http\Request;

class RuleSectionController extends Controller
{
    public function __construct(protected RuleSectionService $service)
    {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->service->datatable($request);
        }

        // ✅ Exclude archived articles from dropdown
        $articles = RuleArticle::whereNull('deleted_at')->orderBy('sort_order')->get();

        return view('admin.rules_and_regulations.sections.index', compact('articles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'article_id' => 'required|exists:rule_articles,id',
            'number' => 'required|string|max:20',
            'body' => 'required|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'sometimes|boolean', // ✅ Add
        ]);

        try {
            $section = $this->service->create($validated);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Section saved successfully', 'data' => $section], 201);
    }

    public function edit(RuleSection $ruleSection)
    {
        return response()->json($ruleSection);
    }

    public function update(Request $request, RuleSection $ruleSection)
    {
        $validated = $request->validate([
            'article_id' => 'required|exists:rule_articles,id',
            'number' => 'required|string|max:20',
            'body' => 'required|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'sometimes|boolean', // ✅ Add
        ]);

        try {
            $section = $this->service->update($ruleSection, $validated);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Section updated successfully', 'data' => $section]);
    }

    public function destroy(RuleSection $ruleSection)
    {
        try {
            $this->service->delete($ruleSection);

            return response()->json(['message' => 'Section deleted successfully']);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    // ✅ New
    public function archive(RuleSection $ruleSection)
    {
        try {
            $section = $this->service->archive($ruleSection);

            return response()->json([
                'message' => 'Section archived successfully',
                'section' => $section,
            ]);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    // ✅ New
    public function unarchive(RuleSection $ruleSection)
    {
        try {
            $section = $this->service->unarchive($ruleSection);

            return response()->json([
                'message' => 'Section unarchived successfully',
                'section' => $section,
            ]);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}