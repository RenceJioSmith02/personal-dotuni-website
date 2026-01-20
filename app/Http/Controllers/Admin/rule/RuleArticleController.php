<?php

namespace App\Http\Controllers\Admin\rule;

use App\Http\Controllers\Controller;
use App\Models\RuleArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RuleArticleController extends Controller
{
    public function index()
    {
        $articles = RuleArticle::orderBy('sort_order')->get();
        return view('admin.rules_and_regulations.articles.index', compact('articles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'number' => 'required|string|max:20',
            'title' => 'required|string|max:250',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['updated_by'] = Auth::id();

        $existing = RuleArticle::withTrashed()
            ->where('number', $validated['number'])
            ->first();

        if ($existing) {
            if ($existing->trashed()) $existing->restore();
            $existing->update($validated);
            $article = $existing;
        } else {
            $article = RuleArticle::create($validated);
        }

        return response()->json([
            'message' => 'Article saved successfully',
            'data' => $article,
        ], 201);
    }

    public function edit(RuleArticle $ruleArticle)
    {
        return response()->json($ruleArticle);
    }

    public function update(Request $request, RuleArticle $ruleArticle)
    {
        $validated = $request->validate([
            'number' => 'required|string|max:20',
            'title' => 'required|string|max:250',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['updated_by'] = Auth::id();
        $ruleArticle->update($validated);

        return response()->json(['message' => 'Article updated successfully']);
    }

    public function destroy(RuleArticle $ruleArticle)
    {
        $ruleArticle->delete();
        return response()->json(['message' => 'Article deleted successfully']);
    }
}
