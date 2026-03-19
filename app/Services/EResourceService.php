<?php

namespace App\Services;

use Throwable;
use DomainException;
use App\Models\EResource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EResourceService
{
    public function websiteList()
    {
        return EResource::where('is_active', true)
            ->whereNull('deleted_at') // ✅ Exclude archived
            ->orderBy('sort_order')
            ->get();
    }

    public function datatable(Request $request)
    {
        // ✅ Show ALL records including archived
        $query = EResource::query();

        $total = $query->count();

        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('link_url', 'like', "%{$search}%");
            });
        }

        $filtered = $query->count();

        $columns = ['name', 'description', 'link_url', 'sort_order', 'status', 'created_at', 'updated_at', 'actions'];
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderColumn = $columns[$orderColumnIndex] ?? 'sort_order';
        $orderDir = $request->input('order.0.dir', 'asc') === 'asc' ? 'asc' : 'desc';
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        if (!in_array($orderColumn, ['actions'])) {
            $query->orderBy($orderColumn, $orderDir);
        }

        $data = $query->offset($start)->limit($length)->get();

        return [
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data->map(function ($item) {
                return [
                    'name' => $item->name,
                    'description' => \Str::limit($item->description, 80),
                    'link_url' => $item->link_url
                        ? '<a href="' . $item->link_url . '" target="_blank">'. $item->link_url .'</a>'
                        : '<span class="text-muted">—</span>',
                    'sort_order' => $item->sort_order,
                    'status' => $item->is_active
                        ? '<span class="badge badge-success">Active</span>'
                        : '<span class="badge badge-danger">Inactive</span>',
                    'created_at' => $item->created_at->toDateTimeString(),
                    'updated_at' => $item->updated_at->toDateTimeString(),
                    'archived' => !is_null($item->deleted_at), // ✅ Pass archive state
                    'actions' => view('admin.e_resources.partials.actions', compact('item'))->render(),
                ];
            }),
        ];
    }

    public function create(Request $request): EResource
    {
        return DB::transaction(function () use ($request) {
            try {
                $validated = $this->validate(request: $request);

                return EResource::create([
                    'name' => $validated['name'],
                    'description' => $validated['description'] ?? null,
                    'link_url' => $validated['link_url'] ?? null,
                    'sort_order' => $validated['sort_order'] ?? 0,
                    'is_active' => $validated['is_active'],
                    'updated_by' => Auth::id(),
                ]);
            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    public function update(Request $request, EResource $resource): EResource
    {
        return DB::transaction(function () use ($request, $resource) {
            try {
                $validated = $this->validate($request, $resource);

                $resource->update([
                    'name' => $validated['name'],
                    'description' => $validated['description'] ?? null,
                    'link_url' => $validated['link_url'] ?? null,
                    'sort_order' => $validated['sort_order'] ?? 0,
                    'is_active' => $validated['is_active'],
                    'updated_by' => Auth::id(),
                ]);

                return $resource;
            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    /**
     * ✅ Hard delete — must be inactive first
     */
    public function delete(EResource $resource): void
    {
        try {
            DB::transaction(function () use ($resource) {

                // ✅ Guard: must be inactive before hard deleting
                if ($resource->is_active) {
                    throw new DomainException(
                        "Cannot delete '{$resource->name}'. Please deactivate it before deleting."
                    );
                }

                $resource->delete();
            });
        } catch (DomainException $e) {
            throw $e;
        } catch (Throwable $e) {
            report($e);
            throw new DomainException('Failed to delete E-Resource.');
        }
    }

    /**
     * ✅ Archive — sets is_active = false + deleted_at = now()
     */
    public function archive(EResource $resource): EResource
    {
        try {
            return DB::transaction(function () use ($resource) {
                $resource->update([
                    'is_active' => false,
                    'deleted_at' => now(),
                ]);
                return $resource;
            });
        } catch (Throwable $e) {
            report($e);
            throw new DomainException('Failed to archive E-Resource.');
        }
    }

    /**
     * ✅ Unarchive — sets is_active = true + deleted_at = null
     */
    public function unarchive(EResource $resource): EResource
    {
        try {
            return DB::transaction(function () use ($resource) {
                $resource->update([
                    'is_active' => true,
                    'deleted_at' => null,
                ]);
                return $resource;
            });
        } catch (Throwable $e) {
            report($e);
            throw new DomainException('Failed to unarchive E-Resource.');
        }
    }

    private function validate(Request $request, ?EResource $resource = null): array
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('e_resources', 'name')
                    ->ignore($resource?->id)
                    ->whereNull('deleted_at'),
            ],
            'description' => 'nullable|string|max:500',
            'link_url' => [
                'nullable',
                'string',
                'max:1000',
                Rule::unique('e_resources', 'link_url')
                    ->ignore($resource?->id)
                    ->whereNull('deleted_at'),
            ],
            'sort_order' => 'nullable|integer',
            'is_active' => 'required|boolean',
        ]);
    }
}