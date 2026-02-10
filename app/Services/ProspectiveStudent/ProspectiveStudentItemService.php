<?php

namespace App\Services\ProspectiveStudent;

use App\Models\ProspectiveStudentItem;
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
            ->orderBy('sort_order')
            ->get();
    }



    public function datatable(Request $request)
    {
        $query = ProspectiveStudentItem::with('category');

        $total = $query->count();

        /* SEARCH */
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('category', function ($c) use ($search) {
                    $c->where('name', 'like', "%{$search}%");
                })
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $filtered = $query->count();

        /* ORDER */
        $columns = ['category', 'content', 'sort_order', 'status', 'created_at', 'updated_at'];
        $orderCol = $columns[$request->input('order.0.column')] ?? 'sort_order';
        $orderDir = $request->input('order.0.dir', 'asc');

        if ($orderCol === 'category') {
            $query->join(
                'prospective_student_categories',
                'prospective_student_categories.id',
                '=',
                'prospective_student_items.category_id'
            )->orderBy('prospective_student_categories.name', $orderDir)
                ->select('prospective_student_items.*');
        } else {
            $query->orderBy($orderCol, $orderDir);
        }

        /* PAGINATION */
        $items = $query
            ->skip($request->start)
            ->take($request->length)
            ->get();

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $items->map(function ($item) {
                return [
                    'category' => e($item->category->name ?? '-'),
                    'content' => e(
                        Str::limit(strip_tags($item->content), 80)
                    ),
                    'sort_order' => $item->sort_order,
                    'status' => $item->is_active
                        ? '<span class="badge badge-success">Active</span>'
                        : '<span class="badge badge-danger">Inactive</span>',
                    'created_at' => $item->created_at->toDateTimeString(),
                    'updated_at' => $item->updated_at->toDateTimeString(),
                    'actions' => view(
                        'admin.prospective_student.items.partials.actions',
                        compact('item')
                    )->render()
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

    public function update(
        ProspectiveStudentItem $item,
        array $data
    ): void {
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

    public function delete(ProspectiveStudentItem $item): void
    {
        DB::transaction(function () use ($item) {
            try {
                $item->delete();

            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }
}
