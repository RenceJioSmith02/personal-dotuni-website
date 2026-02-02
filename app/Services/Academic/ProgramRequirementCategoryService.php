<?php

namespace App\Services\Academic;

use App\Models\ProgramRequirementCategory;
use Illuminate\Support\Facades\DB;
use DomainException;
use Exception;

class ProgramRequirementCategoryService
{
    /**
     * List all program requirement categories ordered by sort_order
     */
    public function list()
    {
        return ProgramRequirementCategory::orderBy('sort_order')->get();
    }

    /**
     * Create or restore a program requirement category
     */
    public function create(array $data): ProgramRequirementCategory
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
            throw new DomainException('Failed to create category: ' . $e->getMessage());
        }
    }

    /**
     * Update a program requirement category
     */
    public function update(ProgramRequirementCategory $category, array $data): ProgramRequirementCategory
    {
        try {
            return DB::transaction(function () use ($category, $data) {
                $conflict = ProgramRequirementCategory::withTrashed()
                    ->where('name', $data['name'])
                    ->where('id', '!=', $category->id)
                    ->first();

                if ($conflict) {
                    throw new DomainException('Category already exists (including archived).');
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
     * Delete a program requirement category
     */
    public function delete(ProgramRequirementCategory $category): void
    {
        try {
            DB::transaction(function () use ($category) {
                $category->delete();
            });
        } catch (Exception $e) {
            report($e);
            throw new DomainException('Failed to delete category: ' . $e->getMessage());
        }
    }
}
