<?php

namespace App\Services\Rule;

use App\Models\RuleArticle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;
use Illuminate\Http\Request;

class RuleArticleService
{
    public function list()
    {
        return RuleArticle::orderBy('sort_order')->get();
    }



    public function datatable(Request $request)
    {
        $query = RuleArticle::query();

        $total = $query->count();

        /* SEARCH */
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%");
            });
        }

        $filtered = $query->count();

        /* ORDER */
        $columns = ['number', 'title', 'sort_order'];
        $orderCol = $columns[$request->input('order.0.column')] ?? 'sort_order';
        $orderDir = $request->input('order.0.dir', 'asc');

        $query->orderBy($orderCol, $orderDir);

        /* PAGINATION */
        $articles = $query
            ->skip($request->start)
            ->take($request->length)
            ->get();

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $articles->map(function ($article) {
                return [
                    'number' => e($article->number),
                    'title' => e($article->title),
                    'sort_order' => $article->sort_order,
                    'actions' => view(
                        'admin.rules_and_regulations.articles.partials.actions',
                        compact('article')
                    )->render()
                ];
            })
        ]);
    }


    public function storeOrRestore(array $data): RuleArticle
    {
        return DB::transaction(function () use ($data) {
            try {
                $data['updated_by'] = Auth::id();

                $existing = RuleArticle::withTrashed()
                    ->where('number', $data['number'])
                    ->first();

                if ($existing) {
                    if ($existing->trashed()) {
                        $existing->restore();
                    }

                    $existing->update($data);
                    return $existing;
                }

                return RuleArticle::create($data);

            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    public function updateOrRestore(RuleArticle $current, array $data): RuleArticle
    {
        return DB::transaction(function () use ($current, $data) {
            try {
                $data['updated_by'] = Auth::id();

                $conflict = RuleArticle::withTrashed()
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

    public function delete(RuleArticle $article): void
    {
        DB::transaction(function () use ($article) {
            $article->delete();
        });
    }
}
