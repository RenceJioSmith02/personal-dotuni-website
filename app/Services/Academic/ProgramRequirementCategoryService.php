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
        return ProgramRequirementCategory::whereNull('deleted_at') // ✅ Exclude archived
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

        $columns = ['name', 'sort_order', 'status', 'created_at', 'updated_at', 'actions'];
        $orderColumn = $columns[$request->input('order.0.column', 0)] ?? 'sort_order';
        $orderDir = $request->input('order.0.dir', 'asc') === 'asc' ? 'asc' : 'desc';
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        if (!in_array($orderColumn, ['status', 'actions'])) {
            $query->orderBy($orderColumn, $orderDir);
        }

        $data = $query->skip($start)->take($length)->get();

        return [
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data->map(fn($c) => [
                'name' => $c->name,
                'sort_order' => $c->sort_order,
                'status' => $c->is_active
                    ? '<span class="badge badge-success">Active</span>'
                    : '<span class="badge badge-danger">Inactive</span>',
                'created_at' => $c->created_at->toDateTimeString(),
                'updated_at' => $c->updated_at->toDateTimeString(),
                'archived' => !is_null($c->deleted_at),
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
                // ✅ Restore if same name was archived
                $existing = ProgramRequirementCategory::where('name', $data['name'])->first();

                if ($existing && !is_null($existing->deleted_at)) {
                    DB::table('program_requirement_categories')->where('id', $existing->id)->update([
                        'name' => $data['name'],
                        'sort_order' => $data['sort_order'] ?? 0,
                        'is_active' => true,
                        'deleted_at' => null,
                    ]);
                    return $existing->fresh();
                }

                if ($existing) {
                    throw new DomainException('A category with this name already exists.');
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
                    ->whereNull('deleted_at')
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
     * ✅ Hard delete — must be inactive first
     */
    public function delete(ProgramRequirementCategory $category): void
    {
        try {
            DB::transaction(function () use ($category) {

                // ✅ Guard: must be inactive before hard deleting
                if ($category->is_active) {
                    throw new DomainException(
                        "Cannot delete '{$category->name}'. Please deactivate it before deleting."
                    );
                }

                $reqCount = $category->programRequirements()->count();
                $courseCount = $category->programCourses()->count();

                if ($reqCount || $courseCount) {
                    throw new DomainException(
                        "Cannot delete '{$category->name}'. Used in {$reqCount} requirement(s) and {$courseCount} program course(s)."
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
     * ✅ Archive — sets is_active = false + deleted_at = now()
     */
    public function archive(ProgramRequirementCategory $category): ProgramRequirementCategory
    {
        try {
            DB::table('program_requirement_categories')->where('id', $category->id)->update([
                'is_active' => false,
                'deleted_at' => now(),
            ]);

            return $category->fresh();
        } catch (Exception $e) {
            report($e);
            throw new DomainException('Failed to archive category: ' . $e->getMessage());
        }
    }

    /**
     * ✅ Unarchive — sets is_active = true + deleted_at = null
     */
    public function unarchive(ProgramRequirementCategory $category): ProgramRequirementCategory
    {
        try {
            DB::table('program_requirement_categories')->where('id', $category->id)->update([
                'is_active' => true,
                'deleted_at' => null,
            ]);

            return $category->fresh();
        } catch (Exception $e) {
            report($e);
            throw new DomainException('Failed to unarchive category: ' . $e->getMessage());
        }
    }
}