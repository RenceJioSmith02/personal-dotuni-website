<?php

namespace App\Services\Form;

use App\Models\FormCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use DomainException;

class FormCategoryService
{
    public function list()
    {
        return FormCategory::orderBy('sort_order')->get();
    }

    public function create(array $data): FormCategory
    {
        return DB::transaction(function () use ($data) {
            $existing = FormCategory::withTrashed()
                ->where('name', $data['name'])
                ->first();

            if ($existing) {
                if (!$existing->trashed()) {
                    throw new DomainException('Category already exists.');
                }

                $existing->restore();
                $existing->update([
                    'name' => $data['name'],
                    'slug' => Str::slug($data['name']),
                    'sort_order' => $data['sort_order'] ?? 0,
                ]);

                return $existing;
            }

            return FormCategory::create([
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'sort_order' => $data['sort_order'] ?? 0,
            ]);
        });
    }

    public function update(FormCategory $category, array $data): FormCategory
    {
        return DB::transaction(function () use ($category, $data) {
            $category->update([
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'sort_order' => $data['sort_order'] ?? 0,
            ]);

            return $category;
        });
    }

    public function delete(FormCategory $category): void
    {
        if ($category->forms()->exists()) {
            throw new DomainException('Category has forms and cannot be deleted.');
        }

        $category->delete();
    }
}
