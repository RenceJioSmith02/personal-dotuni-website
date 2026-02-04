<?php

namespace App\Http\Controllers\Admin\Rule;

use App\Http\Controllers\Controller;
use App\Models\RuleSection;
use App\Models\RuleArticle;
use App\Services\Rule\RuleSectionService;
use Illuminate\Http\Request;

class RuleSectionController extends Controller
{
    protected RuleSectionService $service;

    public function __construct(RuleSectionService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->service->datatable($request);
        }

        // pass articles for the modal dropdown
        $articles = RuleArticle::orderBy('sort_order')->get();

        return view(
            'admin.rules_and_regulations.sections.index',
            compact('articles')
        );
    }



    public function store(Request $request)
    {
        $validated = $request->validate([
            'article_id' => 'required|exists:rule_articles,id',
            'number' => 'required|string|max:20',
            'body' => 'required|string',
            'sort_order' => 'nullable|integer',
        ]);

        $section = $this->service->storeOrRestore($validated);

        return response()->json([
            'message' => 'Section saved successfully',
            'data' => $section,
        ], 201);
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
        ]);

        $section = $this->service->updateOrRestore($ruleSection, $validated);

        return response()->json([
            'message' => 'Section updated successfully',
            'data' => $section,
        ]);
    }

    public function destroy(RuleSection $ruleSection)
    {
        try {
            $this->service->delete($ruleSection);

            return response()->json([
                'message' => 'Section deleted successfully',
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

}

