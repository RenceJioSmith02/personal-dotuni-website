<?php

namespace App\Services\ProspectiveStudent;

use App\Models\ProspectiveStudentItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProspectiveStudentItemService
{
    public function list()
    {
        return ProspectiveStudentItem::with('category')
            ->orderBy('sort_order')
            ->get();
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
