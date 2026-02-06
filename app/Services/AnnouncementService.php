<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\Asset;
use App\Models\AnnouncementAsset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Throwable;

class AnnouncementService
{
    /**
     * List all announcements with relationships.
     */
    public function list()
    {
        return Announcement::with(['author:id,name', 'assets'])
            ->orderByRaw("CASE status WHEN 'published' THEN 0 ELSE 1 END")
            ->orderByDesc('publish_start')
            ->orderByDesc('id')
            ->get();
    }



    public function datatable(Request $request)
    {
        $query = Announcement::with(['author:id,name', 'assets']);

        $total = $query->count();

        // SEARCH
        if ($search = $request->input('search.value')) {
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('seo_description', 'like', "%{$search}%");
        }

        $filtered = $query->count();

        // ORDERING
        $columns = ['title', 'seo_description', 'status', 'visibility', 'publish_start'];
        $orderColumn = $columns[$request->input('order.0.column', 0)] ?? 'publish_start';
        $orderDir = $request->input('order.0.dir', 'desc');

        $query->orderBy($orderColumn, $orderDir);

        // PAGINATION
        $data = $query->skip($request->start)
            ->take($request->length)
            ->get();

        // RESPONSE
        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data->map(function ($item) {
                $thumbnail = $item->assets->firstWhere('pivot.is_thumbnail', true);

                $statusClass = match ($item->status) {
                    'published' => 'badge-success',
                    'submitted' => 'badge-warning',
                    'archived' => 'badge-secondary',
                    default => 'badge-info',
                };

                $visClass = match ($item->visibility) {
                    'public' => 'badge-success',
                    'unlisted' => 'badge-warning',
                    default => 'badge-secondary',
                };

                return [
                    'thumbnail' => $thumbnail && $thumbnail->kind === 'image'
                        ? '<img src="' . asset('storage/' . $thumbnail->storage_path) . '" class="img-thumbnail" style="max-width:50px;" alt="' . ($thumbnail->alt_text ?? $item->title) . '">'
                        : '<span class="text-muted">No Image</span>',
                    'title' => $item->title,
                    'seo_description' => Str::limit($item->seo_description, 80),
                    'status' => '<span class="badge ' . $statusClass . '">' . ucfirst($item->status) . '</span>',
                    'visibility' => '<span class="badge ' . $visClass . '">' . ucfirst($item->visibility) . '</span>',
                    'publish_window' => $item->publish_start
                        ? $item->publish_start->format('Y-m-d') .
                        ($item->publish_end ? '<br><small class="text-muted">to ' . $item->publish_end->format('Y-m-d') . '</small>' : '')
                        : '<span class="text-muted">—</span>',
                    'actions' => view('admin.clsu.announcement.partials.actions', compact('item'))->render(),
                ];
            }),
        ]);
    }


    /**
     * Create a new announcement with layout & assets.
     */
    public function create(Request $request): Announcement
    {
        return DB::transaction(function () use ($request) {
            try {
                $validated = $this->validateBase($request);

                $announcement = Announcement::create([
                    'title' => $validated['title'],
                    'slug' => $this->makeUniqueSlug($validated['title']),
                    'seo_title' => $validated['seo_title'],
                    'seo_description' => $validated['seo_description'],
                    'status' => $validated['status'],
                    'visibility' => $validated['visibility'],
                    'layout' => $validated['layout'],
                    'article_body' => $validated['article_body'] ?? null,
                    'author_id' => Auth::id(),
                    'publish_start' => $validated['publish_start'] ?? null,
                    'publish_end' => $validated['publish_end'] ?? null,
                    'updated_by' => Auth::id(),
                ]);

                $this->handleLayout($request, $announcement, $validated['layout'], false);

                return $announcement;
            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    /**
     * Update an existing announcement with layout & assets.
     */
    public function update(Request $request, Announcement $announcement): Announcement
    {
        return DB::transaction(function () use ($request, $announcement) {
            try {
                $validated = $this->validateBase($request);

                if ($announcement->title !== $validated['title']) {
                    $announcement->slug = $this->makeUniqueSlug($validated['title'], $announcement->id);
                }

                $announcement->update([
                    'title' => $validated['title'],
                    'seo_title' => $validated['seo_title'],
                    'seo_description' => $validated['seo_description'],
                    'status' => $validated['status'],
                    'visibility' => $validated['visibility'],
                    'layout' => $validated['layout'],
                    'article_body' => $validated['article_body'] ?? null,
                    'publish_start' => $validated['publish_start'] ?? null,
                    'publish_end' => $validated['publish_end'] ?? null,
                    'updated_by' => Auth::id(),
                ]);

                $this->handleLayout($request, $announcement, $validated['layout'], true);

                return $announcement;
            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    /**
     * Delete an announcement and all related assets.
     */
    public function delete(Announcement $announcement): void
    {
        DB::transaction(function () use ($announcement) {
            $this->deleteAllAssets($announcement);
            $announcement->delete();
        });
    }

    /**
     * Handle layout-specific assets and uploads.
     */
    private function handleLayout(Request $request, Announcement $announcement, string $layout, bool $isUpdate): void
    {
        switch ($layout) {
            case 'layout_1':
            case 'layout_5':
                if ($isUpdate)
                    $this->syncMediaRows($request, $announcement);
                $this->handleMediaUploads($request, $announcement);
                break;

            case 'layout_2':
                if ($request->hasFile('hero_image')) {
                    $this->replaceCoverAssets($announcement);
                    $this->handleHeroLayout($request, $announcement);
                } else {
                    AnnouncementAsset::where('announcement_id', $announcement->id)
                        ->where('is_cover', true)
                        ->update(['caption' => $request->hero_caption]);
                }
                break;

            case 'layout_3':
                if ($request->hasFile('split_left_image')) {
                    $this->replaceCoverAssets($announcement);
                    $this->handleSplitLayout($request, $announcement);
                } else {
                    AnnouncementAsset::where('announcement_id', $announcement->id)
                        ->where('is_cover', true)
                        ->update(['caption' => $request->split_right_caption]);
                }
                break;


            case 'layout_4':
                // article-only layout, no media
                break;
        }

        if ($request->hasFile('documents')) {
            $this->handleDocumentUploads($request, $announcement);
        }
    }

    /* ================================
       ASSET HANDLERS
    ================================= */

    private function handleDocumentUploads(Request $request, Announcement $announcement): void
    {
        foreach ($request->file('documents') as $i => $file) {
            $asset = $this->storeFileAsAsset($file);

            AnnouncementAsset::create([
                'announcement_id' => $announcement->id,
                'asset_id' => $asset->id,
                'caption' => null,
                'sort_order' => 1000 + $i,
                'is_thumbnail' => false,
                'is_cover' => false,
            ]);
        }
    }

    private function handleHeroLayout(Request $request, Announcement $announcement): void
    {
        $asset = $this->storeFileAsAsset($request->file('hero_image'));

        AnnouncementAsset::create([
            'announcement_id' => $announcement->id,
            'asset_id' => $asset->id,
            'caption' => $request->hero_caption,
            'is_cover' => true,
            'is_thumbnail' => true,
            'sort_order' => 0,
        ]);
    }

    private function handleSplitLayout(Request $request, Announcement $announcement): void
    {
        $asset = $this->storeFileAsAsset($request->file('split_left_image'));

        AnnouncementAsset::create([
            'announcement_id' => $announcement->id,
            'asset_id' => $asset->id,
            'caption' => $request->split_right_caption,
            'is_cover' => true,
            'is_thumbnail' => true,
            'sort_order' => 0,
        ]);
    }

    private function handleMediaUploads(Request $request, Announcement $announcement): void
    {
        if (!$request->has('media'))
            return;

        $mediaData = [];
        foreach ($request->media as $index => $media) {
            if (!isset($media['image']))
                continue;

            $mediaData[] = [
                'image' => $media['image'],
                'caption' => $media['caption'] ?? null,
                'sort_order' => $media['sort_order'] ?? $index,
            ];
        }

        usort($mediaData, fn($a, $b) => $a['sort_order'] <=> $b['sort_order']);

        foreach ($mediaData as $i => $media) {
            $asset = $this->storeFileAsAsset($media['image']);

            AnnouncementAsset::create([
                'announcement_id' => $announcement->id,
                'asset_id' => $asset->id,
                'caption' => $media['caption'],
                'sort_order' => $media['sort_order'],
                'is_thumbnail' => $i === 0,
                'is_cover' => false,
            ]);
        }
    }


    private function syncMediaRows(Request $request, Announcement $announcement): void
    {
        $existingIdsFromInput = $request->input('existing_media_ids', []);
        $media = $request->input('media', []);

        // Combine IDs from media rows and existing_media_ids input
        $keptIds = collect($media)
            ->pluck('id')
            ->filter() // remove null / undefined
            ->merge($existingIdsFromInput) // ensure existing attachments are kept
            ->unique()
            ->values()
            ->toArray();

        // Delete only attachments that are truly removed
        $announcement->assets()
            ->whereNotIn('announcement_assets.id', $keptIds)
            ->get()
            ->each(fn($a) => $this->deleteAssetPivot($a->pivot));

        // Determine thumbnail by sort_order (existing media included)
        $thumbnailId = collect($media)
            ->filter(fn($row) => !empty($row['id']))
            ->sortBy('sort_order')
            ->first()['id'] ?? null;

        // Update existing media from media rows
        foreach ($media as $row) {
            if (!isset($row['id']))
                continue;

            $attachment = AnnouncementAsset::find($row['id']);
            if (!$attachment)
                continue;

            $attachment->update([
                'caption' => $row['caption'] ?? null,
                'sort_order' => $row['sort_order'] ?? 0,
                'is_thumbnail' => $row['id'] == $thumbnailId,
            ]);
        }
    }



    private function replaceCoverAssets(Announcement $announcement): void
    {
        $announcement->assets()->where('is_cover', true)->get()
            ->each(fn($a) => $this->deleteAssetPivot($a->pivot));
    }

    private function deleteAssetPivot($pivot): void
    {
        if (!$pivot instanceof AnnouncementAsset) {
            $pivot = AnnouncementAsset::find($pivot->id ?? $pivot);
        }

        if (!$pivot)
            return;

        if ($pivot->asset) {
            Storage::disk('public')->delete($pivot->asset->storage_path);
            $pivot->asset->delete();
        }

        $pivot->delete();
    }

    private function deleteAllAssets(Announcement $announcement): void
    {
        $announcement->load('assets');
        foreach ($announcement->assets as $asset) {
            $this->deleteAssetPivot($asset->pivot);
        }
    }

    /* ================================
       UTILITIES
    ================================= */

    public function transformForEdit(Announcement $announcement): array
    {
        $cover = $announcement->assets->firstWhere('pivot.is_cover', true);

        return array_merge($announcement->toArray(), [
            'hero_image' => $announcement->layout === 'layout_2' ? optional($cover)->storage_path : null,
            'hero_caption' => $announcement->layout === 'layout_2' ? optional($cover?->pivot)->caption : null,
            'split_left_image' => $announcement->layout === 'layout_3' ? optional($cover)->storage_path : null,
            'split_right_caption' => $announcement->layout === 'layout_3' ? optional($cover?->pivot)->caption : null,
            'media' => $announcement->assets
                ->where('pivot.is_cover', false)
                ->map(fn($asset) => [
                    'id' => $asset->pivot->id,
                    'image_path' => $asset->storage_path,
                    'caption' => $asset->pivot->caption,
                    'is_thumbnail' => $asset->pivot->is_thumbnail,
                    'sort_order' => $asset->pivot->sort_order,
                ])
                ->values(),
        ]);
    }

    private function storeFileAsAsset($file): Asset
    {
        $path = $file->store('announcements', 'public');

        return Asset::create([
            'kind' => str_starts_with($file->getMimeType(), 'image') ? 'image' : 'document',
            'file_name' => $file->getClientOriginalName(),
            'storage_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size_kb' => round($file->getSize() / 1024),
            'uploaded_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);
    }

    private function makeUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 2;

        while (
            Announcement::withTrashed()
                ->where('slug', $slug)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    private function validateBase(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:250',
            'seo_title' => 'required|string|max:250',
            'seo_description' => 'required|string|max:300',
            'status' => 'required|in:draft,submitted,published,archived',
            'visibility' => 'required|in:public,private,unlisted',
            'layout' => 'required|string|max:50',
            'publish_start' => 'nullable|date',
            'publish_end' => 'nullable|date|after_or_equal:publish_start',
            'article_body' => 'nullable|string',
        ]);
    }
}
