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

    // Pagination
    public function listPaginated($page = 1, $perPage = 20)
    {
        $query = Gallery::with('asset')->orderBy('sort_order');

        $total = $query->count();

        $items = $query->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return [
            'data' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
        ];
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
        $columns = ['image', 'file_name', 'sort_order', 'created_at', 'updated_at', 'actions'];
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
                    'image' => $item->thumbnail_path
                        ? '<a href="' . asset('storage/' . $item->asset->storage_path) . '" target="_blank">
                                <img src="' . asset('storage/' . $item->thumbnail_path) . '"
                                        class="img-thumbnail"
                                        style="max-width:50px; height:auto;"
                                        loading="lazy"
                                >
                            </a>'
                        : '<span class="text-muted">No Image</span>',
                    'file_name' => $item->asset->file_name ?? '—',
                    'sort_order' => $item->sort_order,
                    'created_at' => $item->created_at->toDateTimeString(),
                    'updated_at' => $item->updated_at->toDateTimeString(),
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
                $thumbnailPath = $this->createThumbnail($request->file('image'));

                return Gallery::create([
                    'asset_id' => $asset->id,
                    'thumbnail_path' => $thumbnailPath,
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

                    // 1️⃣ Delete old thumbnail
                    if ($gallery->thumbnail_path) {
                        Storage::disk('public')->delete($gallery->thumbnail_path);
                    }

                    // 2️⃣ Replace original asset
                    $this->replaceAsset($gallery, $request->file('image'));

                    // 3️⃣ Create new thumbnail
                    $gallery->thumbnail_path = $this->createThumbnail(
                        $request->file('image')
                    );
                }

                $gallery->update([
                    'sort_order' => $validated['sort_order'] ?? $gallery->sort_order,
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
        $extension = $file->getClientOriginalExtension();

        $filename = sprintf(
            'gallery-%s-%s.%s',
            now()->format('Y-m-d'),
            substr(bin2hex(random_bytes(4)), 0, 8),
            $extension
        );

        $path = $file->storeAs('gallery', $filename, 'public');

        return Asset::create([
            'kind' => 'image',
            'file_name' => $filename,
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

        $gallery->update([
            'asset_id' => $asset->id,
        ]);

        return $asset;
    }


    private function createThumbnail($file): string
    {
        $image = imagecreatefromstring(file_get_contents($file));
        $width = imagesx($image);
        $height = imagesy($image);

        $thumbWidth = 150;
        $thumbHeight = intval(($height / $width) * $thumbWidth);

        $thumbnail = imagecreatetruecolor($thumbWidth, $thumbHeight);
        imagecopyresampled(
            $thumbnail,
            $image,
            0,
            0,
            0,
            0,
            $thumbWidth,
            $thumbHeight,
            $width,
            $height
        );

        $filename = 'thumb-' . uniqid() . '.jpg';
        $path = storage_path('app/public/gallery/thumbnails/' . $filename);

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        imagejpeg($thumbnail, $path, 80);

        imagedestroy($image);
        imagedestroy($thumbnail);

        return 'gallery/thumbnails/' . $filename;
    }

}
