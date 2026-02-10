<?php

namespace App\Services\ProspectiveStudent;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ProspectiveStudentCategory;

class ProspectiveStudentCategoryService
{
    public function list()
    {
        return ProspectiveStudentCategory::orderBy('sort_order')->get();
    }

    public function datatable(Request $request)
    {
        $query = ProspectiveStudentCategory::query();

        $total = $query->count();

        /* SEARCH */
        if ($search = $request->input('search.value')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $filtered = $query->count();

        /* ORDER */
        $columns = ['name', 'sort_order', 'created_at', 'updated_at'];
        $orderCol = $columns[$request->input('order.0.column')] ?? 'sort_order';
        $orderDir = $request->input('order.0.dir', 'asc');

        $query->orderBy($orderCol, $orderDir);

        /* PAGINATION */
        $items = $query
            ->skip($request->start)
            ->take($request->length)
            ->get();

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $items->map(function ($category) {
                return [
                    'name' => e($category->name),
                    'sort_order' => $category->sort_order,
                    'status' => $category->is_active
                        ? '<span class="badge badge-success">Active</span>'
                        : '<span class="badge badge-danger">Inactive</span>',
                    'created_at' => $category->created_at->toDateTimeString(),
                    'updated_at' => $category->updated_at->toDateTimeString(),
                    'actions' => view(
                        'admin.prospective_student.categories.partials.actions',
                        compact('category')
                    )->render()
                ];
            })
        ]);
    }

    public function createOrRestore(array $data): ProspectiveStudentCategory
    {
        return DB::transaction(function () use ($data) {
            try {
                $data['updated_by'] = Auth::id();

                $existing = ProspectiveStudentCategory::withTrashed()
                    ->where('name', $data['name'])
                    ->first();

                if ($existing) {
                    if ($existing->trashed()) {
                        $existing->restore();
                    }

                    $existing->update($data);
                    return $existing;
                }

                return ProspectiveStudentCategory::create($data);

            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    public function updateOrRestore(
        ProspectiveStudentCategory $current,
        array $data
    ): ProspectiveStudentCategory {
        return DB::transaction(function () use ($current, $data) {
            try {
                $data['updated_by'] = Auth::id();

                $existing = ProspectiveStudentCategory::withTrashed()
                    ->where('name', $data['name'])
                    ->where('id', '!=', $current->id)
                    ->first();

                if ($existing) {
                    if ($existing->trashed()) {
                        $existing->restore();
                    }

                    $existing->update($data);

                    // remove the old one to avoid duplicates
                    $current->delete();

                    return $existing;
                }

                $current->update($data);
                return $current;

            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }


    public function delete(ProspectiveStudentCategory $category): void
    {
        DB::transaction(function () use ($category) {
            try {
                if ($category->items()->exists()) {
                    abort(422, 'Category has items and cannot be deleted.');
                }

                $category->delete();

            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }
}
