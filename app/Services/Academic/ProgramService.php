<?php

namespace App\Services\Academic;

use App\Models\Program;
use App\Models\Asset;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use DomainException;

class ProgramService
{
    public function list()
    {
        return Program::with('asset')->get();
    }

    public function create(array $data, ?UploadedFile $image): Program
    {
        return DB::transaction(function () use ($data, $image) {
            try {
                $assetId = $image ? $this->storeImage($image) : null;

                return Program::create([
                    ...$data,
                    'program_asset_id' => $assetId,
                ]);
            } catch (\Throwable $e) {
                // Rollback any stored image if exists
                if (isset($assetId)) {
                    $this->deleteAssetById($assetId);
                }
                throw $e; // rethrow to be handled by caller
            }
        });
    }

    public function update(Program $program, array $data, ?UploadedFile $image): Program
    {
        return DB::transaction(function () use ($program, $data, $image) {
            try {
                if ($image) {
                    $this->replaceImage($program, $image);
                }

                $program->update($data);
                return $program;
            } catch (\Throwable $e) {
                throw $e; // transaction automatically rolls back
            }
        });
    }

    public function delete(Program $program): void
    {
        DB::transaction(function () use ($program) {
            try {
                if ($program->asset) {
                    Storage::disk('public')->delete($program->asset->storage_path);
                    $program->asset->delete();
                }

                $program->delete();
            } catch (\Throwable $e) {
                throw $e;
            }
        });
    }

    protected function storeImage(UploadedFile $file): int
    {
        $path = $file->store('programs', 'public');

        return Asset::create([
            'kind' => 'image',
            'file_name' => $file->getClientOriginalName(),
            'storage_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size_kb' => round($file->getSize() / 1024),
            'uploaded_by' => Auth::id(),
        ])->id;
    }

    protected function replaceImage(Program $program, UploadedFile $file): void
    {
        if ($program->asset) {
            Storage::disk('public')->delete($program->asset->storage_path);
            $program->asset->delete();
        }

        $program->program_asset_id = $this->storeImage($file);
        $program->save();
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
