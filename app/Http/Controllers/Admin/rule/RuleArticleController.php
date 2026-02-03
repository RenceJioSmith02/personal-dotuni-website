<?php

namespace App\Http\Controllers\Admin\rule;

use App\Models\RuleArticle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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

        return view(
            'admin.rules_and_regulations.articles.index'
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'number' => 'required|string|max:20',
            'title' => 'required|string|max:250',
            'sort_order' => 'nullable|integer',
        ]);

        $article = $this->service->storeOrRestore($data);

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
        $data = $request->validate([
            'number' => 'required|string|max:20',
            'title' => 'required|string|max:250',
            'sort_order' => 'nullable|integer',
        ]);

        $this->service->updateOrRestore($ruleArticle, $data);

        return response()->json([
            'message' => 'Article updated successfully',
        ]);
    }

    public function destroy(RuleArticle $ruleArticle)
    {
        $this->service->delete($ruleArticle);

        return response()->json([
            'message' => 'Article deleted successfully',
        ]);
    }
}


// class RuleArticleController extends Controller
// {
//     public function index()
//     {
//         $articles = RuleArticle::orderBy('sort_order')->get();
//         return view('admin.rules_and_regulations.articles.index', compact('articles'));
//     }

//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'number' => 'required|string|max:20',
//             'title' => 'required|string|max:250',
//             'sort_order' => 'nullable|integer',
//         ]);

//         $validated['updated_by'] = Auth::id();

//         $existing = RuleArticle::withTrashed()
//             ->where('number', $validated['number'])
//             ->first();

//         if ($existing) {
//             if ($existing->trashed()) $existing->restore();
//             $existing->update($validated);
//             $article = $existing;
//         } else {
//             $article = RuleArticle::create($validated);
//         }

//         return response()->json([
//             'message' => 'Article saved successfully',
//             'data' => $article,
//         ], 201);
//     }

//     public function edit(RuleArticle $ruleArticle)
//     {
//         return response()->json($ruleArticle);
//     }

//     public function update(Request $request, RuleArticle $ruleArticle)
//     {
//         $validated = $request->validate([
//             'number' => 'required|string|max:20',
//             'title' => 'required|string|max:250',
//             'sort_order' => 'nullable|integer',
//         ]);

//         $validated['updated_by'] = Auth::id();
//         $ruleArticle->update($validated);

//         return response()->json(['message' => 'Article updated successfully']);
//     }

//     public function destroy(RuleArticle $ruleArticle)
//     {
//         $ruleArticle->delete();
//         return response()->json(['message' => 'Article deleted successfully']);
//     }
// }
