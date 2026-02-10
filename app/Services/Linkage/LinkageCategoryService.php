<?php

namespace App\Services\Linkage;

use App\Models\LinkageCategory;
use Illuminate\Support\Facades\DB;
use DomainException;
use Illuminate\Http\Request;

class LinkageCategoryService
{

    public function list()
    {
        return LinkageCategory::orderBy('sort_order')->get();
    }

    public function datatable(Request $request)
    {
        $query = LinkageCategory::query();

        $total = $query->count();

        /* ======================
         * SEARCH
         * ====================== */
        if ($search = $request->input('search.value')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $filtered = $query->count();

        /* ======================
         * ORDERING
         * ====================== */
        $columns = ['name', 'sort_order', 'created_at', 'updated_at'];
        $orderColumn = $columns[$request->input('order.0.column', 0)] ?? 'sort_order';
        $orderDir = $request->input('order.0.dir', 'asc');

        $query->orderBy($orderColumn, $orderDir);

        /* ======================
         * PAGINATION
         * ====================== */
        $data = $query
            ->skip($request->start)
            ->take($request->length)
            ->get();

        /* ======================
         * RESPONSE FORMAT
         * ====================== */
        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data->map(fn($c) => [
                'name' => $c->name,
                'sort_order' => $c->sort_order,
                'created_at' => $c->created_at->toDateTimeString(),
                'updated_at' => $c->updated_at->toDateTimeString(),
                'actions' => view(
                    'admin.linkage.categories.partials.actions',
                    compact('c')
                )->render()
            ])
        ]);
    }


    public function create(array $data): LinkageCategory
    {
        return DB::transaction(function () use ($data) {
            return LinkageCategory::create([
                'name' => $data['name'],
                'sort_order' => $data['sort_order'] ?? 0,
            ]);
        });
    }

    public function update(LinkageCategory $category, array $data): LinkageCategory
    {
        return DB::transaction(function () use ($category, $data) {
            $category->update([
                'name' => $data['name'],
                'sort_order' => $data['sort_order'] ?? 0,
            ]);

            return $category;
        });
    }

    public function delete(LinkageCategory $category): void
    {
        if ($category->linkages()->exists()) {
            throw new DomainException('Category has linkages and cannot be deleted.');
        }

        $category->delete();
    }
}
