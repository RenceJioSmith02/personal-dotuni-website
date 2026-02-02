<?php

namespace App\Services\Linkage;

use App\Models\LinkageCategory;
use Illuminate\Support\Facades\DB;
use DomainException;

class LinkageCategoryService
{
    public function list()
    {
        return LinkageCategory::orderBy('sort_order')->get();
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
