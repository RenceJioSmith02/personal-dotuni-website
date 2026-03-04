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
        return LinkageCategory::whereNull('deleted_at') // ✅ Exclude archived
            ->orderBy('sort_order')
            ->get();
    }

    public function datatable(Request $request)
    {
        $query = LinkageCategory::query();

        $total = $query->count();

        if ($search = $request->input('search.value')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $filtered = $query->count();

        // ✅ Matches the thead column order exactly
        $columns = ['name', 'sort_order', 'status', 'created_at', 'updated_at', 'actions'];
        $orderColumn = $columns[$request->input('order.0.column', 0)] ?? 'sort_order';
        $orderDir = $request->input('order.0.dir', 'asc') === 'asc' ? 'asc' : 'desc';

        // ✅ Skip non-DB columns
        if (!in_array($orderColumn, ['status', 'actions'])) {
            $query->orderBy($orderColumn, $orderDir);
        }

        $data = $query
            ->skip($request->start)
            ->take($request->length)
            ->get();

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data->map(fn($c) => [
                'name' => $c->name,
                'sort_order' => $c->sort_order,
                'status' => $c->is_active // ✅ Add
                    ? '<span class="badge badge-success">Active</span>'
                    : '<span class="badge badge-danger">Inactive</span>',
                'created_at' => $c->created_at->toDateTimeString(),
                'updated_at' => $c->updated_at->toDateTimeString(),
                'archived' => !is_null($c->deleted_at),
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
            // ✅ Restore if same name was archived
            $existing = LinkageCategory::where('name', $data['name'])->first();

            if ($existing && !is_null($existing->deleted_at)) {
                DB::table('linkage_categories')->where('id', $existing->id)->update([
                    'name' => $data['name'],
                    'sort_order' => $data['sort_order'] ?? 0,
                    'deleted_at' => null,
                ]);
                return $existing->fresh();
            }

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

    /**
     * ✅ Hard delete — must be inactive first
     */
    public function delete(LinkageCategory $category): void
    {
        try {
            DB::transaction(function () use ($category) {

                // ✅ Guard: must be inactive before hard deleting
                if ($category->is_active) {
                    throw new DomainException(
                        "Cannot delete '{$category->name}'. Please deactivate it before deleting."
                    );
                }

                if ($category->linkages()->exists()) {
                    throw new DomainException(
                        "Cannot delete '{$category->name}'. It has linkages assigned to it."
                    );
                }

                $category->delete();
            });
        } catch (DomainException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);
            throw new DomainException('Failed to delete category.');
        }
    }

    
    /**
     * ✅ Archive — sets is_active = false + deleted_at = now()
     */
    public function archive(LinkageCategory $category): LinkageCategory
    {
        try {
            DB::table('linkage_categories')->where('id', $category->id)->update([
                'is_active' => false,  // ✅ Add
                'deleted_at' => now(),
            ]);

            return $category->fresh();
        } catch (\Throwable $e) {
            report($e);
            throw new DomainException('Failed to archive category.');
        }
    }

    /**
     * ✅ Unarchive — sets is_active = true + deleted_at = null
     */
    public function unarchive(LinkageCategory $category): LinkageCategory
    {
        try {
            DB::table('linkage_categories')->where('id', $category->id)->update([
                'is_active' => true,   // ✅ Add
                'deleted_at' => null,
            ]);

            return $category->fresh();
        } catch (\Throwable $e) {
            report($e);
            throw new DomainException('Failed to unarchive category.');
        }
    }


}