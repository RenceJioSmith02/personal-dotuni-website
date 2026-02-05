<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\DotuniNews;
use App\Models\DotuniNewsAsset;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Throwable;

class DotuniNewsService
{
    /**
     * List all news with attachments & author
     */
    public function list()
    {
        return DotuniNews::with(['author:id,name', 'attachments.asset'])
            ->orderByRaw("CASE status WHEN 'published' THEN 0 ELSE 1 END")
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get();
    }

    public function datatable(Request $request)
    {
        $query = DotuniNews::with(['author:id,name', 'attachments.asset']);

        $total = $query->count();

        // Search
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('seo_description', 'like', "%{$search}%");
            });
        }

        $filtered = $query->count();

        // Ordering
        $columns = ['thumbnail', 'title', 'seo_description', 'status', 'visibility', 'published_at', 'actions'];
        $orderColumnIndex = $request->input('order.0.column', 1);
        $orderColumn = $columns[$orderColumnIndex] ?? 'published_at';
        $orderDir = $request->input('order.0.dir', 'desc');

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        $orderDir = $orderDir === 'asc' ? 'asc' : 'desc';

        if (!in_array($orderColumn, ['thumbnail', 'actions'])) {
            $query->orderBy($orderColumn, $orderDir);
        }

        $data = $query->offset($start)->limit($length)->get();

        return [
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data->map(function ($item) {
                $thumbnail = $item->attachments->firstWhere('is_thumbnail', true)?->asset;

                // Status badge
                $statusClass = match ($item->status) {
                    'published' => 'badge-success',
                    'submitted' => 'badge-warning',
                    'archived' => 'badge-secondary',
                    default => 'badge-info',
                };

                // Visibility badge
                $visClass = match ($item->visibility) {
                    'public' => 'badge-success',
                    'unlisted' => 'badge-warning',
                    default => 'badge-secondary',
                };

                return [
                    'thumbnail' => $thumbnail
                        ? '<img src="' . asset('storage/' . $thumbnail->storage_path) . '" class="img-thumbnail" style="max-width:50px;" alt="' . ($thumbnail->alt_text ?? $item->title) . '">'
                        : '<span class="text-muted">No Image</span>',
                    'title' => $item->title,
                    'seo_description' => \Str::limit($item->seo_description, 80),
                    'status' => '<span class="badge ' . $statusClass . '">' . ucfirst($item->status) . '</span>',
                    'visibility' => '<span class="badge ' . $visClass . '">' . ucfirst($item->visibility) . '</span>',
                    'published_at' => $item->published_at ? $item->published_at->format('Y-m-d H:i') : '<span class="text-muted">—</span>',
                    'actions' => view('admin.dotuni_news.partials.actions', compact('item'))->render()
                ];
            }),
        ];
    }


    /**
     * Create news with layout & media
     */
    public function create(Request $request): DotuniNews
    {
        return DB::transaction(function () use ($request) {
            try {
                $validated = $this->validateBase($request);

                $news = DotuniNews::create([
                    'title' => $validated['title'],
                    'headline' => $validated['headline'] ?? null,
                    'slug' => $this->makeUniqueSlug($validated['title']),
                    'seo_title' => $validated['seo_title'],
                    'seo_description' => $validated['seo_description'],
                    'status' => $validated['status'] ?? 'submitted',
                    'visibility' => $validated['visibility'] ?? 'public',
                    'article_body' => $validated['article_body'] ?? null,
                    'layout' => $validated['layout'],
                    'author_id' => Auth::id(),
                    'published_at' => $this->resolvePublishedAt($validated),
                    'updated_by' => Auth::id(),
                ]);

                $this->handleLayout($request, $news, $validated['layout'], false);

                return $news;
            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    /**
     * Update news with layout & media
     */
    public function update(Request $request, DotuniNews $news): DotuniNews
    {
        return DB::transaction(function () use ($request, $news) {
            try {
                $validated = $this->validateBase($request);

                if ($news->title !== $validated['title']) {
                    $news->slug = $this->makeUniqueSlug($validated['title'], $news->id);
                }

                $news->update([
                    'title' => $validated['title'],
                    'headline' => $validated['headline'] ?? null,
                    'seo_title' => $validated['seo_title'],
                    'seo_description' => $validated['seo_description'],
                    'status' => $validated['status'] ?? $news->status,
                    'visibility' => $validated['visibility'] ?? $news->visibility,
                    'layout' => $validated['layout'],
                    'article_body' => $validated['article_body'] ?? null,
                    'published_at' => $this->resolvePublishedAt($validated, $news),
                    'updated_by' => Auth::id(),
                ]);

                $this->handleLayout($request, $news, $validated['layout'], true);

                return $news;
            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    /**
     * Delete news and all media
     */
    public function delete(DotuniNews $news): void
    {
        DB::transaction(function () use ($news) {
            $this->deleteAllMedia($news);
            $news->delete();
        });
    }

    /* ================================
       LAYOUT HANDLER
    ================================= */

    private function handleLayout(Request $request, DotuniNews $news, string $layout, bool $isUpdate): void
    {
        switch ($layout) {
            case 'layout_1':
            case 'layout_5':
                if ($isUpdate)
                    $this->syncMediaRows($request, $news);
                $this->handleMediaUploads($request, $news);
                break;

            case 'layout_2':
                if ($request->hasFile('hero_image')) {
                    $this->replaceCoverAssets($news);
                    $this->handleHeroLayout($request, $news);
                } else {
                    $news->attachments()
                        ->where('is_cover', true)
                        ->update(['caption' => $request->hero_caption]);
                }
                break;

            case 'layout_3':
                if ($request->hasFile('split_left_image')) {
                    $this->replaceCoverAssets($news);
                    $this->handleSplitLayout($request, $news);
                } else {
                    $news->attachments()
                        ->where('is_cover', true)
                        ->update(['caption' => $request->split_right_caption]);
                }
                break;

            case 'layout_4': // article only
                break;
        }
    }

    /* ================================
       LAYOUT IMPLEMENTATIONS
    ================================= */

    private function handleHeroLayout(Request $request, DotuniNews $news): void
    {
        $asset = $this->storeImageAsAsset($request->file('hero_image'), 'dotuni_news');

        DotuniNewsAsset::create([
            'news_id' => $news->id,
            'asset_id' => $asset->id,
            'caption' => $request->hero_caption,
            'is_cover' => true,
            'is_thumbnail' => true,
            'sort_order' => 0,
        ]);
    }

    private function handleSplitLayout(Request $request, DotuniNews $news): void
    {
        $asset = $this->storeImageAsAsset($request->file('split_left_image'), 'dotuni_news');

        DotuniNewsAsset::create([
            'news_id' => $news->id,
            'asset_id' => $asset->id,
            'caption' => $request->split_right_caption,
            'is_cover' => true,
            'is_thumbnail' => true,
            'sort_order' => 0,
        ]);
    }

    /* ================================
       MEDIA HANDLING (layout_1 / layout_5)
    ================================= */

    // private function syncMediaRows(Request $request, DotuniNews $news): void
    // {
    //     $existingIds = $request->input('existing_media_ids', []);

    //     $news->attachments()
    //         ->whereNotIn('id', $existingIds)
    //         ->get()
    //         ->each(fn($a) => $this->deleteAttachment($a));

    //     foreach ($existingIds as $index => $id) {
    //         $data = $request->input("media.$index", []);
    //         $row = DotuniNewsAsset::find($id);
    //         if (!$row)
    //             continue;

    //         $row->update([
    //             'caption' => $data['caption'] ?? null,
    //             'sort_order' => $data['sort_order'] ?? $index,
    //             'is_thumbnail' => $index === 0,
    //         ]);
    //     }
    // }

    private function syncMediaRows(Request $request, DotuniNews $news): void
    {
        $existingIdsFromInput = $request->input('existing_media_ids', []);
        $media = $request->input('media', []);

        // Combine existing IDs from both media rows AND existing_media_ids input
        $keptIds = collect($media)
            ->pluck('id')
            ->filter() // removes null / undefined
            ->merge($existingIdsFromInput) // <- important for layout 5
            ->unique()
            ->values()
            ->toArray();

        // delete only DB records that are truly removed
        $news->attachments()
            ->whereNotIn('id', $keptIds)
            ->get()
            ->each(fn($a) => $this->deleteAttachment($a));

        // determine thumbnail by sort_order (existing media first)
        $thumbnailId = collect($media)
            ->merge(
                collect($existingIdsFromInput)
                    ->map(fn($id) => ['id' => $id, 'sort_order' => 0]) // fallback sort_order for existing only IDs
            )
            ->sortBy('sort_order')
            ->first()['id'] ?? null;

        // Update existing media from media rows
        foreach ($media as $row) {
            if (!isset($row['id']))
                continue;

            $attachment = DotuniNewsAsset::find($row['id']);
            if (!$attachment)
                continue;

            $attachment->update([
                'caption' => $row['caption'] ?? null,
                'sort_order' => $row['sort_order'] ?? 0,
                'is_thumbnail' => $row['id'] === $thumbnailId,
            ]);
        }
    }



    // working version (except layout 5)
    // private function syncMediaRows(Request $request, DotuniNews $news): void
    // {
    //     $existingIds = $request->input('existing_media_ids', []);
    //     $media = $request->input('media', []);

    //     // collect ONLY valid existing IDs from media rows
    //     $keptIds = collect($media)
    //         ->pluck('id')
    //         ->filter() // removes null / undefined
    //         ->values()
    //         ->toArray();

    //     // delete only DB records that are truly removed
    //     $news->attachments()
    //         ->whereNotIn('id', $keptIds)
    //         ->get()
    //         ->each(fn($a) => $this->deleteAttachment($a));
        
    //     // determine thumbnail by sort_order
    //     $thumbnailId = collect($media)
    //         ->filter(fn($row) => isset($row['id'])) // only existing media
    //         ->sortBy('sort_order')
    //         ->first()['id'] ?? null;


    //     foreach ($media as $row) {
    //         if (!isset($row['id'])) {
    //             continue; // new uploads handled elsewhere
    //         }

    //         $attachment = DotuniNewsAsset::find($row['id']);
    //         if (!$attachment)
    //             continue;

    //         $attachment->update([
    //             'caption' => $row['caption'] ?? null,
    //             'sort_order' => $row['sort_order'] ?? 0,
    //             'is_thumbnail' => $row['id'] === $thumbnailId,
    //         ]);
    //     }

    // }


    private function handleMediaUploads(Request $request, DotuniNews $news): void
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
            $asset = $this->storeImageAsAsset($media['image'], 'dotuni_news');

            $attachment = DotuniNewsAsset::create([
                'news_id' => $news->id,
                'asset_id' => $asset->id,
                'caption' => $media['caption'],
                'sort_order' => $media['sort_order'],
                'is_thumbnail' => $i === 0,
                'is_cover' => false,
            ]);

            Gallery::create([
                'asset_id' => $asset->id,
                'sort_order' => $media['sort_order'],
                'updated_by' => Auth::id(),
            ]);
        }
    }

    /* ================================
       MEDIA HELPERS
    ================================= */

    private function replaceCoverAssets(DotuniNews $news): void
    {
        $news->attachments()->where('is_cover', true)->get()
            ->each(fn($a) => $this->deleteAttachment($a));
    }

    private function deleteAttachment(DotuniNewsAsset $attachment): void
    {
        if ($attachment->asset) {
            Gallery::where('asset_id', $attachment->asset_id)->delete();
            Storage::disk('public')->delete($attachment->asset->storage_path);
            $attachment->asset->delete();
        }
        $attachment->delete();
    }

    private function deleteAllMedia(DotuniNews $news): void
    {
        $news->load('attachments.asset');
        foreach ($news->attachments as $attachment) {
            $this->deleteAttachment($attachment);
        }
    }

    /* ================================
       UTILITIES
    ================================= */

    public function transformForEdit(DotuniNews $news): array
    {
        $cover = $news->attachments->firstWhere('is_cover', true);

        return array_merge($news->toArray(), [
            'media' => $news->attachments
                ->where('is_cover', false)
                ->map(fn($a) => [
                    'id' => $a->id,
                    'image_path' => $a->asset->storage_path ?? null,
                    'caption' => $a->caption,
                    'is_thumbnail' => $a->is_thumbnail,
                    'sort_order' => $a->sort_order,
                ])
                ->values(),

            'hero_image' => $news->layout === 'layout_2' && $cover ? $cover->asset->storage_path : null,
            'hero_caption' => $news->layout === 'layout_2' ? $cover?->caption : null,
            'split_left_image' => $news->layout === 'layout_3' && $cover ? $cover->asset->storage_path : null,
            'split_right_caption' => $news->layout === 'layout_3' ? $cover?->caption : null,
        ]);
    }

    private function resolvePublishedAt(array $validated, ?DotuniNews $news = null)
    {
        $status = $validated['status'] ?? $news?->status ?? 'submitted';

        if ($status !== 'published') {
            return $validated['published_at'] ?? $news?->published_at;
        }

        return $validated['published_at'] ?? $news?->published_at ?? now();
    }


    private function makeUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 2;

        while (
            DotuniNews::withTrashed()
                ->where('slug', $slug)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    private function storeImageAsAsset($file, string $folder): Asset
    {
        $path = $file->store($folder, 'public');

        return Asset::create([
            'kind' => 'image',
            'file_name' => $file->getClientOriginalName(),
            'storage_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size_kb' => round($file->getSize() / 1024),
            'uploaded_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);
    }

    private function validateBase(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:250',
            'headline' => 'nullable|string|max:250',
            'seo_title' => 'required|string|max:250',
            'seo_description' => 'required|string|max:300',
            'status' => 'nullable|in:submitted,published,archived',
            'visibility' => 'nullable|in:public,private,unlisted,',
            'layout' => 'required|string|max:50',
            'published_at' => 'nullable|date',
            'article_body' => 'nullable|string',
        ]);
    }


    public function publish(DotuniNews $news): DotuniNews
    {
        return DB::transaction(function () use ($news) {

            if ($news->status !== 'submitted') {
                return $news;
            }

            $news->update([
                'status' => 'published',
                'published_at' => $news->published_at ?? now(),
                'updated_by' => Auth::id(),
            ]);

            return $news;
        });
    }


}
