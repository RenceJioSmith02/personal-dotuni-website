<?php

namespace App\Http\Controllers\Admin\Rule;

use App\Http\Controllers\Controller;
use App\Models\RuleSubSection;
use App\Models\RuleSection;
use App\Services\Rule\RuleSubSectionService;
use Illuminate\Http\Request;

class RuleSubSectionController extends Controller
{
    protected RuleSubSectionService $service;

    public function __construct(RuleSubSectionService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $subSections = $this->service->list();
        $sections = RuleSection::with('article')->orderBy('sort_order')->get();

        return view(
            'admin.rules_and_regulations.sub_sections.index',
            compact('subSections', 'sections')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'section_id' => 'required|exists:rule_sections,id',
            'number' => 'required|string|max:20',
            'body' => 'required|string',
            'sort_order' => 'nullable|integer',
        ]);

        $subSection = $this->service->storeOrRestore($validated);

        return response()->json([
            'message' => 'Sub-section saved successfully',
            'data' => $subSection,
        ], 201);
    }

    public function edit(RuleSubSection $ruleSubSection)
    {
        return response()->json($ruleSubSection);
    }

    public function update(Request $request, RuleSubSection $ruleSubSection)
    {
        $validated = $request->validate([
            'section_id' => 'required|exists:rule_sections,id',
            'number' => 'required|string|max:20',
            'body' => 'required|string',
            'sort_order' => 'nullable|integer',
        ]);

        $subSection = $this->service->updateOrRestore($ruleSubSection, $validated);

        return response()->json([
            'message' => 'Sub-section updated successfully',
            'data' => $subSection,
        ]);
    }

    public function destroy(RuleSubSection $ruleSubSection)
    {
        $this->service->delete($ruleSubSection);

        return response()->json([
            'message' => 'Sub-section deleted successfully',
        ]);
    }
}


// namespace App\Http\Controllers\Admin\rule;

// use App\Http\Controllers\Controller;
// use App\Models\RuleSubSection;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use App\Models\RuleSection;

// class RuleSubSectionController extends Controller
// {

//     public function index()
//     {
//         $subSections = RuleSubSection::with('section.article')
//             ->orderBy('sort_order')
//             ->get();

//         $sections = RuleSection::with('article')->orderBy('sort_order')->get(); // <-- fetch sections for modal

//         return view(
//             'admin.rules_and_regulations.sub_sections.index',
//             compact('subSections', 'sections') // <-- pass sections
//         );
//     }


//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'section_id' => 'required|exists:rule_sections,id',
//             'number' => 'required|string|max:20',
//             'body' => 'required|string',
//             'sort_order' => 'nullable|integer',
//         ]);

//         $validated['updated_by'] = Auth::id();

//         $existing = RuleSubSection::withTrashed()
//             ->where('section_id', $validated['section_id'])
//             ->where('number', $validated['number'])
//             ->first();

//         if ($existing) {
//             if ($existing->trashed())
//                 $existing->restore();
//             $existing->update($validated);
//             $subSection = $existing;
//         } else {
//             $subSection = RuleSubSection::create($validated);
//         }

//         return response()->json([
//             'message' => 'Sub-section saved successfully',
//             'data' => $subSection,
//         ], 201);
//     }

//     public function edit(RuleSubSection $ruleSubSection)
//     {
//         return response()->json($ruleSubSection);
//     }

//     public function update(Request $request, RuleSubSection $ruleSubSection)
//     {
//         $validated = $request->validate([
//             'section_id' => 'required|exists:rule_sections,id',
//             'number' => 'required|string|max:20',
//             'body' => 'required|string',
//             'sort_order' => 'nullable|integer',
//         ]);

//         $validated['updated_by'] = Auth::id();
//         $ruleSubSection->update($validated);

//         return response()->json(['message' => 'Sub-section updated successfully']);
//     }

//     public function destroy(RuleSubSection $ruleSubSection)
//     {
//         $ruleSubSection->delete();
//         return response()->json(['message' => 'Sub-section deleted successfully']);
//     }
// }
