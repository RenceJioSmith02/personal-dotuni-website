<?php

namespace App\Services\Clsu;

use DomainException;
use App\Models\Asset;
use App\Models\ClsuNews;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ClsuNewsService
{
    public function list()
    {
        return ClsuNews::with('thumbnail')->orderBy('sort_order');
    }


    public function datatable(Request $request)
    {
        $query = $this->list(); // query builder

        $total = $query->count();

        // Search
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('url', 'like', "%{$search}%");
            });
        }

        $filtered = $query->count();

        // Ordering
        $columns = ['title', 'description', 'url', 'sort_order', 'status', 'created_at', 'updated_at', 'actions'];
        $orderColumnIndex = $request->input('order.0.column', 1);
        $orderColumn = $columns[$orderColumnIndex] ?? 'sort_order';
        $orderDir = $request->input('order.0.dir', 'asc');

        // Only order by DB columns
        if (!in_array($orderColumn, ['status', 'url'])) {
            $query->orderBy($orderColumn, $orderDir);
        }

        // Pagination: call skip() and take() **before get()**
        $data = $query
            ->skip($request->start)
            ->take($request->length)
            ->get();

        // Format JSON for DataTables
        return [
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data->map(function ($item) {
                return [
                    'title' => $item->title,
                    'description' => \Str::limit($item->description, 80),
                    'url' => $item->url ? '<a href="' . $item->url . '" target="_blank">View</a>' : '<span class="text-muted">—</span>',
                    'sort_order' => $item->sort_order,
                    'status' => $item->is_active
                        ? '<span class="badge badge-success">Active</span>'
                        : '<span class="badge badge-danger">Inactive</span>',
                    'created_at' => $item->created_at->toDateTimeString(),
                    'updated_at' => $item->updated_at->toDateTimeString(),
                    'actions' => view('admin.clsu.news.partials.actions', compact('item'))->render()
                ];
            })
        ];
    }
    

    public function create(array $data, ?UploadedFile $image): ClsuNews
    {
        return DB::transaction(function () use ($data, $image) {
            try {
                $assetId = $image ? $this->storeImage($image) : null;

                return ClsuNews::create([
                    'title' => $data['title'],
                    'url' => $data['url'] ?? null,
                    'description' => $data['description'] ?? null,
                    'sort_order' => $data['sort_order'] ?? 0,
                    'is_active' => $data['is_active'],
                    'thumbnail_asset_id' => $assetId,
                    'updated_by' => Auth::id(),
                ]);
            } catch (\Throwable $e) {
                if (isset($assetId)) {
                    $this->deleteAssetById($assetId);
                }
                throw $e;
            }
        });
    }

    public function update(ClsuNews $news, array $data, ?UploadedFile $image): ClsuNews
    {
        return DB::transaction(function () use ($news, $data, $image) {
            try {
                if ($image) {
                    $this->replaceImage($news, $image);
                }

                $news->update([
                    'title' => $data['title'],
                    'url' => $data['url'] ?? null,
                    'description' => $data['description'] ?? null,
                    'sort_order' => $data['sort_order'] ?? 0,
                    'is_active' => $data['is_active'],
                    'updated_by' => Auth::id(),
                ]);

                return $news;
            } catch (\Throwable $e) {
                throw $e;
            }
        });
    }

    public function delete(ClsuNews $news): void
    {
        DB::transaction(function () use ($news) {
            try {
                if ($news->thumbnail) {
                    Storage::disk('public')->delete($news->thumbnail->storage_path);
                    $news->thumbnail->delete();
                }

                $news->delete();
            } catch (\Throwable $e) {
                throw $e;
            }
        });
    }

    protected function storeImage(UploadedFile $file): int
    {

        $extension = $file->getClientOriginalExtension();

        $filename = sprintf(
            'clsu_news-%s-%s.%s',
            now()->format('Y-m-d'),
            substr(bin2hex(random_bytes(4)), 0, 8),
            $extension
        );

        $path = $file->storeAs('clsu-news', $filename, 'public');

        return Asset::create([
            'kind' => 'image',
            'file_name' => $filename,
            'storage_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size_kb' => round($file->getSize() / 1024),
            'uploaded_by' => Auth::id(),
        ])->id;
    }

    protected function replaceImage(ClsuNews $news, UploadedFile $file): void
    {
        if ($news->thumbnail) {
            Storage::disk('public')->delete($news->thumbnail->storage_path);
            $news->thumbnail->delete();
        }

        $news->thumbnail_asset_id = $this->storeImage($file);
        $news->save();
    }

    protected function deleteAssetById(int $id): void
    {
        $asset = Asset::find($id);
        if ($asset) {
            Storage::disk('public')->delete($asset->storage_path);
            $asset->delete();
        }
    }
}
