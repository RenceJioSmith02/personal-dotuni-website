<?php

namespace App\Services\Form;

use App\Models\Form;
use App\Models\Asset;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class FormService
{
    public function list()
    {
        return Form::with(['category', 'asset'])
            ->orderBy('sort_order')
            ->get();
    }


    public function datatable(Request $request)
    {
        $query = Form::with(['category', 'asset']);

        $total = $query->count();

        /* ======================
         * SEARCH
         * ====================== */
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($qc) use ($search) {
                        $qc->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('asset', function ($qa) use ($search) {
                        $qa->where('mime_type', 'like', "%{$search}%");
                    });
            });
        }

        $filtered = $query->count();

        /* ======================
         * ORDERING
         * ====================== */
        $columns = ['file', 'name', 'category', 'type', 'status', 'actions'];
        $orderColumnIndex = $request->input('order.0.column', 1);
        $orderColumn = $columns[$orderColumnIndex] ?? 'name';
        $orderDir = $request->input('order.0.dir', 'asc');

        $orderDir = $orderDir === 'asc' ? 'asc' : 'desc';

        if ($orderColumn === 'name') {
            $query->orderBy('name', $orderDir);
        }

        /* ======================
         * PAGINATION
         * ====================== */
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        $data = $query->offset($start)->limit($length)->get();

        /* ======================
         * RESPONSE
         * ====================== */
        return [
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data->map(function ($form) {

                return [
                    'file' => $form->file_url
                        ? '<a href="' . $form->file_url . '" target="_blank">
                           <i class="fas fa-file-alt"></i>
                       </a>'
                        : '<span class="text-muted">—</span>',

                    'name' => $form->name,

                    'category' => $form->category->name ?? '—',

                    'type' => strtoupper($form->asset->mime_type ?? '—'),

                    'status' => $form->is_active
                        ? '<span class="badge badge-success">Active</span>'
                        : '<span class="badge badge-danger">Inactive</span>',

                    'actions' => view(
                        'admin.form.forms.partials.actions',
                        compact('form')
                    )->render(),
                ];
            }),
        ];
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
