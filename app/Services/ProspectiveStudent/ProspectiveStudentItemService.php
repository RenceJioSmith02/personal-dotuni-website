<?php

namespace App\Services\ProspectiveStudent;

use App\Models\ProspectiveStudentItem;
use DomainException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProspectiveStudentItemService
{
    public function list()
    {
        return ProspectiveStudentItem::with('category')
            ->whereNull('deleted_at') // ✅ Exclude archived
            ->orderBy('sort_order')
            ->get();
    }

    public function datatable(Request $request)
    {
        // ✅ Show ALL records including archived
        $query = ProspectiveStudentItem::with('category');

        $total = $query->count();

        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('category', fn($c) => $c->where('name', 'like', "%{$search}%"))
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $filtered = $query->count();

        $columns = ['category', 'content', 'sort_order', 'status', 'created_at', 'updated_at', 'actions'];
        $orderCol = $columns[$request->input('order.0.column', 0)] ?? 'sort_order';
        $orderDir = $request->input('order.0.dir', 'asc') === 'asc' ? 'asc' : 'desc';
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        if ($orderCol === 'category') {
            $query->join(
                'prospective_student_categories',
                'prospective_student_categories.id',
                '=',
                'prospective_student_items.category_id'
            )
                ->orderBy('prospective_student_categories.name', $orderDir)
                ->select('prospective_student_items.*');
        } elseif (!in_array($orderCol, ['status', 'actions'])) {
            $query->orderBy($orderCol, $orderDir);
        }

        $items = $query->skip($start)->take($length)->get();

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $items->map(function ($item) {
                return [
                    'category' => e($item->category->name ?? '—'),
                    'content' => e(Str::limit(strip_tags($item->content), 80)),
                    'sort_order' => $item->sort_order,
                    'status' => $item->is_active
                        ? '<span class="badge badge-success">Active</span>'
                        : '<span class="badge badge-danger">Inactive</span>',
                    'created_at' => $item->created_at->toDateTimeString(),
                    'updated_at' => $item->updated_at->toDateTimeString(),
                    'archived' => !is_null($item->deleted_at), // ✅ Pass archive state
                    'actions' => view(
                        'admin.prospective_student.items.partials.actions',
                        compact('item')
                    )->render(),
                ];
            })
        ]);
    }

    public function create(array $data): ProspectiveStudentItem
    {
        return DB::transaction(function () use ($data) {
            try {
                return ProspectiveStudentItem::create([
                    'category_id' => $data['category_id'],
                    'content' => $data['content'],
                    'sort_order' => $data['sort_order'] ?? 0,
                    'is_active' => $data['is_active'],
                    'updated_by' => Auth::id(),
                ]);
            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    public function update(ProspectiveStudentItem $item, array $data): void
    {
        DB::transaction(function () use ($item, $data) {
            try {
                $item->update([
                    'category_id' => $data['category_id'],
                    'content' => $data['content'],
                    'sort_order' => $data['sort_order'] ?? 0,
                    'is_active' => $data['is_active'],
                    'updated_by' => Auth::id(),
                ]);
            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    /**
     * ✅ Hard delete — must be inactive first
     */
    public function delete(ProspectiveStudentItem $item): void
    {
        try {
            DB::transaction(function () use ($item) {

                // ✅ Guard: must be inactive before hard deleting
                if ($item->is_active) {
                    throw new DomainException(
                        "Cannot delete this item. Please deactivate it before deleting."
                    );
                }

                $item->delete();
            });
        } catch (DomainException $e) {
            throw $e;
        } catch (Throwable $e) {
            report($e);
            throw new DomainException('Failed to delete item.');
        }
    }

    /**
     * ✅ Archive — sets is_active = false + deleted_at = now()
     */
    public function archive(ProspectiveStudentItem $item): ProspectiveStudentItem
    {
        try {
            DB::table('prospective_student_items')->where('id', $item->id)->update([
                'is_active' => false,
                'deleted_at' => now(),
            ]);

            return $item->fresh();
        } catch (Throwable $e) {
            report($e);
            throw new DomainException('Failed to archive item.');
        }
    }

    /**
     * ✅ Unarchive — sets is_active = true + deleted_at = null
     */
    public function unarchive(ProspectiveStudentItem $item): ProspectiveStudentItem
    {
        try {
            DB::table('prospective_student_items')->where('id', $item->id)->update([
                'is_active' => true,
                'deleted_at' => null,
            ]);

            return $item->fresh();
        } catch (Throwable $e) {
            report($e);
            throw new DomainException('Failed to unarchive item.');
        }
    }
}
