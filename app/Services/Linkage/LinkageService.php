<?php

namespace App\Services\Linkage;

use App\Models\Linkage;
use App\Models\Asset;
use DomainException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class LinkageService
{
    public function list()
    {
        return Linkage::with(['category', 'logo'])
            ->whereNull('deleted_at') // ✅ Exclude archived
            ->orderBy('sort_order')
            ->get();
    }

    public function datatable(Request $request)
    {
        // ✅ Show ALL records including archived
        $query = Linkage::with(['category', 'logo']);

        $total = $query->count();

        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('url', 'like', "%{$search}%")
                    ->orWhereHas('category', fn($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        $filtered = $query->count();

        $columns = ['id', 'title', 'category_id', 'url', 'status', 'created_at', 'updated_at', 'actions'];
        $orderColumn = $columns[$request->input('order.0.column', 0)] ?? 'id';
        $orderDir = $request->input('order.0.dir', 'asc') === 'asc' ? 'asc' : 'desc';

        if (!in_array($orderColumn, ['status', 'actions'])) {
            $query->orderBy($orderColumn, $orderDir);
        }

        $data = $query
            ->skip($request->start)
            ->take($request->length)
            ->get();

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data->map(fn($l) => [
                'title' => $l->title,
                'category' => $l->category->name ?? '—',
                'url' => $l->url
                    ? '<a href="' . $l->url . '" target="_blank">' . $l->url . '</a>'
                    : '<span class="text-muted">—</span>',
                'status' => $l->is_active
                    ? '<span class="badge badge-success">Active</span>'
                    : '<span class="badge badge-danger">Inactive</span>',
                'created_at' => $l->created_at->toDateTimeString(),
                'updated_at' => $l->updated_at->toDateTimeString(),
                'archived' => !is_null($l->deleted_at), // ✅ Pass archive state
                'actions' => view('admin.linkage.linkages.partials.actions', compact('l'))->render(),
            ])
        ]);
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

    /**
     * ✅ Hard delete — must be inactive first
     */
    public function delete(Linkage $linkage): void
    {
        try {
            DB::transaction(function () use ($linkage) {

                // ✅ Guard: must be inactive before hard deleting
                if ($linkage->is_active) {
                    throw new DomainException(
                        "Cannot delete '{$linkage->title}'. Please deactivate it before deleting."
                    );
                }

                if ($linkage->logo) {
                    Storage::disk('public')->delete($linkage->logo->storage_path);
                    $linkage->logo->delete();
                }

                $linkage->delete();
            });
        } catch (DomainException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);
            throw new DomainException('Failed to delete linkage.');
        }
    }

    /**
     * ✅ Archive — sets is_active = false + deleted_at = now()
     */
    public function archive(Linkage $linkage): Linkage
    {
        try {
            DB::table('linkages')->where('id', $linkage->id)->update([
                'is_active' => false,
                'deleted_at' => now(),
            ]);

            return $linkage->fresh();
        } catch (\Throwable $e) {
            report($e);
            throw new DomainException('Failed to archive linkage.');
        }
    }

    /**
     * ✅ Unarchive — sets is_active = true + deleted_at = null
     */
    public function unarchive(Linkage $linkage): Linkage
    {
        try {
            DB::table('linkages')->where('id', $linkage->id)->update([
                'is_active' => true,
                'deleted_at' => null,
            ]);

            return $linkage->fresh();
        } catch (\Throwable $e) {
            report($e);
            throw new DomainException('Failed to unarchive linkage.');
        }
    }

    protected function storeLogo(UploadedFile $file): int
    {
        $extension = $file->getClientOriginalExtension();

        $filename = sprintf(
            'linkages-%s-%s.%s',
            now()->format('Y-m-d'),
            substr(bin2hex(random_bytes(4)), 0, 8),
            $extension
        );

        $path = $file->storeAs('linkages', $filename, 'public');

        return Asset::create([
            'kind' => 'image',
            'file_name' => $filename,
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