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
            ->whereNull('deleted_at') // ✅ Exclude archived
            ->orderBy('sort_order')
            ->get();
    }

    public function datatable(Request $request)
    {
        // ✅ Show ALL records including archived
        $query = RuleSubSection::with('section.article');

        $total = $query->count();

        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas(
                    'section.article',
                    fn($q2) =>
                    $q2->where('rule_articles.number', 'like', "%{$search}%")
                        ->orWhere('rule_articles.title', 'like', "%{$search}%")
                )
                    ->orWhereHas(
                        'section',
                        fn($q2) =>
                        $q2->where('rule_sections.number', 'like', "%{$search}%")
                    )
                    ->orWhere('rule_sub_sections.number', 'like', "%{$search}%")
                    ->orWhere('rule_sub_sections.body', 'like', "%{$search}%");
            });
        }

        $filtered = $query->count();

        $columns = ['article', 'section', 'number', 'body', 'sort_order', 'status', 'actions'];
        $orderColIndex = $request->input('order.0.column', 4);
        $orderCol = $columns[$orderColIndex] ?? 'sort_order';
        $orderDir = $request->input('order.0.dir', 'asc') === 'asc' ? 'asc' : 'desc';
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        if ($orderCol === 'article') {
            $query->join('rule_sections', 'rule_sections.id', '=', 'rule_sub_sections.section_id')
                ->join('rule_articles', 'rule_articles.id', '=', 'rule_sections.article_id')
                ->orderBy('rule_articles.number', $orderDir)
                ->select('rule_sub_sections.*');
        } elseif ($orderCol === 'section') {
            $query->join('rule_sections', 'rule_sections.id', '=', 'rule_sub_sections.section_id')
                ->orderBy('rule_sections.number', $orderDir)
                ->select('rule_sub_sections.*');
        } elseif (!in_array($orderCol, ['status', 'actions'])) {
            $query->orderBy('rule_sub_sections.' . $orderCol, $orderDir);
        }

        $subSections = $query->skip($start)->take($length)->get();

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $subSections->map(function ($sub) {
                return [
                    'article' => $sub->section->article->number ?? '—',
                    'section' => $sub->section->number ?? '—',
                    'number' => e($sub->number),
                    'body' => Str::limit($sub->body, 80),
                    'sort_order' => $sub->sort_order,
                    'status' => $sub->is_active
                        ? '<span class="badge badge-success">Active</span>'
                        : '<span class="badge badge-danger">Inactive</span>',
                    'archived' => !is_null($sub->deleted_at), // ✅ Pass archive state
                    'actions' => view(
                        'admin.rules_and_regulations.sub_sections.partials.actions',
                        compact('sub')
                    )->render(),
                ];
            })
        ]);
    }

    public function create(array $data): RuleSubSection
    {
        return DB::transaction(function () use ($data) {
            try {
                $data['updated_by'] = Auth::id();

                // ✅ Restore if same section+number was archived
                $existing = RuleSubSection::where('section_id', $data['section_id'])
                    ->where('number', $data['number'])
                    ->first();

                if ($existing && !is_null($existing->deleted_at)) {
                    DB::table('rule_sub_sections')->where('id', $existing->id)->update([
                        'section_id' => $data['section_id'],
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
                    throw new DomainException('A sub-section with this number already exists in this section.');
                }

                return RuleSubSection::create($data);

            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    public function update(RuleSubSection $subSection, array $data): RuleSubSection
    {
        return DB::transaction(function () use ($subSection, $data) {
            try {
                $data['updated_by'] = Auth::id();

                $conflict = RuleSubSection::where('section_id', $data['section_id'])
                    ->where('number', $data['number'])
                    ->where('id', '!=', $subSection->id)
                    ->whereNull('deleted_at')
                    ->first();

                if ($conflict) {
                    throw new DomainException('A sub-section with this number already exists in this section.');
                }

                $subSection->update($data);
                return $subSection;

            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    /**
     * ✅ Hard delete — must be inactive first
     */
    public function delete(RuleSubSection $subSection): void
    {
        try {
            DB::transaction(function () use ($subSection) {

                // ✅ Guard: must be inactive before hard deleting
                if ($subSection->is_active) {
                    throw new DomainException(
                        "Cannot delete this sub-section. Please deactivate it before deleting."
                    );
                }

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

    /**
     * ✅ Archive — sets is_active = false + deleted_at = now()
     */
    public function archive(RuleSubSection $subSection): RuleSubSection
    {
        try {
            DB::table('rule_sub_sections')->where('id', $subSection->id)->update([
                'is_active' => false,
                'deleted_at' => now(),
            ]);

            return $subSection->fresh();
        } catch (Throwable $e) {
            report($e);
            throw new DomainException('Failed to archive sub-section.');
        }
    }

    /**
     * ✅ Unarchive — sets is_active = true + deleted_at = null
     */
    public function unarchive(RuleSubSection $subSection): RuleSubSection
    {
        try {
            DB::table('rule_sub_sections')->where('id', $subSection->id)->update([
                'is_active' => true,
                'deleted_at' => null,
            ]);

            return $subSection->fresh();
        } catch (Throwable $e) {
            report($e);
            throw new DomainException('Failed to unarchive sub-section.');
        }
    }
}