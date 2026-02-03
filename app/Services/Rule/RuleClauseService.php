<?php

namespace App\Services\Rule;

use App\Models\RuleClause;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;
use Illuminate\Http\Request;

class RuleClauseService
{
    public function list()
    {
        return RuleClause::with('subSection.section.article')
            ->orderBy('sort_order')
            ->get();
    }


    public function datatable(Request $request)
    {
        $query = RuleClause::with('subSection.section.article');

        $total = $query->count();

        /* SEARCH */
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                // Search in article number
                $q->whereHas('subSection.section.article', function ($q2) use ($search) {
                    $q2->where('rule_articles.number', 'like', "%{$search}%");
                })
                    // Search in section number
                    ->orWhereHas('subSection.section', function ($q2) use ($search) {
                        $q2->where('rule_sections.number', 'like', "%{$search}%");
                    })
                    // Search in sub-section number
                    ->orWhereHas('subSection', function ($q2) use ($search) {
                        $q2->where('rule_sub_sections.number', 'like', "%{$search}%");
                    })
                    // Search in clause number and body
                    ->orWhere('rule_clauses.number', 'like', "%{$search}%")
                    ->orWhere('rule_clauses.body', 'like', "%{$search}%");
            });
        }

        $filtered = $query->count();

        /* ORDERING */
        $columns = ['article', 'section', 'sub_section', 'number', 'body', 'sort_order'];
        $orderColIndex = $request->input('order.0.column', 5);
        $orderDir = $request->input('order.0.dir', 'asc');
        $orderCol = $columns[$orderColIndex] ?? 'sort_order';

        if ($orderCol === 'article') {
            $query->join('rule_sub_sections', 'rule_sub_sections.id', '=', 'rule_clauses.sub_section_id')
                ->join('rule_sections', 'rule_sections.id', '=', 'rule_sub_sections.section_id')
                ->join('rule_articles', 'rule_articles.id', '=', 'rule_sections.article_id')
                ->orderBy('rule_articles.number', $orderDir)
                ->select('rule_clauses.*');
        } elseif ($orderCol === 'section') {
            $query->join('rule_sub_sections', 'rule_sub_sections.id', '=', 'rule_clauses.sub_section_id')
                ->join('rule_sections', 'rule_sections.id', '=', 'rule_sub_sections.section_id')
                ->orderBy('rule_sections.number', $orderDir)
                ->select('rule_clauses.*');
        } elseif ($orderCol === 'sub_section') {
            $query->join('rule_sub_sections', 'rule_sub_sections.id', '=', 'rule_clauses.sub_section_id')
                ->orderBy('rule_sub_sections.number', $orderDir)
                ->select('rule_clauses.*');
        } else {
            // Fully qualify clause columns for safety
            $query->orderBy('rule_clauses.' . $orderCol, $orderDir);
        }

        /* PAGINATION */
        $clauses = $query->skip($request->start)->take($request->length)->get();

        /* RESPONSE */
        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $clauses->map(function ($clause) {
                return [
                    'article' => $clause->subSection->section->article->number ?? '-',
                    'section' => $clause->subSection->section->number ?? '-',
                    'sub_section' => $clause->subSection->number ?? '-',
                    'number' => $clause->number,
                    'body' => \Str::limit($clause->body, 80),
                    'sort_order' => $clause->sort_order,
                    'actions' => view('admin.rules_and_regulations.clauses.partials.actions', compact('clause'))->render()
                ];
            })
        ]);
    }



    public function storeOrRestore(array $data): RuleClause
    {
        return DB::transaction(function () use ($data) {
            try {
                $data['updated_by'] = Auth::id();

                $existing = RuleClause::withTrashed()
                    ->where('sub_section_id', $data['sub_section_id'])
                    ->where('number', $data['number'])
                    ->first();

                if ($existing) {
                    if ($existing->trashed()) {
                        $existing->restore();
                    }

                    $existing->update($data);
                    return $existing;
                }

                return RuleClause::create($data);

            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    public function updateOrRestore(RuleClause $current, array $data): RuleClause
    {
        return DB::transaction(function () use ($current, $data) {
            try {
                $data['updated_by'] = Auth::id();

                $conflict = RuleClause::withTrashed()
                    ->where('sub_section_id', $data['sub_section_id'])
                    ->where('number', $data['number'])
                    ->where('id', '!=', $current->id)
                    ->first();

                if ($conflict) {
                    if ($conflict->trashed()) {
                        $conflict->restore();
                    }

                    $conflict->update($data);
                    $current->delete();

                    return $conflict;
                }

                $current->update($data);
                return $current;

            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    public function delete(RuleClause $clause): void
    {
        DB::transaction(fn() => $clause->delete());
    }
}
