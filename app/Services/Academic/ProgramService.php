<?php

namespace App\Services\Academic;

use DomainException;
use App\Models\Asset;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProgramService
{

    public function list()
    {
        $programs = Program::with('asset')->get()->map(function ($program) {

            $program->imagePath = optional($program->asset)->storage_path;

            return $program;
        });

        return $programs;
    }


    public function listPaginated($page = 1, $perPage = 8)
    {
        $query = Program::with('asset')
            ->where('is_active', true)
            ->orderByDesc('created_at');

        $paginated = $query->paginate(
            $perPage,
            ['*'],
            'page',
            $page
        );

        $paginated->getCollection()->transform(function ($program) {
            return [
                'id' => $program->id,
                'title' => $program->title,
                'image_url' => $program->image_url,
            ];
        });

        return $paginated;
    }


    // Server-side DataTables
    public function datatable(Request $request)
    {
        $query = Program::with('asset');

        $total = $query->count();

        /* ======================
         * SEARCH
         * ====================== */
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%");
            });
        }

        $filtered = $query->count();

        /* ======================
         * ORDERING
         * ====================== */
        $columns = ['title', 'description', 'type', 'total_units', 'is_active', 'created_at', 'updated_at', 'actions'];
        $orderColumn = $columns[$request->input('order.0.column', 1)] ?? 'title';
        $orderDir = $request->input('order.0.dir', 'asc');
        $query->orderBy($orderColumn, $orderDir);

        /* ======================
         * PAGINATION
         * ====================== */
        $data = $query->skip($request->start)
            ->take($request->length)
            ->get();

        /* ======================
         * RESPONSE
         * ====================== */
        return [
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data->map(fn($p) => [
                'title' => $p->title,
                'description' => $p->description,
                'type' => $p->type,
                'total_units' => $p->total_units,
                'status' => $p->is_active
                    ? '<span class="badge badge-success">Active</span>'
                    : '<span class="badge badge-danger">Inactive</span>',
                'created_at' => $p->created_at->toDateTimeString(),
                'updated_at' => $p->updated_at->toDateTimeString(),
                'actions' => view('admin.academic.programs.partials.actions', compact('p'))->render()
            ])
        ];
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
        try {
            DB::transaction(function () use ($program) {

                $courseCount = $program->programCourses()->count();
                $reqCount = $program->programRequirements()->count();

                if ($courseCount || $reqCount) {
                    throw new DomainException(
                        "Cannot delete '{$program->title}'. It is used in {$courseCount} course(s) and {$reqCount} requirement(s)."
                    );
                }

                if ($program->asset) {
                    Storage::disk('public')->delete($program->asset->storage_path);
                    $program->asset->delete();
                }

                $program->delete();
            });
        } catch (DomainException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);
            throw new DomainException('Failed to delete program.');
        }
    }


    protected function storeImage(UploadedFile $file): int
    {
        $extension = $file->getClientOriginalExtension();

        $filename = sprintf(
            'programs-%s-%s.%s',
            now()->format('Y-m-d'),
            substr(bin2hex(random_bytes(4)), 0, 8),
            $extension
        );

        $path = $file->storeAs('programs', $filename, 'public');

        return Asset::create([
            'kind' => 'image',
            'file_name' => $filename,
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
