<?php

namespace App\Services\Rule;

use Throwable;
use DomainException;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\RuleSubSection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RuleSubSectionService
{
    public function list()
    {
        return RuleSubSection::with('section.article')
            ->orderBy('sort_order')
            ->get();
    }

    public function datatable(Request $request)
    {
        $query = RuleSubSection::with('section.article');

        $total = $query->count();

        /* SEARCH */
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('section.article', function ($q2) use ($search) {
                    $q2->where('rule_articles.number', 'like', "%{$search}%")
                        ->orWhere('rule_articles.title', 'like', "%{$search}%");
                })
                    ->orWhereHas('section', function ($q2) use ($search) {
                        $q2->where('rule_sections.number', 'like', "%{$search}%");
                    })
                    ->orWhere('rule_sub_sections.number', 'like', "%{$search}%")
                    ->orWhere('rule_sub_sections.body', 'like', "%{$search}%");
            });
        }

        $filtered = $query->count();

        /* ORDER */
        $columns = ['article', 'section', 'number', 'body', 'sort_order'];
        $orderColIndex = $request->input('order.0.column', 4);
        $orderDir = $request->input('order.0.dir', 'asc');
        $orderCol = $columns[$orderColIndex] ?? 'sort_order';

        if ($orderCol === 'article') {
            $query->join('rule_sections', 'rule_sections.id', '=', 'rule_sub_sections.section_id')
                ->join('rule_articles', 'rule_articles.id', '=', 'rule_sections.article_id')
                ->orderBy('rule_articles.number', $orderDir)
                ->select('rule_sub_sections.*');
        } elseif ($orderCol === 'section') {
            $query->join('rule_sections', 'rule_sections.id', '=', 'rule_sub_sections.section_id')
                ->orderBy('rule_sections.number', $orderDir)
                ->select('rule_sub_sections.*');
        } else {
            $query->orderBy('rule_sub_sections.' . $orderCol, $orderDir);
        }

        /* PAGINATION */
        $subSections = $query->skip($request->start)->take($request->length)->get();

        /* RESPONSE */
        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $subSections->map(function ($sub) {
                return [
                    'article' => $sub->section->article->number ?? '-',
                    'section' => $sub->section->number ?? '-',
                    'number' => e($sub->number),
                    'body' => Str::limit($sub->body, 80),
                    'sort_order' => $sub->sort_order,
                    'actions' => view(
                        'admin.rules_and_regulations.sub_sections.partials.actions',
                        compact('sub')
                    )->render()
                ];
            })
        ]);
    }


    public function storeOrRestore(array $data): RuleSubSection
    {
        return DB::transaction(function () use ($data) {
            try {
                $data['updated_by'] = Auth::id();

                $existing = RuleSubSection::withTrashed()
                    ->where('section_id', $data['section_id'])
                    ->where('number', $data['number'])
                    ->first();

                if ($existing) {
                    if ($existing->trashed()) {
                        $existing->restore();
                    }

                    $existing->update($data);
                    return $existing;
                }

                return RuleSubSection::create($data);

            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    public function updateOrRestore(RuleSubSection $current, array $data): RuleSubSection
    {
        return DB::transaction(function () use ($current, $data) {
            try {
                $data['updated_by'] = Auth::id();

                $conflict = RuleSubSection::withTrashed()
                    ->where('section_id', $data['section_id'])
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


    public function delete(RuleSubSection $subSection): void
    {
        try {
            DB::transaction(function () use ($subSection) {

                $clauseCount = $subSection->clauses()->count();

                if ($clauseCount > 0) {
                    throw new DomainException(
                        "Cannot delete this sub-section. It has {$clauseCount} linked clause(s)."
                    );
                }

                $subSection->delete();
            });
        } catch (DomainException $e) {
            throw $e;
        } catch (Throwable $e) {
            report($e);
            throw new DomainException('Failed to delete sub-section.');
        }
    }

}
