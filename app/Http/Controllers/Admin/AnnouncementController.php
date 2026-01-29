<?php

namespace App\Http\Controllers\Admin;

use App\Models\Asset;
use App\Models\Announcement;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\AnnouncementAsset;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    /* ==========================
     * INDEX
     * ========================== */

    public function index()
    {
        $announcements = Announcement::with(['author:id,name', 'assets'])
            ->orderByRaw("CASE status WHEN 'published' THEN 0 ELSE 1 END")
            ->orderByDesc('publish_start')
            ->orderByDesc('id')
            ->get();

        return view('admin.clsu.announcement.index', compact('announcements'));
    }

    /* ==========================
     * STORE
     * ========================== */

    public function store(Request $request)
    {
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

        return response()->json(['message' => 'Announcement created successfully'], 201);
    }

    /* ==========================
     * EDIT
     * ========================== */

    public function edit(Announcement $announcement)
    {
        $announcement->load(['author', 'assets']);

        return response()->json($this->transformForEdit($announcement));
    }

    /* ==========================
     * UPDATE
     * ========================== */

    public function update(Request $request, Announcement $announcement)
    {
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

        return response()->json(['message' => 'Announcement updated successfully']);
    }

    /* ==========================
     * DESTROY
     * ========================== */

    public function destroy(Announcement $announcement)
    {
        $this->deleteAllAssets($announcement);
        $announcement->delete();

        return response()->json(['message' => 'Announcement deleted successfully']);
    }

    /* ==========================
     * LAYOUT HANDLER
     * ========================== */

    // private function handleLayout(Request $request, Announcement $announcement, string $layout, bool $isUpdate): void
    // {
    //     switch ($layout) {

    //         // Image gallery / mixed media
    //         case 'layout_1':
    //         case 'layout_gallery':
    //             if ($isUpdate) {
    //                 $this->syncAssets($request, $announcement);
    //             }
    //             $this->handleUploads($request, $announcement);
    //             break;

    //         // Document-based layout
    //         case 'layout_documents':
    //             if ($isUpdate) {
    //                 $this->syncAssets($request, $announcement);
    //             }
    //             $this->handleUploads($request, $announcement, true);
    //             break;

    //         // Article only
    //         case 'layout_article':
    //             // no uploads
    //             break;
    //     }
    // }

    private function handleLayout(Request $request, Announcement $announcement, string $layout, bool $isUpdate): void
    {
        switch ($layout) {

            case 'layout_1':
                if ($isUpdate) {
                    $this->syncMediaRows($request, $announcement);
                }
                $this->handleMediaUploads($request, $announcement);
                break;

            case 'layout_2':
                if ($request->hasFile('hero_image')) {
                    $this->replaceCoverAssets($announcement);
                    $this->handleHeroLayout($request, $announcement);
                }
                break;

            case 'layout_3':
                if ($request->hasFile('split_left_image')) {
                    $this->replaceCoverAssets($announcement);
                    $this->handleSplitLayout($request, $announcement);
                }
                break;

            case 'layout_4':
                // article only
                break;

            case 'layout_5':
                if ($isUpdate) {
                    $this->syncMediaRows($request, $announcement);
                }
                $this->handleMediaUploads($request, $announcement);
                break;
        }

        // documents (global)
        if ($request->hasFile('documents')) {
            $this->handleDocumentUploads($request, $announcement);
        }
    }

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


    private function replaceCoverAssets(Announcement $announcement): void
    {
        $announcement->assets()->where('is_cover', true)->get()
            ->each(fn($a) => $this->deleteAssetPivot($a->pivot));
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



    private function syncMediaRows(Request $request, Announcement $announcement): void
    {
        $existingIds = $request->input('existing_media_ids', []);

        if ($existingIds) {
            $announcement->assets()
                ->whereNotIn('announcement_assets.id', $existingIds)
                ->get()
                ->each(fn($a) => $this->deleteAssetPivot($a->pivot));
        }

        $mediaData = [];

        foreach ($existingIds as $index => $id) {
            $data = $request->input("media.$index", []);
            $mediaData[] = [
                'id' => $id,
                'caption' => $data['caption'] ?? null,
                'sort_order' => $data['sort_order'] ?? $index,
            ];
        }

        usort($mediaData, fn($a, $b) => $a['sort_order'] <=> $b['sort_order']);

        foreach ($mediaData as $i => $data) {
            AnnouncementAsset::where('id', $data['id'])->update([
                'caption' => $data['caption'],
                'sort_order' => $data['sort_order'],
                'is_thumbnail' => $i === 0,
            ]);
        }
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




    // /* ==========================
    //  * ASSET SYNC (UPDATE)
    //  * ========================== */

    // private function syncAssets(Request $request, Announcement $announcement): void
    // {
    //     $existingIds = $request->input('existing_asset_ids', []);

    //     if ($existingIds) {
    //         $announcement->assets()
    //             ->whereNotIn('announcement_assets.id', $existingIds)
    //             ->get()
    //             ->each(fn($a) => $this->deleteAssetPivot($a->pivot));
    //     }

    //     foreach ($existingIds as $index => $id) {
    //         $data = $request->input("assets.$index", []);
    //         AnnouncementAsset::where('id', $id)->update([
    //             'caption' => $data['caption'] ?? null,
    //             'sort_order' => $data['sort_order'] ?? $index,
    //             'is_thumbnail' => $index === 0,
    //         ]);
    //     }
    // }

    // /* ==========================
    //  * FILE UPLOADS
    //  * ========================== */

    // private function handleUploads(Request $request, Announcement $announcement, bool $documentsOnly = false): void
    // {
    //     if (!$request->has('assets'))
    //         return;

    //     $assets = $request->assets;

    //     usort(
    //         $assets,
    //         fn($a, $b) =>
    //         ($a['sort_order'] ?? 0) <=> ($b['sort_order'] ?? 0)
    //     );

    //     foreach ($assets as $i => $data) {

    //         if (!isset($data['file']))
    //             continue;

    //         $asset = $this->storeFileAsAsset($data['file']);

    //         AnnouncementAsset::create([
    //             'announcement_id' => $announcement->id,
    //             'asset_id' => $asset->id,
    //             'caption' => $data['caption'] ?? null,
    //             'sort_order' => $data['sort_order'] ?? $i,
    //             'is_thumbnail' => $i === 0,
    //             'is_cover' => $i === 0 && !$documentsOnly,
    //         ]);
    //     }
    // }

    /* ==========================
     * DELETE HELPERS
     * ========================== */

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

    /* ==========================
     * UTILITIES
     * ========================== */

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

    private function transformForEdit(Announcement $announcement): array
    {
        $cover = $announcement->assets->firstWhere('pivot.is_cover', true);

        return array_merge($announcement->toArray(), [
            'hero_image' => $announcement->layout === 'layout_2'
                ? optional($cover)->storage_path
                : null,

            'hero_caption' => $announcement->layout === 'layout_2'
                ? optional($cover?->pivot)->caption
                : null,

            'split_left_image' => $announcement->layout === 'layout_3'
                ? optional($cover)->storage_path
                : null,

            'split_right_caption' => $announcement->layout === 'layout_3'
                ? optional($cover?->pivot)->caption
                : null,

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

}
