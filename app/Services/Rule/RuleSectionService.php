<?php

namespace App\Services\Rule;

use Throwable;
use DomainException;
use App\Models\RuleSection;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RuleSectionService
{
    public function list()
    {
        return RuleSection::with('article')
            ->whereNull('deleted_at') // ✅ Exclude archived
            ->orderBy('sort_order')
            ->get();
    }

    public function datatable(Request $request)
    {
        // ✅ Show ALL records including archived
        $query = RuleSection::with('article');

        $total = $query->count();

        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('article', fn($q2) => $q2->where('rule_articles.number', 'like', "%{$search}%"))
                    ->orWhere('rule_sections.number', 'like', "%{$search}%")
                    ->orWhere('rule_sections.body', 'like', "%{$search}%");
            });
        }

        $filtered = $query->count();

        $columns = ['article', 'number', 'body', 'sort_order', 'status', 'actions'];
        $orderColIndex = $request->input('order.0.column', 3);
        $orderCol = $columns[$orderColIndex] ?? 'sort_order';
        $orderDir = $request->input('order.0.dir', 'asc') === 'asc' ? 'asc' : 'desc';
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        if ($orderCol === 'article') {
            $query->join('rule_articles', 'rule_articles.id', '=', 'rule_sections.article_id')
                ->orderBy('rule_articles.number', $orderDir)
                ->select('rule_sections.*');
        } elseif (!in_array($orderCol, ['status', 'actions'])) {
            $query->orderBy('rule_sections.' . $orderCol, $orderDir);
        }

        $sections = $query->skip($start)->take($length)->get();

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $sections->map(function ($section) {
                return [
                    'article' => $section->article->number ?? '—',
                    'number' => e($section->number),
                    'body' => Str::limit($section->body, 80),
                    'sort_order' => $section->sort_order,
                    'status' => $section->is_active
                        ? '<span class="badge badge-success">Active</span>'
                        : '<span class="badge badge-danger">Inactive</span>',
                    'archived' => !is_null($section->deleted_at), // ✅ Pass archive state
                    'actions' => view(
                        'admin.rules_and_regulations.sections.partials.actions',
                        compact('section')
                    )->render(),
                ];
            })
        ]);
    }

    public function create(array $data): RuleSection
    {
        return DB::transaction(function () use ($data) {
            try {
                $data['updated_by'] = Auth::id();

                // ✅ Restore if same article+number was archived
                $existing = RuleSection::where('article_id', $data['article_id'])
                    ->where('number', $data['number'])
                    ->first();

                if ($existing && !is_null($existing->deleted_at)) {
                    DB::table('rule_sections')->where('id', $existing->id)->update([
                        'article_id' => $data['article_id'],
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
                    throw new DomainException('A section with this number already exists in this article.');
                }

                return RuleSection::create($data);

            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    public function update(RuleSection $section, array $data): RuleSection
    {
        return DB::transaction(function () use ($section, $data) {
            try {
                $data['updated_by'] = Auth::id();

                $conflict = RuleSection::where('article_id', $data['article_id'])
                    ->where('number', $data['number'])
                    ->where('id', '!=', $section->id)
                    ->whereNull('deleted_at')
                    ->first();

                if ($conflict) {
                    throw new DomainException('A section with this number already exists in this article.');
                }

                $section->update($data);
                return $section;

            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    /**
     * ✅ Hard delete — must be inactive first
     */
    public function delete(RuleSection $section): void
    {
        try {
            DB::transaction(function () use ($section) {

                // ✅ Guard: must be inactive before hard deleting
                if ($section->is_active) {
                    throw new DomainException(
                        "Cannot delete this section. Please deactivate it before deleting."
                    );
                }

                $subSectionCount = $section->subSections()->count();
                if ($subSectionCount > 0) {
                    throw new DomainException(
                        "Cannot delete this section. It has {$subSectionCount} linked sub-section(s)."
                    );
                }

                $section->delete();
            });
        } catch (DomainException $e) {
            throw $e;
        } catch (Throwable $e) {
            report($e);
            throw new DomainException('Failed to delete section.');
        }
    }

    /**
     * ✅ Archive — sets is_active = false + deleted_at = now()
     */
    public function archive(RuleSection $section): RuleSection
    {
        try {
            DB::table('rule_sections')->where('id', $section->id)->update([
                'is_active' => false,
                'deleted_at' => now(),
            ]);

            return $section->fresh();
        } catch (Throwable $e) {
            report($e);
            throw new DomainException('Failed to archive section.');
        }
    }

    /**
     * ✅ Unarchive — sets is_active = true + deleted_at = null
     */
    public function unarchive(RuleSection $section): RuleSection
    {
        try {
            DB::table('rule_sections')->where('id', $section->id)->update([
                'is_active' => true,
                'deleted_at' => null,
            ]);

            return $section->fresh();
        } catch (Throwable $e) {
            report($e);
            throw new DomainException('Failed to unarchive section.');
        }
    }
}