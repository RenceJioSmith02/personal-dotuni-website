<?php

namespace App\Services\Academic;

use App\Models\ProgramRequirementCategory;
use Illuminate\Support\Facades\DB;
use DomainException;
use Exception;
use Illuminate\Http\Request;

class ProgramRequirementCategoryService
{
    public function list()
    {
        return ProgramRequirementCategory::whereNull('deleted_at')
            ->orderBy('sort_order')
            ->get();
    }

    public function datatable(Request $request)
    {
        // ✅ Show ALL records including archived
        $query = ProgramRequirementCategory::query();

        $total = $query->count();

        if ($search = $request->input('search.value')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $filtered = $query->count();

        $columns = ['name', 'sort_order', 'created_at', 'updated_at'];
        $orderColumn = $columns[$request->input('order.0.column', 0)] ?? 'sort_order';
        $orderDir = $request->input('order.0.dir', 'asc');
        $query->orderBy($orderColumn, $orderDir);

        $data = $query
            ->skip($request->start)
            ->take($request->length)
            ->get();

        return [
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data->map(fn($c) => [
                'name' => $c->name,
                'sort_order' => $c->sort_order,
                'created_at' => $c->created_at->toDateTimeString(),
                'updated_at' => $c->updated_at->toDateTimeString(),
                'archived' => !is_null($c->deleted_at), // ✅ Pass archive state
                'actions' => view(
                    'admin.academic.program_requirement_categories.partials.actions',
                    compact('c')
                )->render()
            ])
        ];
    }

    public function create(array $data): ProgramRequirementCategory
    {
        try {
            return DB::transaction(function () use ($data) {
                // ✅ Restore if same name was previously archived
                $existing = ProgramRequirementCategory::where('name', $data['name'])->first();

                if ($existing && !is_null($existing->deleted_at)) {
                    $existing->update(array_merge($data, ['deleted_at' => null]));
                    return $existing;
                }

                return ProgramRequirementCategory::create($data);
            });
        } catch (Exception $e) {
            report($e);
            throw new DomainException('Failed to create category: ' . $e->getMessage());
        }
    }

    public function update(ProgramRequirementCategory $category, array $data): ProgramRequirementCategory
    {
        try {
            return DB::transaction(function () use ($category, $data) {
                $conflict = ProgramRequirementCategory::where('name', $data['name'])
                    ->where('id', '!=', $category->id)
                    ->first();

                if ($conflict) {
                    throw new DomainException('A category with this name already exists.');
                }

                $category->update($data);
                return $category;
            });
        } catch (Exception $e) {
            report($e);
            throw new DomainException('Failed to update category: ' . $e->getMessage());
        }
    }

    /**
     * ✅ Hard delete — must be archived first
     */
    public function delete(ProgramRequirementCategory $category): void
    {
        try {
            DB::transaction(function () use ($category) {

                // ✅ Guard: must be archived before hard deleting
                if (is_null($category->deleted_at)) {
                    throw new DomainException(
                        "Cannot delete '{$category->name}'. Please archive it before deleting."
                    );
                }

                $reqCount = $category->programRequirements()->count();
                $courseCount = $category->programCourses()->count();

                if ($reqCount || $courseCount) {
                    throw new DomainException(
                        "Cannot delete '{$category->name}'. Used in {$reqCount} requirements and {$courseCount} program courses."
                    );
                }

                $category->delete();
            });
        } catch (DomainException $e) {
            throw $e;
        } catch (Exception $e) {
            report($e);
            throw new DomainException('Failed to delete category: ' . $e->getMessage());
        }
    }

    /**
     * ✅ Archive — sets deleted_at = now()
     */
    public function archive(ProgramRequirementCategory $category): ProgramRequirementCategory
    {
        try {
            return DB::transaction(function () use ($category) {
                $category->update(['deleted_at' => now()]);
                return $category;
            });
        } catch (Exception $e) {
            report($e);
            throw new DomainException('Failed to archive category: ' . $e->getMessage());
        }
    }

    /**
     * ✅ Unarchive — sets deleted_at = null
     */
    public function unarchive(ProgramRequirementCategory $category): ProgramRequirementCategory
    {
        try {
            return DB::transaction(function () use ($category) {
                $category->update(['deleted_at' => null]);
                return $category;
            });
        } catch (Exception $e) {
            report($e);
            throw new DomainException('Failed to unarchive category: ' . $e->getMessage());
        }
    }
}