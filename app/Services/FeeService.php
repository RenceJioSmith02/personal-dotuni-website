<?php

namespace App\Services;

use App\Models\Fee;
use App\Models\Asset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class FeeService
{
    public function list()
    {
        return Fee::with('asset')->orderBy('sort_order')->get();
    }

    public function create(array $data, ?UploadedFile $image): Fee
    {
        return DB::transaction(function () use ($data, $image) {
            $assetId = $image ? $this->storeImage($image) : null;

            return Fee::create([
                'title' => $data['title'],
                'caption' => $data['caption'] ?? null,
                'sort_order' => $data['sort_order'] ?? 0,
                'asset_id' => $assetId,
                'updated_by' => Auth::id(),
            ]);
        });
    }

    public function update(Fee $fee, array $data, ?UploadedFile $image): Fee
    {
        return DB::transaction(function () use ($fee, $data, $image) {

            if ($image) {
                $this->replaceImage($fee, $image);
            }

            $fee->update([
                'title' => $data['title'],
                'caption' => $data['caption'] ?? null,
                'sort_order' => $data['sort_order'] ?? 0,
                'updated_by' => Auth::id(),
            ]);

            return $fee;
        });
    }

    public function delete(Fee $fee): void
    {
        DB::transaction(function () use ($fee) {
            if ($fee->asset) {
                Storage::disk('public')->delete($fee->asset->storage_path);
                $fee->asset->delete();
            }
            $fee->delete();
        });
    }

    protected function storeImage(UploadedFile $file): int
    {
        $path = $file->store('fees', 'public');

        return Asset::create([
            'kind' => 'image',
            'file_name' => $file->getClientOriginalName(),
            'storage_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size_kb' => round($file->getSize() / 1024),
            'uploaded_by' => Auth::id(),
        ])->id;
    }

    protected function replaceImage(Fee $fee, UploadedFile $file): void
    {
        if ($fee->asset) {
            Storage::disk('public')->delete($fee->asset->storage_path);
            $fee->asset->delete();
        }

        $fee->asset_id = $this->storeImage($file);
        $fee->save();
    }
}
