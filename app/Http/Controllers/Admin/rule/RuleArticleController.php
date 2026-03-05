<?php

namespace App\Http\Controllers\Admin\rule;

use App\Models\RuleArticle;
use DomainException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Rule\RuleArticleService;

class RuleArticleController extends Controller
{
    public function __construct(
        protected RuleArticleService $service
    ) {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->service->datatable($request);
        }

        return view('admin.rules_and_regulations.articles.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'number' => 'required|string|max:20',
            'title' => 'required|string|max:250',
            'sort_order' => 'nullable|integer',
            'is_active' => 'sometimes|boolean', // ✅ Add
        ]);

        try {
            $article = $this->service->create($data);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Article saved successfully', 'data' => $article], 201);
    }

    public function edit(RuleArticle $ruleArticle)
    {
        return response()->json($ruleArticle);
    }

    public function update(Request $request, RuleArticle $ruleArticle)
    {
        $data = $request->validate([
            'number' => 'required|string|max:20',
            'title' => 'required|string|max:250',
            'sort_order' => 'nullable|integer',
            'is_active' => 'sometimes|boolean', // ✅ Add
        ]);

        try {
            $this->service->update($ruleArticle, $data);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Article updated successfully']);
    }

    public function destroy(RuleArticle $ruleArticle)
    {
        try {
            $this->service->delete($ruleArticle);

            return response()->json(['message' => 'Article deleted successfully']);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    // ✅ New
    public function archive(RuleArticle $ruleArticle)
    {
        try {
            $article = $this->service->archive($ruleArticle);

            return response()->json([
                'message' => 'Article archived successfully',
                'article' => $article,
            ]);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    // ✅ New
    public function unarchive(RuleArticle $ruleArticle)
    {
        try {
            $article = $this->service->unarchive($ruleArticle);

            return response()->json([
                'message' => 'Article unarchived successfully',
                'article' => $article,
            ]);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}