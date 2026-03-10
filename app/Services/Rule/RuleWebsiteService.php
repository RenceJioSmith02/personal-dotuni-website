<?php

namespace App\Services\Rule;

use App\Models\RuleArticle;

class RuleWebsiteService
{
    public function list()
    {
        return RuleArticle::with([
            'sections' => fn($q) => $q
                ->where('is_active', true)
                ->whereNull('deleted_at')
                ->orderBy('sort_order')
                ->with([
                    'subSections' => fn($q) => $q
                        ->where('is_active', true)
                        ->whereNull('deleted_at')
                        ->orderBy('sort_order')
                        ->with([
                            'clauses' => fn($q) => $q
                                ->where('is_active', true)
                                ->whereNull('deleted_at')
                                ->orderBy('sort_order')
                        ])
                ])
        ])
            ->where('is_active', true)       // ✅ Active articles only
            ->whereNull('deleted_at')         // ✅ Non-archived only
            ->orderBy('sort_order')
            ->get();
    }
}