<?php

namespace App\Services\Form;

use App\Models\Form;
use App\Models\Asset;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FormService
{
    public function list()
    {
        return Form::with(['category', 'asset'])
            ->orderBy('sort_order')
            ->get();
    }

    public function create(array $data, UploadedFile $file): Form
    {
        return DB::transaction(function () use ($data, $file) {
            $assetId = $this->storeFile($file, $data['name']);

            return Form::create([
                'form_category_id' => $data['form_category_id'],
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'description' => $data['description'] ?? null,
                'asset_id' => $assetId,
                'sort_order' => $data['sort_order'] ?? 0,
                'is_active' => $data['is_active'],
                'updated_by' => Auth::id(),
            ]);
        });
    }

    public function update(Form $form, array $data, ?UploadedFile $file): Form
    {
        return DB::transaction(function () use ($form, $data, $file) {
            if ($file) {
                $this->replaceFile($form, $file, $data['name']);
            }

            $form->update([
                'form_category_id' => $data['form_category_id'],
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'description' => $data['description'] ?? null,
                'sort_order' => $data['sort_order'] ?? 0,
                'is_active' => $data['is_active'],
                'updated_by' => Auth::id(),
            ]);

            return $form;
        });
    }

    public function delete(Form $form): void
    {
        DB::transaction(function () use ($form) {
            if ($form->asset) {
                Storage::disk('public')->delete($form->asset->storage_path);
                $form->asset->delete();
            }

            $form->delete();
        });
    }

    protected function storeFile(UploadedFile $file, string $name): int
    {
        $filename = Str::slug($name) . '-' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('forms', $filename, 'public');

        return Asset::create([
            'kind' => 'document',
            'file_name' => $file->getClientOriginalName(),
            'storage_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size_kb' => round($file->getSize() / 1024),
            'uploaded_by' => Auth::id(),
        ])->id;
    }

    protected function replaceFile(Form $form, UploadedFile $file, string $name): void
    {
        if ($form->asset) {
            Storage::disk('public')->delete($form->asset->storage_path);
            $form->asset->delete();
        }

        $form->asset_id = $this->storeFile($file, $name);
        $form->save();
    }
}
