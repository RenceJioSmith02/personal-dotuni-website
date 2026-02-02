<?php

namespace App\Services\Clsu;

use App\Models\ClsuNews;
use App\Models\Asset;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use DomainException;

class ClsuNewsService
{
    public function list()
    {
        return ClsuNews::with('thumbnail')->orderBy('sort_order')->get();
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
        $path = $file->store('news', 'public');

        return Asset::create([
            'kind' => 'image',
            'file_name' => $file->getClientOriginalName(),
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
