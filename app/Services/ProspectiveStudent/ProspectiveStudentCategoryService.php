<?php

namespace App\Services\ProspectiveStudent;

use App\Models\ProspectiveStudentCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProspectiveStudentCategoryService
{
    public function list()
    {
        return ProspectiveStudentCategory::orderBy('sort_order')->get();
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
