<?php

namespace App\Services;

use App\Models\Gallery;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Throwable;

class GalleryService
{
    /**
     * List all gallery items
     */
    public function list()
    {
        return Gallery::with('asset')->orderBy('sort_order')->get();
    }


    public function datatable(Request $request)
    {
        $query = Gallery::with('asset');

        $total = $query->count();

        /* ======================
         * SEARCH
         * ====================== */
        if ($search = $request->input('search.value')) {
            $query->whereHas('asset', function ($q) use ($search) {
                $q->where('file_name', 'like', "%{$search}%");
            });
        }

        $filtered = $query->count();

        /* ======================
         * ORDERING
         * ====================== */
        $columns = ['image', 'file_name', 'sort_order', 'actions'];
        $orderIndex = $request->input('order.0.column', 2);
        $orderDir = $request->input('order.0.dir', 'asc');

        if (($columns[$orderIndex] ?? null) === 'sort_order') {
            $query->orderBy('sort_order', $orderDir);
        }

        /* ======================
         * PAGINATION
         * ====================== */
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        $items = $query->offset($start)->limit($length)->get();

        /* ======================
         * RESPONSE
         * ====================== */
        return [
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $items->map(function ($item) {

                return [
                    'image' => $item->asset
                        ? '<img src="' . asset('storage/' . $item->asset->storage_path) . '"
                             class="img-thumbnail"
                             style="max-width:50px;"
                             alt="' . e($item->asset->alt_text ?? 'Gallery image') . '">'
                        : '<span class="text-muted">No Image</span>',

                    'file_name' => $item->asset->file_name ?? '—',

                    'sort_order' => $item->sort_order,

                    'actions' => view(
                        'admin.gallery.partials.actions',
                        compact('item')
                    )->render(),
                ];
            }),
        ];
    }

    /**
     * Create a new gallery item
     */
    public function create(Request $request): Gallery
    {
        return DB::transaction(function () use ($request) {
            try {
                $validated = $request->validate([
                    'sort_order' => 'nullable|integer',
                    'image' => 'required|image|max:2048',
                ]);

                // Store asset
                $asset = $this->storeImageAsset($request->file('image'));

                return Gallery::create([
                    'asset_id' => $asset->id,
                    'sort_order' => $validated['sort_order'] ?? 0,
                    'updated_by' => Auth::id(),
                ]);
            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    /**
     * Update an existing gallery item
     */
    public function update(Request $request, Gallery $gallery): Gallery
    {
        return DB::transaction(function () use ($request, $gallery) {
            try {
                $validated = $request->validate([
                    'sort_order' => 'nullable|integer',
                    'image' => 'nullable|image|max:2048',
                ]);

                if ($request->hasFile('image')) {
                    $this->replaceAsset($gallery, $request->file('image'));
                }

                $gallery->update([
                    'sort_order' => $validated['sort_order'] ?? 0,
                    'updated_by' => Auth::id(),
                ]);

                return $gallery;
            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    /**
     * Delete a gallery item
     */
    public function delete(Gallery $gallery): void
    {
        DB::transaction(function () use ($gallery) {
            try {
                if ($gallery->asset) {
                    Storage::disk('public')->delete($gallery->asset->storage_path);
                    $gallery->asset->delete();
                }

                $gallery->delete();
            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    /**
     * Store an image as Asset
     */
    private function storeImageAsset($file): Asset
    {
        $path = $file->store('gallery', 'public');

        return Asset::create([
            'kind' => 'image',
            'file_name' => $file->getClientOriginalName(),
            'storage_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size_kb' => round($file->getSize() / 1024),
            'alt_text' => null,
            'uploaded_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);
    }

    /**
     * Replace asset for update
     */
    private function replaceAsset(Gallery $gallery, $file): Asset
    {
        if ($gallery->asset) {
            Storage::disk('public')->delete($gallery->asset->storage_path);
            $gallery->asset->delete();
        }

        $asset = $this->storeImageAsset($file);
        $gallery->asset_id = $asset->id;
        return $asset;
    }
}
