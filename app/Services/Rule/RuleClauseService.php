<?php

namespace App\Services\Rule;

use App\Models\RuleClause;
use DomainException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;
use Illuminate\Http\Request;

class RuleClauseService
{
    public function list()
    {
        return RuleClause::with('subSection.section.article')
            ->whereNull('deleted_at') // ✅ Exclude archived
            ->orderBy('sort_order')
            ->get();
    }

    public function datatable(Request $request)
    {
        // ✅ Show ALL records including archived
        $query = RuleClause::with('subSection.section.article');

        $total = $query->count();

        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas(
                    'subSection.section.article',
                    fn($q2) =>
                    $q2->where('rule_articles.number', 'like', "%{$search}%")
                )
                    ->orWhereHas(
                        'subSection.section',
                        fn($q2) =>
                        $q2->where('rule_sections.number', 'like', "%{$search}%")
                    )
                    ->orWhereHas(
                        'subSection',
                        fn($q2) =>
                        $q2->where('rule_sub_sections.number', 'like', "%{$search}%")
                    )
                    ->orWhere('rule_clauses.number', 'like', "%{$search}%")
                    ->orWhere('rule_clauses.body', 'like', "%{$search}%");
            });
        }

        $filtered = $query->count();

        $columns = ['article', 'section', 'sub_section', 'number', 'body', 'sort_order', 'status', 'actions'];
        $orderColIndex = $request->input('order.0.column', 5);
        $orderCol = $columns[$orderColIndex] ?? 'sort_order';
        $orderDir = $request->input('order.0.dir', 'asc') === 'asc' ? 'asc' : 'desc';
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

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
        } elseif (!in_array($orderCol, ['status', 'actions'])) {
            $query->orderBy('rule_clauses.' . $orderCol, $orderDir);
        }

        $clauses = $query->skip($start)->take($length)->get();

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $clauses->map(function ($clause) {
                return [
                    'article' => $clause->subSection->section->article->number ?? '—',
                    'section' => $clause->subSection->section->number ?? '—',
                    'sub_section' => $clause->subSection->number ?? '—',
                    'number' => e($clause->number),
                    'body' => Str::limit($clause->body, 80),
                    'sort_order' => $clause->sort_order,
                    'status' => $clause->is_active
                        ? '<span class="badge badge-success">Active</span>'
                        : '<span class="badge badge-danger">Inactive</span>',
                    'archived' => !is_null($clause->deleted_at), // ✅ Pass archive state
                    'actions' => view(
                        'admin.rules_and_regulations.clauses.partials.actions',
                        compact('clause')
                    )->render(),
                ];
            })
        ]);
    }

    public function create(array $data): RuleClause
    {
        return DB::transaction(function () use ($data) {
            try {
                $data['updated_by'] = Auth::id();

                // ✅ Restore if same sub_section+number was archived
                $existing = RuleClause::where('sub_section_id', $data['sub_section_id'])
                    ->where('number', $data['number'])
                    ->first();

                if ($existing && !is_null($existing->deleted_at)) {
                    DB::table('rule_clauses')->where('id', $existing->id)->update([
                        'sub_section_id' => $data['sub_section_id'],
                        'number' => $data['number'],
                        'body' => $data['body'],
                        'sort_order' => $data['sort_order'] ?? 0,
                        'is_active' => true,
                        'deleted_at' => null,
                        'updated_by' => Auth::id(),
                    ]);
                    return $existing->fresh();
                }

                if ($existing) {
                    throw new DomainException('A clause with this number already exists in this sub-section.');
                }

                return RuleClause::create($data);

            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    public function update(RuleClause $clause, array $data): RuleClause
    {
        return DB::transaction(function () use ($clause, $data) {
            try {
                $data['updated_by'] = Auth::id();

                $conflict = RuleClause::where('sub_section_id', $data['sub_section_id'])
                    ->where('number', $data['number'])
                    ->where('id', '!=', $clause->id)
                    ->whereNull('deleted_at')
                    ->first();

                if ($conflict) {
                    throw new DomainException('A clause with this number already exists in this sub-section.');
                }

                $clause->update($data);
                return $clause;

            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    /**
     * ✅ Hard delete — must be inactive first
     */
    public function delete(RuleClause $clause): void
    {
        try {
            DB::transaction(function () use ($clause) {

                // ✅ Guard: must be inactive before hard deleting
                if ($clause->is_active) {
                    throw new DomainException(
                        "Cannot delete this clause. Please deactivate it before deleting."
                    );
                }

                $clause->delete();
            });
        } catch (DomainException $e) {
            throw $e;
        } catch (Throwable $e) {
            report($e);
            throw new DomainException('Failed to delete clause.');
        }
    }

    /**
     * ✅ Archive — sets is_active = false + deleted_at = now()
     */
    public function archive(RuleClause $clause): RuleClause
    {
        try {
            DB::table('rule_clauses')->where('id', $clause->id)->update([
                'is_active' => false,
                'deleted_at' => now(),
            ]);

            return $clause->fresh();
        } catch (Throwable $e) {
            report($e);
            throw new DomainException('Failed to archive clause.');
        }
    }

    /**
     * ✅ Unarchive — sets is_active = true + deleted_at = null
     */
    public function unarchive(RuleClause $clause): RuleClause
    {
        try {
            DB::table('rule_clauses')->where('id', $clause->id)->update([
                'is_active' => true,
                'deleted_at' => null,
            ]);

            return $clause->fresh();
        } catch (Throwable $e) {
            report($e);
            throw new DomainException('Failed to unarchive clause.');
        }
    }
}