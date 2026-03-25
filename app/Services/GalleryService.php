<?php

namespace App\Services;

use App\Models\Gallery;
use App\Models\Asset;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Throwable;

class GalleryService
{
    public function list()
    {
        return Gallery::with('asset')
            ->whereNull('deleted_at') 
            ->orderBy('sort_order')
            ->get();
    }

    public function listBanners()
    {
        return Gallery::with('asset')
            ->whereNull('deleted_at')
            ->where('is_active', true)
            ->where('is_homepage_banner', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function listPaginated($page = 1, $perPage = 20)
    {
        $query = Gallery::with('asset')
            ->whereNull('deleted_at') // ✅ Exclude archived
            ->orderBy('sort_order');

        $total = $query->count();
        $items = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

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
        // ✅ Show ALL records including archived
        $query = Gallery::with('asset');

        $total = $query->count();

        if ($search = $request->input('search.value')) {
            $query->whereHas('asset', fn($q) => $q->where('file_name', 'like', "%{$search}%"));
        }

        $filtered = $query->count();

        $columns = ['image', 'file_name', 'sort_order', 'created_at', 'updated_at', 'actions'];
        $orderIndex = $request->input('order.0.column', 2);
        $orderDir = $request->input('order.0.dir', 'asc');
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        if (($columns[$orderIndex] ?? null) === 'sort_order') {
            $query->orderBy('sort_order', $orderDir);
        }

        $items = $query->offset($start)->limit($length)->get();

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
                                    loading="lazy">
                           </a>'
                        : '<span class="text-muted">No Image</span>',
                    'file_name' => $item->asset->file_name ?? '—',
                    'sort_order' => $item->sort_order,
                    'status' => $item->is_active
                        ? '<span class="badge badge-success">Active</span>'
                        : '<span class="badge badge-danger">Inactive</span>',
                    'is_homepage_banner' => $item->is_homepage_banner
                        ? '<span class="badge badge-info"><i class="fas fa-home mr-1"></i>Banner</span>'
                        : '<span class="badge badge-secondary">No</span>',
                    'created_at' => $item->created_at->toDateTimeString(),
                    'updated_at' => $item->updated_at->toDateTimeString(),
                    'archived' => !is_null($item->deleted_at), // ✅ Pass archive state
                    'actions' => view('admin.gallery.partials.actions', compact('item'))->render(),
                ];
            }),
        ];
    }

    public function create(Request $request): Gallery
    {
        return DB::transaction(function () use ($request) {
            try {
                $validated = $request->validate([
                    'sort_order' => 'nullable|integer',
                    'is_active' => 'nullable|boolean',
                    'is_homepage_banner' => 'nullable|boolean', 
                    'image' => 'required|image',
                ]);

                $asset = $this->storeImageAsset($request->file('image'));
                $thumbnailPath = $this->createThumbnail($request->file('image'));

                return Gallery::create([
                    'asset_id' => $asset->id,
                    'thumbnail_path' => $thumbnailPath,
                    'sort_order' => $validated['sort_order'] ?? 0,
                    'is_active' => $validated['is_active'] ?? true, 
                    'is_homepage_banner' => $validated['is_homepage_banner'] ?? false, 
                    'updated_by' => Auth::id(),
                ]);
            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    public function update(Request $request, Gallery $gallery): Gallery
    {
        return DB::transaction(function () use ($request, $gallery) {
            try {
                $validated = $request->validate([
                    'sort_order' => 'nullable|integer',
                    'is_active' => 'nullable|boolean',
                    'is_homepage_banner' => 'nullable|boolean', 
                    'image' => 'nullable|image',
                ]);

                if ($request->hasFile('image')) {
                    if ($gallery->thumbnail_path) {
                        Storage::disk('public')->delete($gallery->thumbnail_path);
                    }

                    $this->replaceAsset($gallery, $request->file('image'));

                    $gallery->thumbnail_path = $this->createThumbnail($request->file('image'));
                }

                $gallery->update([
                    'sort_order' => $validated['sort_order'] ?? $gallery->sort_order,
                    'is_active' => $validated['is_active'] ?? $gallery->is_active,
                    'is_homepage_banner' => $validated['is_homepage_banner'] ?? $gallery->is_homepage_banner,
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
     * ✅ Hard delete — must be inactive first
     */
    public function delete(Gallery $gallery): void
    {
        try {
            DB::transaction(function () use ($gallery) {

                // ✅ Guard: must be inactive before hard deleting
                if ($gallery->is_active) {
                    throw new DomainException(
                        "Cannot delete this gallery item. Please deactivate it before deleting."
                    );
                }

                // Delete thumbnail
                if ($gallery->thumbnail_path) {
                    Storage::disk('public')->delete($gallery->thumbnail_path);
                }

                // Delete asset
                if ($gallery->asset) {
                    Storage::disk('public')->delete($gallery->asset->storage_path);
                    $gallery->asset->delete();
                }

                $gallery->delete();
            });
        } catch (DomainException $e) {
            throw $e;
        } catch (Throwable $e) {
            report($e);
            throw new DomainException('Failed to delete gallery item.');
        }
    }

    /**
     * ✅ Archive — sets is_active = false + deleted_at = now()
     */
    public function archive(Gallery $gallery): Gallery
    {
        try {
            DB::table('gallery')->where('id', $gallery->id)->update([
                'is_active' => false,
                'deleted_at' => now(),
            ]);

            return $gallery->fresh();
        } catch (Throwable $e) {
            report($e);
            throw new DomainException('Failed to archive gallery item.');
        }
    }

    /**
     * ✅ Unarchive — sets is_active = true + deleted_at = null
     */
    public function unarchive(Gallery $gallery): Gallery
    {
        try {
            DB::table('gallery')->where('id', $gallery->id)->update([
                'is_active' => true,
                'deleted_at' => null,
            ]);

            return $gallery->fresh();
        } catch (Throwable $e) {
            report($e);
            throw new DomainException('Failed to unarchive gallery item.');
        }
    }

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

    private function replaceAsset(Gallery $gallery, $file): Asset
    {
        if ($gallery->asset) {
            Storage::disk('public')->delete($gallery->asset->storage_path);
            $gallery->asset->delete();
        }

        $asset = $this->storeImageAsset($file);

        $gallery->update(['asset_id' => $asset->id]);

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
        imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);

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