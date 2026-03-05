<?php

namespace App\Services\Rule;

use Throwable;
use DomainException;
use App\Models\RuleArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RuleArticleService
{
    public function list()
    {
        return RuleArticle::whereNull('deleted_at') // ✅ Exclude archived
            ->orderBy('sort_order')
            ->get();
    }

    public function datatable(Request $request)
    {
        // ✅ Show ALL records including archived
        $query = RuleArticle::query();

        $total = $query->count();

        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%");
            });
        }

        $filtered = $query->count();

        $columns = ['number', 'title', 'sort_order', 'status', 'created_at', 'updated_at', 'actions'];
        $orderCol = $columns[$request->input('order.0.column', 0)] ?? 'sort_order';
        $orderDir = $request->input('order.0.dir', 'asc') === 'asc' ? 'asc' : 'desc';
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        if (!in_array($orderCol, ['status', 'actions'])) {
            $query->orderBy($orderCol, $orderDir);
        }

        $articles = $query->skip($start)->take($length)->get();

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $articles->map(function ($article) {
                return [
                    'number' => e($article->number),
                    'title' => e($article->title),
                    'sort_order' => $article->sort_order,
                    'status' => $article->is_active
                        ? '<span class="badge badge-success">Active</span>'
                        : '<span class="badge badge-danger">Inactive</span>',
                    'created_at' => $article->created_at->toDateTimeString(),
                    'updated_at' => $article->updated_at->toDateTimeString(),
                    'archived' => !is_null($article->deleted_at), // ✅ Pass archive state
                    'actions' => view(
                        'admin.rules_and_regulations.articles.partials.actions',
                        compact('article')
                    )->render(),
                ];
            })
        ]);
    }

    public function create(array $data): RuleArticle
    {
        return DB::transaction(function () use ($data) {
            try {
                $data['updated_by'] = Auth::id();

                // ✅ Restore if same number was archived
                $existing = RuleArticle::where('number', $data['number'])->first();

                if ($existing && !is_null($existing->deleted_at)) {
                    DB::table('rule_articles')->where('id', $existing->id)->update([
                        'number' => $data['number'],
                        'title' => $data['title'],
                        'sort_order' => $data['sort_order'] ?? 0,
                        'is_active' => true,
                        'deleted_at' => null,
                        'updated_by' => Auth::id(),
                    ]);
                    return $existing->fresh();
                }

                if ($existing) {
                    throw new DomainException('An article with this number already exists.');
                }

                return RuleArticle::create($data);

            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    public function update(RuleArticle $article, array $data): RuleArticle
    {
        return DB::transaction(function () use ($article, $data) {
            try {
                $data['updated_by'] = Auth::id();

                $conflict = RuleArticle::where('number', $data['number'])
                    ->where('id', '!=', $article->id)
                    ->whereNull('deleted_at')
                    ->first();

                if ($conflict) {
                    throw new DomainException('An article with this number already exists.');
                }

                $article->update($data);
                return $article;

            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    /**
     * ✅ Hard delete — must be inactive first
     */
    public function delete(RuleArticle $article): void
    {
        try {
            DB::transaction(function () use ($article) {

                // ✅ Guard: must be inactive before hard deleting
                if ($article->is_active) {
                    throw new DomainException(
                        "Cannot delete '{$article->title}'. Please deactivate it before deleting."
                    );
                }

                $sectionsCount = $article->sections()->count();
                if ($sectionsCount > 0) {
                    throw new DomainException(
                        "Cannot delete this article. It has {$sectionsCount} linked section(s)."
                    );
                }

                $article->delete();
            });
        } catch (DomainException $e) {
            throw $e;
        } catch (Throwable $e) {
            report($e);
            throw new DomainException('Failed to delete article.');
        }
    }

    /**
     * ✅ Archive — sets is_active = false + deleted_at = now()
     */
    public function archive(RuleArticle $article): RuleArticle
    {
        try {
            DB::table('rule_articles')->where('id', $article->id)->update([
                'is_active' => false,
                'deleted_at' => now(),
            ]);

            return $article->fresh();
        } catch (Throwable $e) {
            report($e);
            throw new DomainException('Failed to archive article.');
        }
    }

    /**
     * ✅ Unarchive — sets is_active = true + deleted_at = null
     */
    public function unarchive(RuleArticle $article): RuleArticle
    {
        try {
            DB::table('rule_articles')->where('id', $article->id)->update([
                'is_active' => true,
                'deleted_at' => null,
            ]);

            return $article->fresh();
        } catch (Throwable $e) {
            report($e);
            throw new DomainException('Failed to unarchive article.');
        }
    }
}

