<?php

namespace App\Services;

use App\Models\Fee;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FeeService
{
    public function list()
    {
        return Fee::with('asset')->orderBy('sort_order')->get();
    }

    public function datatable(Request $request)
    {
        $query = Fee::with('asset');

        $total = $query->count();

        /* ===============================
           SEARCH
        =============================== */
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('caption', 'like', "%{$search}%")
                    ->orWhere('sort_order', 'like', "%{$search}%");
            });
        }

        $filtered = $query->count();

        /* ===============================
           ORDERING
        =============================== */
        $columns = [
            'title',
            'caption',
            'sort_order',
            'actions'
        ];

        $orderColumnIndex = $request->input('order.0.column', 3);
        $orderColumn = $columns[$orderColumnIndex] ?? 'sort_order';
        $orderDir = $request->input('order.0.dir', 'asc');

        $orderDir = $orderDir === 'asc' ? 'asc' : 'desc';

        if (!in_array($orderColumn, ['asset', 'actions'])) {
            $query->orderBy($orderColumn, $orderDir);
        }

        /* ===============================
           PAGINATION
        =============================== */
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        $data = $query
            ->offset($start)
            ->limit($length)
            ->get();

        /* ===============================
           RESPONSE
        =============================== */
        return [
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data->map(function ($item) {

                return [
                    'title' => e($item->title),

                    'caption' => Str::limit($item->caption, 80),

                    'sort_order' => $item->sort_order,

                    'actions' => view(
                        'admin.fees.partials.actions',
                        compact('item')
                    )->render(),
                ];
            }),
        ];
    }


    public function create(array $data, ?UploadedFile $image): Fee
    {
        return DB::transaction(function () use ($data, $image) {
            $assetId = $image ? $this->storeImage($image) : null;

            return Fee::create([
                'title' => $data['title'],
                'caption' => $data['caption'] ?? null,
                'sort_order' => $data['sort_order'] ?? 0,
                'asset_id' => $assetId,
                'updated_by' => Auth::id(),
            ]);
        });
    }

    public function update(Fee $fee, array $data, ?UploadedFile $image): Fee
    {
        return DB::transaction(function () use ($fee, $data, $image) {

            if ($image) {
                $this->replaceImage($fee, $image);
            }

            $fee->update([
                'title' => $data['title'],
                'caption' => $data['caption'] ?? null,
                'sort_order' => $data['sort_order'] ?? 0,
                'updated_by' => Auth::id(),
            ]);

            return $fee;
        });
    }

    public function delete(Fee $fee): void
    {
        DB::transaction(function () use ($fee) {
            if ($fee->asset) {
                Storage::disk('public')->delete($fee->asset->storage_path);
                $fee->asset->delete();
            }
            $fee->delete();
        });
    }

    protected function storeImage(UploadedFile $file): int
    {
        $extension = $file->getClientOriginalExtension();

        $filename = sprintf(
            'fee-%s-%s.%s',
            now()->format('Y-m-d'),
            substr(bin2hex(random_bytes(4)), 0, 8),
            $extension
        );

        $path = $file->storeAs('fees', $filename, 'public');

        return Asset::create([
            'kind' => 'image',
            'file_name' => $filename,
            'storage_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size_kb' => round($file->getSize() / 1024),
            'uploaded_by' => Auth::id(),
        ])->id;
    }

    protected function replaceImage(Fee $fee, UploadedFile $file): void
    {
        if ($fee->asset) {
            Storage::disk('public')->delete($fee->asset->storage_path);
            $fee->asset->delete();
        }

        $fee->asset_id = $this->storeImage($file);
        $fee->save();
    }
}
