<?php

namespace App\Services\ProspectiveStudent;

use DomainException;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ProspectiveStudentCategory;

class ProspectiveStudentCategoryService
{
    public function list()
    {
        return ProspectiveStudentCategory::whereNull('deleted_at') // ✅ Exclude archived
            ->orderBy('sort_order')
            ->get();
    }

    public function datatable(Request $request)
    {
        // ✅ Show ALL records including archived
        $query = ProspectiveStudentCategory::query();

        $total = $query->count();

        if ($search = $request->input('search.value')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $filtered = $query->count();

        $columns = ['name', 'sort_order', 'status', 'created_at', 'updated_at', 'actions'];
        $orderCol = $columns[$request->input('order.0.column', 0)] ?? 'sort_order';
        $orderDir = $request->input('order.0.dir', 'asc') === 'asc' ? 'asc' : 'desc';
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        if (!in_array($orderCol, ['status', 'actions'])) {
            $query->orderBy($orderCol, $orderDir);
        }

        $items = $query->skip($start)->take($length)->get();

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
                    'archived' => !is_null($category->deleted_at), // ✅ Pass archive state
                    'actions' => view(
                        'admin.prospective_student.categories.partials.actions',
                        compact('category')
                    )->render(),
                ];
            })
        ]);
    }

    public function create(array $data): ProspectiveStudentCategory
    {
        return DB::transaction(function () use ($data) {
            try {
                $data['updated_by'] = Auth::id();

                // ✅ Restore if same name was archived
                $existing = ProspectiveStudentCategory::where('name', $data['name'])->first();

                if ($existing && !is_null($existing->deleted_at)) {
                    DB::table('prospective_student_categories')->where('id', $existing->id)->update([
                        'name' => $data['name'],
                        'sort_order' => $data['sort_order'] ?? 0,
                        'is_active' => $data['is_active'] ?? true,
                        'deleted_at' => null,
                        'updated_by' => Auth::id(),
                    ]);
                    return $existing->fresh();
                }

                if ($existing) {
                    throw new DomainException('Category already exists.');
                }

                return ProspectiveStudentCategory::create($data);

            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    public function update(
        ProspectiveStudentCategory $current,
        array $data
    ): ProspectiveStudentCategory {
        return DB::transaction(function () use ($current, $data) {
            try {
                $data['updated_by'] = Auth::id();

                $conflict = ProspectiveStudentCategory::where('name', $data['name'])
                    ->where('id', '!=', $current->id)
                    ->whereNull('deleted_at')
                    ->first();

                if ($conflict) {
                    throw new DomainException('A category with this name already exists.');
                }

                $current->update($data);
                return $current;

            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    /**
     * ✅ Hard delete — must be inactive first
     */
    public function delete(ProspectiveStudentCategory $category): void
    {
        try {
            DB::transaction(function () use ($category) {

                // ✅ Guard: must be inactive before hard deleting
                if ($category->is_active) {
                    throw new DomainException(
                        "Cannot delete '{$category->name}'. Please deactivate it before deleting."
                    );
                }

                if ($category->items()->exists()) {
                    throw new DomainException(
                        "Cannot delete '{$category->name}'. It has items assigned to it."
                    );
                }

                $category->delete();
            });
        } catch (DomainException $e) {
            throw $e;
        } catch (Throwable $e) {
            report($e);
            throw new DomainException('Failed to delete category.');
        }
    }

    /**
     * ✅ Archive — sets is_active = false + deleted_at = now()
     */
    public function archive(ProspectiveStudentCategory $category): ProspectiveStudentCategory
    {
        try {
            DB::table('prospective_student_categories')->where('id', $category->id)->update([
                'is_active' => false,
                'deleted_at' => now(),
            ]);

            return $category->fresh();
        } catch (Throwable $e) {
            report($e);
            throw new DomainException('Failed to archive category.');
        }
    }

    /**
     * ✅ Unarchive — sets is_active = true + deleted_at = null
     */
    public function unarchive(ProspectiveStudentCategory $category): ProspectiveStudentCategory
    {
        try {
            DB::table('prospective_student_categories')->where('id', $category->id)->update([
                'is_active' => true,
                'deleted_at' => null,
            ]);

            return $category->fresh();
        } catch (Throwable $e) {
            report($e);
            throw new DomainException('Failed to unarchive category.');
        }
    }
}
