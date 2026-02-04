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

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->service->datatable($request);
        }

        // Fetch sections for modal dropdowns
        $sections = RuleSection::with('article')->orderBy('sort_order')->get();

        return view(
            'admin.rules_and_regulations.sub_sections.index',
            compact('sections')
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
        try {
            $this->service->delete($ruleSubSection);

            return response()->json([
                'message' => 'Sub-section deleted successfully',
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

}

