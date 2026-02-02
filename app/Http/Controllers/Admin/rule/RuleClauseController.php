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

    public function index()
    {
        $clauses = $this->service->list();
        $subSections = RuleSubSection::with('section.article')->orderBy('sort_order')->get();

        return view(
            'admin.rules_and_regulations.clauses.index',
            compact('clauses', 'subSections')
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


// namespace App\Http\Controllers\Admin\rule;

// use App\Http\Controllers\Controller;
// use App\Models\RuleClause;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use App\Models\RuleSubSection;

// class RuleClauseController extends Controller
// {

//     public function index()
//     {
//         $clauses = RuleClause::with('subSection.section.article')
//             ->orderBy('sort_order')
//             ->get();

//         $subSections = RuleSubSection::with('section.article')
//             ->orderBy('sort_order')
//             ->get(); // <-- fetch sub-sections for modal dropdown

//         return view(
//             'admin.rules_and_regulations.clauses.index',
//             compact('clauses', 'subSections') // <-- pass to view
//         );
//     }


//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'sub_section_id' => 'required|exists:rule_sub_sections,id',
//             'number' => 'required|string|max:20',
//             'body' => 'required|string',
//             'sort_order' => 'nullable|integer',
//         ]);

//         $validated['updated_by'] = Auth::id();

//         $existing = RuleClause::withTrashed()
//             ->where('sub_section_id', $validated['sub_section_id'])
//             ->where('number', $validated['number'])
//             ->first();

//         if ($existing) {
//             if ($existing->trashed())
//                 $existing->restore();
//             $existing->update($validated);
//             $clause = $existing;
//         } else {
//             $clause = RuleClause::create($validated);
//         }

//         return response()->json([
//             'message' => 'Clause saved successfully',
//             'data' => $clause,
//         ], 201);
//     }

//     public function edit(RuleClause $ruleClause)
//     {
//         return response()->json($ruleClause);
//     }

//     public function update(Request $request, RuleClause $ruleClause)
//     {
//         $validated = $request->validate([
//             'sub_section_id' => 'required|exists:rule_sub_sections,id',
//             'number' => 'required|string|max:20',
//             'body' => 'required|string',
//             'sort_order' => 'nullable|integer',
//         ]);

//         $validated['updated_by'] = Auth::id();
//         $ruleClause->update($validated);

//         return response()->json(['message' => 'Clause updated successfully']);
//     }

//     public function destroy(RuleClause $ruleClause)
//     {
//         $ruleClause->delete();
//         return response()->json(['message' => 'Clause deleted successfully']);
//     }
// }
