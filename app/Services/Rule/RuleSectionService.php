<?php

namespace App\Services\Rule;

use App\Models\RuleSection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RuleSectionService
{
    public function list()
    {
        return RuleSection::with('article')
            ->orderBy('sort_order')
            ->get();
    }

    public function datatable(Request $request)
    {
        $query = RuleSection::with('article');

        $total = $query->count();

        /* SEARCH */
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                // Search related article number
                $q->whereHas('article', function ($q2) use ($search) {
                    $q2->where('rule_articles.number', 'like', "%{$search}%");
                })
                    // Search section number and body
                    ->orWhere('rule_sections.number', 'like', "%{$search}%")
                    ->orWhere('rule_sections.body', 'like', "%{$search}%");
            });
        }

        $filtered = $query->count();

        /* ORDER */
        $columns = ['article', 'number', 'body', 'sort_order'];
        $orderColIndex = $request->input('order.0.column', 3);
        $orderDir = $request->input('order.0.dir', 'asc');
        $orderCol = $columns[$orderColIndex] ?? 'sort_order';

        if ($orderCol === 'article') {
            $query->join('rule_articles', 'rule_articles.id', '=', 'rule_sections.article_id')
                ->orderBy('rule_articles.number', $orderDir)
                ->select('rule_sections.*'); // important: select only the main table
        } else {
            $query->orderBy('rule_sections.' . $orderCol, $orderDir);
        }

        /* PAGINATION */
        $sections = $query->skip($request->start)->take($request->length)->get();

        /* RESPONSE */
        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $sections->map(function ($section) {
                return [
                    'article' => $section->article->number ?? '-',
                    'number' => e($section->number),
                    'body' => Str::limit($section->body, 80),
                    'sort_order' => $section->sort_order,
                    'actions' => view(
                        'admin.rules_and_regulations.sections.partials.actions',
                        compact('section')
                    )->render()
                ];
            })
        ]);
    }

    
    public function storeOrRestore(array $data): RuleSection
    {
        return DB::transaction(function () use ($data) {
            try {
                $data['updated_by'] = Auth::id();

                $existing = RuleSection::withTrashed()
                    ->where('article_id', $data['article_id'])
                    ->where('number', $data['number'])
                    ->first();

                if ($existing) {
                    if ($existing->trashed()) {
                        $existing->restore();
                    }

                    $existing->update($data);
                    return $existing;
                }

                return RuleSection::create($data);

            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    public function updateOrRestore(RuleSection $current, array $data): RuleSection
    {
        return DB::transaction(function () use ($current, $data) {
            try {
                $data['updated_by'] = Auth::id();

                $conflict = RuleSection::withTrashed()
                    ->where('article_id', $data['article_id'])
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

    public function delete(RuleSection $section): void
    {
        DB::transaction(fn() => $section->delete());
    }
}
