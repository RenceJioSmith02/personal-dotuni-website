<?php

namespace App\Services\Linkage;

use App\Models\Linkage;
use App\Models\Asset;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LinkageService
{
    public function list()
    {
        return Linkage::with(['category', 'logo'])
            ->orderBy('sort_order')
            ->get();
    }

    public function create(array $data, ?UploadedFile $logo): Linkage
    {
        return DB::transaction(function () use ($data, $logo) {
            $assetId = $logo ? $this->storeLogo($logo) : null;

            return Linkage::create([
                'category_id' => $data['category_id'],
                'title' => $data['title'],
                'url' => $data['url'],
                'description' => $data['description'] ?? null,
                'sort_order' => $data['sort_order'] ?? 0,
                'is_active' => $data['is_active'],
                'logo_asset_id' => $assetId,
            ]);
        });
    }

    public function update(Linkage $linkage, array $data, ?UploadedFile $logo): Linkage
    {
        return DB::transaction(function () use ($linkage, $data, $logo) {
            if ($logo) {
                $this->replaceLogo($linkage, $logo);
            }

            $linkage->update([
                'category_id' => $data['category_id'],
                'title' => $data['title'],
                'url' => $data['url'],
                'description' => $data['description'] ?? null,
                'sort_order' => $data['sort_order'] ?? 0,
                'is_active' => $data['is_active'],
            ]);

            return $linkage;
        });
    }

    public function delete(Linkage $linkage): void
    {
        DB::transaction(function () use ($linkage) {
            if ($linkage->logo) {
                Storage::disk('public')->delete($linkage->logo->storage_path);
                $linkage->logo->delete();
            }

            $linkage->delete();
        });
    }

    protected function storeLogo(UploadedFile $file): int
    {
        $path = $file->store('linkages', 'public');

        return Asset::create([
            'kind' => 'image',
            'file_name' => $file->getClientOriginalName(),
            'storage_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size_kb' => round($file->getSize() / 1024),
            'uploaded_by' => Auth::id(),
        ])->id;
    }

    protected function replaceLogo(Linkage $linkage, UploadedFile $file): void
    {
        if ($linkage->logo) {
            Storage::disk('public')->delete($linkage->logo->storage_path);
            $linkage->logo->delete();
        }

        $linkage->logo_asset_id = $this->storeLogo($file);
        $linkage->save();
    }
}
