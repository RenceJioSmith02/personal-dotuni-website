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
                    'status' => $validated['status'],
                    'visibility' => $validated['visibility'],
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
                    'status' => $validated['status'],
                    'visibility' => $validated['visibility'],
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
                }
                break;

            case 'layout_3':
                if ($request->hasFile('split_left_image')) {
                    $this->replaceCoverAssets($news);
                    $this->handleSplitLayout($request, $news);
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

    private function syncMediaRows(Request $request, DotuniNews $news): void
    {
        $existingIds = $request->input('existing_media_ids', []);

        $news->attachments()
            ->whereNotIn('id', $existingIds)
            ->get()
            ->each(fn($a) => $this->deleteAttachment($a));

        foreach ($existingIds as $index => $id) {
            $data = $request->input("media.$index", []);
            $row = DotuniNewsAsset::find($id);
            if (!$row)
                continue;

            $row->update([
                'caption' => $data['caption'] ?? null,
                'sort_order' => $data['sort_order'] ?? $index,
                'is_thumbnail' => $index === 0,
            ]);
        }
    }

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
        if ($validated['status'] !== 'published')
            return $validated['published_at'] ?? null;
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
            'status' => 'required|in:draft,submitted,published,archived',
            'visibility' => 'required|in:public,private,unlisted',
            'layout' => 'required|string|max:50',
            'published_at' => 'nullable|date',
            'article_body' => 'nullable|string',
        ]);
    }
}
