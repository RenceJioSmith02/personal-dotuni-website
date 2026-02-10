<?php

namespace App\Services\Academic;

use App\Models\ProgramRequirementCategory;
use Illuminate\Support\Facades\DB;
use DomainException;
use Exception;
use Illuminate\Http\Request;


class ProgramRequirementCategoryService
{
    // Returns all categories (for non-DataTables usage)
    public function list()
    {
        return ProgramRequirementCategory::orderBy('sort_order')->get();
    }

    // Server-side DataTables
    public function datatable(Request $request)
    {
        $query = ProgramRequirementCategory::query();

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
        $columns = ['name', 'sort_order', 'status', 'created_at', 'updated_at', 'actions'];
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
         * RESPONSE
         * ====================== */
        return [
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data->map(fn($c) => [
                'name' => $c->name,
                'sort_order' => $c->sort_order,
            'created_at' => $c->created_at->toDateTimeString(),
            'updated_at' => $c->updated_at->toDateTimeString(),
                'actions' => view(
                    'admin.academic.program_requirement_categories.partials.actions',
                    compact('c')
                )->render()
            ])
        ];
    }

    /**
     * Create a new category or restore if soft-deleted
     */
    public function createOrRestore(array $data): ProgramRequirementCategory
    {
        try {
            return DB::transaction(function () use ($data) {
                $existing = ProgramRequirementCategory::withTrashed()
                    ->where('name', $data['name'])
                    ->first();

                if ($existing) {
                    $existing->restore();
                    $existing->update($data);
                    return $existing;
                }

                return ProgramRequirementCategory::create($data);
            });
        } catch (Exception $e) {
            report($e);
            throw new DomainException('Failed to create or restore category: ' . $e->getMessage());
        }
    }

    /**
     * Update a category or restore if soft-deleted with the same name
     */
    public function updateOrRestore(ProgramRequirementCategory $category, array $data): ProgramRequirementCategory
    {
        try {
            return DB::transaction(function () use ($category, $data) {
                $conflict = ProgramRequirementCategory::withTrashed()
                    ->where('name', $data['name'])
                    ->where('id', '!=', $category->id)
                    ->first();

                if ($conflict) {
                    // Restore the soft-deleted conflicting record instead of updating this one
                    if ($conflict->trashed()) {
                        $conflict->restore();
                        $conflict->update($data);
                        return $conflict;
                    }

                    throw new DomainException('Category already exists (including archived).');
                }

                $category->update($data);
                return $category;
            });
        } catch (Exception $e) {
            report($e);
            throw new DomainException('Failed to update or restore category: ' . $e->getMessage());
        }
    }

    /**
     * Soft delete a category
     */
    // public function delete(ProgramRequirementCategory $category): void
    // {
    //     try {
    //         DB::transaction(function () use ($category) {

    //             // Check relations
    //             if ($category->programRequirements()->exists()) {
    //                 throw new DomainException(
    //                     "Cannot delete '{$category->name}'. It is used in program requirements."
    //                 );
    //             }

    //             if ($category->programCourses()->exists()) {
    //                 throw new DomainException(
    //                     "Cannot delete '{$category->name}'. It is used in program courses."
    //                 );
    //             }

    //             $category->delete();
    //         });
    //     } catch (DomainException $e) {
    //         throw $e;
    //     } catch (Exception $e) {
    //         report($e);
    //         throw new DomainException('Failed to delete category: ' . $e->getMessage());
    //     }
    // }

    public function delete(ProgramRequirementCategory $category): void
    {
        try {
            DB::transaction(function () use ($category) {

                // Count usages
                $reqCount = $category->programRequirements()->count();
                $courseCount = $category->programCourses()->count();

                // Block delete if used
                if ($reqCount || $courseCount) {
                    throw new DomainException(
                        "Cannot delete '{$category->name}'. Used in {$reqCount} requirements and {$courseCount} program courses."
                    );
                }

                // Safe delete
                $category->delete();
            });
        } catch (DomainException $e) {
            throw $e;
        } catch (Exception $e) {
            report($e);
            throw new DomainException('Failed to delete category: ' . $e->getMessage());
        }
    }


}
