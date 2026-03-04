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
        return FormCategory::whereNull('deleted_at') // ✅ Exclude archived
            ->orderBy('sort_order')
            ->get();
    }

    public function datatable(Request $request)
    {
        // ✅ Show ALL records including archived
        $query = FormCategory::query();

        $total = $query->count();

        if ($search = $request->input('search.value')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $filtered = $query->count();

        $columns = ['name', 'sort_order', 'status', 'created_at', 'updated_at', 'actions'];
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderColumn = $columns[$orderColumnIndex] ?? 'sort_order';
        $orderDir = $request->input('order.0.dir', 'asc') === 'asc' ? 'asc' : 'desc';
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        if (!in_array($orderColumn, ['actions', 'status'])) {
            $query->orderBy($orderColumn, $orderDir);
        }

        $data = $query->offset($start)->limit($length)->get();

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
                    'created_at' => $category->created_at->toDateTimeString(),
                    'updated_at' => $category->updated_at->toDateTimeString(),
                    'archived' => !is_null($category->deleted_at), // ✅ Pass archive state
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
            // ✅ Restore if same name was archived
            $existing = FormCategory::where('name', $data['name'])->first();

            if ($existing && !is_null($existing->deleted_at)) {
                DB::table('form_categories')->where('id', $existing->id)->update([
                    'name' => $data['name'],
                    'slug' => Str::slug($data['name']),
                    'sort_order' => $data['sort_order'] ?? 0,
                    'is_active' => true,
                    'deleted_at' => null,
                ]);
                return $existing->fresh();
            }

            if ($existing) {
                throw new DomainException('Category already exists.');
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

    /**
     * ✅ Hard delete — must be inactive first
     */
    public function delete(FormCategory $category): void
    {
        try {
            DB::transaction(function () use ($category) {

                // ✅ Guard: must be inactive before hard deleting
                if ($category->is_active) {
                    throw new DomainException(
                        "Cannot delete '{$category->name}'. Please deactivate it before deleting."
                    );
                }

                if ($category->forms()->exists()) {
                    throw new DomainException(
                        "Cannot delete '{$category->name}'. It has forms assigned to it."
                    );
                }

                $category->delete();
            });
        } catch (DomainException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);
            throw new DomainException('Failed to delete category.');
        }
    }

    /**
     * ✅ Archive — sets is_active = false + deleted_at = now()
     */
    public function archive(FormCategory $category): FormCategory
    {
        try {
            DB::table('form_categories')->where('id', $category->id)->update([
                'is_active' => false,
                'deleted_at' => now(),
            ]);

            return $category->fresh();
        } catch (\Throwable $e) {
            report($e);
            throw new DomainException('Failed to archive category.');
        }
    }

    /**
     * ✅ Unarchive — sets is_active = true + deleted_at = null
     */
    public function unarchive(FormCategory $category): FormCategory
    {
        try {
            DB::table('form_categories')->where('id', $category->id)->update([
                'is_active' => true,
                'deleted_at' => null,
            ]);

            return $category->fresh();
        } catch (\Throwable $e) {
            report($e);
            throw new DomainException('Failed to unarchive category.');
        }
    }
}