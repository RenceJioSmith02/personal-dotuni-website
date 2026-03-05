<?php

namespace App\Http\Controllers\Admin\Rule;

use App\Http\Controllers\Controller;
use App\Models\RuleSubSection;
use App\Models\RuleSection;
use App\Services\Rule\RuleSubSectionService;
use DomainException;
use Illuminate\Http\Request;

class RuleSubSectionController extends Controller
{
    public function __construct(protected RuleSubSectionService $service)
    {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->service->datatable($request);
        }

        // ✅ Exclude archived sections from dropdown
        $sections = RuleSection::with('article')
            ->whereNull('deleted_at')
            ->orderBy('sort_order')
            ->get();

        return view('admin.rules_and_regulations.sub_sections.index', compact('sections'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'section_id' => 'required|exists:rule_sections,id',
            'number' => 'required|string|max:20',
            'body' => 'required|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'sometimes|boolean', // ✅ Add
        ]);

        try {
            $subSection = $this->service->create($validated);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Sub-section saved successfully', 'data' => $subSection], 201);
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
            'is_active' => 'sometimes|boolean', // ✅ Add
        ]);

        try {
            $subSection = $this->service->update($ruleSubSection, $validated);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Sub-section updated successfully', 'data' => $subSection]);
    }

    public function destroy(RuleSubSection $ruleSubSection)
    {
        try {
            $this->service->delete($ruleSubSection);

            return response()->json(['message' => 'Sub-section deleted successfully']);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    // ✅ New
    public function archive(RuleSubSection $ruleSubSection)
    {
        try {
            $subSection = $this->service->archive($ruleSubSection);

            return response()->json([
                'message' => 'Sub-section archived successfully',
                'subSection' => $subSection,
            ]);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    // ✅ New
    public function unarchive(RuleSubSection $ruleSubSection)
    {
        try {
            $subSection = $this->service->unarchive($ruleSubSection);

            return response()->json([
                'message' => 'Sub-section unarchived successfully',
                'subSection' => $subSection,
            ]);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}