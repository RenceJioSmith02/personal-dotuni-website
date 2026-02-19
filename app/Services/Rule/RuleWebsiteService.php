<?php

namespace App\Services\Rule;

use App\Models\RuleArticle;

class RuleWebsiteService
{
    public function list()
    {
        return RuleArticle::with([
            'sections.subSections.clauses'
        ])
            ->orderBy('sort_order')
            ->get();
    }
}
