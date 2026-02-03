<?php

namespace App\Services\Form;

use App\Models\FormCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use DomainException;
use Illuminate\Http\Request;

class FormCategoryService
{
    public function list()
    {
        return FormCategory::orderBy('sort_order')->get();
    }



    public function datatable(Request $request)
    {
        $query = FormCategory::query();

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
        $columns = ['name', 'sort_order', 'status', 'actions'];
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderColumn = $columns[$orderColumnIndex] ?? 'sort_order';
        $orderDir = $request->input('order.0.dir', 'asc');

        $orderDir = $orderDir === 'asc' ? 'asc' : 'desc';

        if (!in_array($orderColumn, ['actions', 'status'])) {
            $query->orderBy($orderColumn, $orderDir);
        }

        /* ======================
         * PAGINATION
         * ====================== */
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        $data = $query->offset($start)->limit($length)->get();

        /* ======================
         * RESPONSE
         * ====================== */
        return [
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data->map(function ($category) {
                return [
                    'name' => $category->name,
                    'sort_order' => $category->sort_order,
                    'status' => $category->is_active
                        ? '<span class="badge badge-success">Active</span>'
                        : '<span class="badge badge-danger">Inactive</span>',
                    'actions' => view(
                        'admin.form.categories.partials.actions',
                        compact('category')
                    )->render(),
                ];
            }),
        ];
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
