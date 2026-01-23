<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DotuniNews;
use App\Models\DotuniNewsAsset;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DotuniNewsController extends Controller
{
    public function index()
    {
        $news = DotuniNews::with([
            'author:id,name',
            'attachments.asset'
        ])
            ->orderByRaw("CASE status WHEN 'published' THEN 0 ELSE 1 END")
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get();

        return view('admin.dotuni_news.index', compact('news'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateBase($request);

        $slug = $this->makeUniqueSlug($validated['title']);

        $publishedAt = $validated['published_at'] ?? null;
        if ($validated['status'] === 'published' && !$publishedAt) {
            $publishedAt = now();
        }

        $news = DotuniNews::create([
            'title' => $validated['title'],
            'headline' => $validated['headline'] ?? null,
            'slug' => $slug,
            'seo_title' => $validated['seo_title'],
            'seo_description' => $validated['seo_description'],
            'status' => $validated['status'],
            'visibility' => $validated['visibility'],
            'author_id' => Auth::id(),
            'published_at' => $publishedAt,
            'updated_by' => Auth::id(),
        ]);

        // MEDIA ROWS (image + caption)
        $this->handleMediaUploads($request, $news);

        // Fallback thumbnail upload (if no media thumbnail chosen)
        if ($request->hasFile('thumbnail') && !$news->attachments()->where('is_thumbnail', true)->exists()) {
            $this->attachThumbnail($news, $request->file('thumbnail'));
        }

        return response()->json(['message' => 'DotUni News created successfully'], 201);
    }

    public function edit(DotuniNews $dotuniNews)
    {
        try {
            $dotuniNews->load(['author', 'attachments.asset']);

            $data = $dotuniNews->toArray();

            $thumbnail = $dotuniNews->attachments->firstWhere('is_thumbnail', true);
            $data['thumbnail'] = $thumbnail && $thumbnail->asset
                ? ['storage_path' => $thumbnail->asset->storage_path]
                : null;

            $data['media'] = $dotuniNews->attachments->map(function ($attach) {
                return [
                    'id' => $attach->id, // ← add this
                    'image_path' => $attach->asset->storage_path ?? null,
                    'caption' => $attach->caption ?? '',
                    'is_thumbnail' => $attach->is_thumbnail ?? false,
                    'sort_order' => $attach->sort_order ?? 0,
                ];
            })->toArray();

            $data['published_at'] = optional($dotuniNews->published_at)->format('Y-m-d H:i:s');

            return response()->json($data);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, DotuniNews $dotuniNews)
    {
        $validated = $this->validateBase($request);

        if ($dotuniNews->title !== $validated['title']) {
            $dotuniNews->slug = $this->makeUniqueSlug($validated['title'], $dotuniNews->id);
        }

        $publishedAt = $validated['published_at'] ?? null;
        if ($validated['status'] === 'published' && !$publishedAt) {
            $publishedAt = $dotuniNews->published_at ?? now();
        }

        $dotuniNews->update([
            'title' => $validated['title'],
            'headline' => $validated['headline'] ?? null,
            'seo_title' => $validated['seo_title'],
            'seo_description' => $validated['seo_description'],
            'status' => $validated['status'],
            'visibility' => $validated['visibility'],
            'published_at' => $publishedAt,
            'updated_by' => Auth::id(),
        ]);

        // ============================
        // HANDLE MEDIA UPDATES
        // ============================

        $existingIds = $request->input('existing_media_ids', []); // array of IDs to keep

        // Delete media that are not in existing_media_ids
        $dotuniNews->attachments()->whereNotIn('id', $existingIds)->get()->each(function ($attachment) {
            if ($attachment->asset) {
                Storage::disk('public')->delete($attachment->asset->storage_path);
                $attachment->asset->delete();
            }
            $attachment->delete();
        });

        // Update existing media fields (caption, is_thumbnail, sort_order)
        foreach ($existingIds as $index => $mediaId) {
            $mediaData = $request->input('media')[$index] ?? null;
            if (!$mediaData)
                continue;

            $attachment = DotuniNewsAsset::find($mediaId);
            if ($attachment) {
                $attachment->update([
                    'caption' => $mediaData['caption'] ?? null,
                    'is_thumbnail' => !empty($mediaData['is_thumbnail']),
                    'sort_order' => $mediaData['sort_order'] ?? $index,
                ]);
            }
        }

        // Handle new media uploads
        $this->handleMediaUploads($request, $dotuniNews);

        // Replace thumbnail if uploaded AND no media thumbnail selected
        if ($request->hasFile('thumbnail') && !$dotuniNews->attachments()->where('is_thumbnail', true)->exists()) {
            $this->attachThumbnail($dotuniNews, $request->file('thumbnail'));
        }

        return response()->json(['message' => 'DotUni News updated successfully']);
    }



    // public function update(Request $request, DotuniNews $dotuniNews)
    // {
    //     $validated = $this->validateBase($request);

    //     if ($dotuniNews->title !== $validated['title']) {
    //         $dotuniNews->slug = $this->makeUniqueSlug($validated['title'], $dotuniNews->id);
    //     }

    //     $publishedAt = $validated['published_at'] ?? null;
    //     if ($validated['status'] === 'published' && !$publishedAt) {
    //         $publishedAt = $dotuniNews->published_at ?? now();
    //     }

    //     $dotuniNews->update([
    //         'title' => $validated['title'],
    //         'headline' => $validated['headline'] ?? null,
    //         'seo_title' => $validated['seo_title'],
    //         'seo_description' => $validated['seo_description'],
    //         'status' => $validated['status'],
    //         'visibility' => $validated['visibility'],
    //         'published_at' => $publishedAt,
    //         'updated_by' => Auth::id(),
    //     ]);

    //     // Remove old media
    //     $this->deleteAllMedia($dotuniNews);

    //     // Re-attach new media
    //     $this->handleMediaUploads($request, $dotuniNews);

    //     // Replace thumbnail if uploaded AND no media thumbnail selected
    //     if ($request->hasFile('thumbnail') && !$dotuniNews->attachments()->where('is_thumbnail', true)->exists()) {
    //         $this->attachThumbnail($dotuniNews, $request->file('thumbnail'));
    //     }

    //     return response()->json(['message' => 'DotUni News updated successfully']);
    // }

    public function destroy(DotuniNews $dotuniNews)
    {
        $this->deleteAllMedia($dotuniNews);
        $dotuniNews->delete();

        return response()->json(['message' => 'DotUni News deleted successfully']);
    }

    /* ==========================
     * MEDIA HANDLING
     * ========================== */

    private function handleMediaUploads(Request $request, DotuniNews $news): void
    {
        if (!$request->has('media'))
            return;

        foreach ($request->media as $index => $media) {
            if (!isset($media['image']))
                continue;

            $asset = $this->storeImageAsAsset($media['image'], 'dotuni_news');

            DotuniNewsAsset::create([
                'news_id' => $news->id,
                'asset_id' => $asset->id,
                'caption' => $media['caption'] ?? null,
                'is_thumbnail' => !empty($media['is_thumbnail']),
                'is_cover' => !empty($media['is_cover']),
                'sort_order' => $media['sort_order'] ?? $index,
            ]);
        }
    }

    private function attachThumbnail(DotuniNews $news, $file): void
    {
        $asset = $this->storeImageAsAsset($file, 'dotuni_news');

        DotuniNewsAsset::create([
            'news_id' => $news->id,
            'asset_id' => $asset->id,
            'caption' => null,
            'is_thumbnail' => true,
            'is_cover' => false,
            'sort_order' => 0,
        ]);
    }

    private function deleteAllMedia(DotuniNews $news): void
    {
        $news->load('attachments.asset');

        foreach ($news->attachments as $attachment) {
            if ($attachment->asset) {
                Storage::disk('public')->delete($attachment->asset->storage_path);
                $attachment->asset->delete();
            }
            $attachment->delete();
        }
    }

    /* ==========================
     * VALIDATION
     * ========================== */

    private function validateBase(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:250',
            'headline' => 'nullable|string|max:250',
            'seo_title' => 'required|string|max:250',
            'seo_description' => 'required|string|max:300',
            'status' => 'required|in:draft,submitted,published,archived',
            'visibility' => 'required|in:public,private,unlisted',
            'published_at' => 'nullable|date',
            'thumbnail' => 'nullable|image|max:2048',
        ]);
    }

    /* ==========================
     * HELPERS
     * ========================== */

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
            'alt_text' => null,
            'uploaded_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);
    }
}
