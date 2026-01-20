<?php

namespace App\Http\Controllers\Admin\rule;

use App\Http\Controllers\Controller;
use App\Models\RuleSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\RuleArticle; // make sure to import the model

class RuleSectionController extends Controller
{

    public function index()
    {
        $sections = RuleSection::with('article')->orderBy('sort_order')->get();
        $articles = RuleArticle::orderBy('sort_order')->get(); // <-- fetch articles

        return view(
            'admin.rules_and_regulations.sections.index',
            compact('sections', 'articles') // <-- pass articles
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

        $validated['updated_by'] = Auth::id();

        $existing = RuleSection::withTrashed()
            ->where('article_id', $validated['article_id'])
            ->where('number', $validated['number'])
            ->first();

        if ($existing) {
            if ($existing->trashed())
                $existing->restore();
            $existing->update($validated);
            $section = $existing;
        } else {
            $section = RuleSection::create($validated);
        }

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

        $validated['updated_by'] = Auth::id();
        $ruleSection->update($validated);

        return response()->json(['message' => 'Section updated successfully']);
    }

    public function destroy(RuleSection $ruleSection)
    {
        $ruleSection->delete();
        return response()->json(['message' => 'Section deleted successfully']);
    }
}
