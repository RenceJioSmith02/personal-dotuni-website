<?php

namespace App\Services;

use App\Models\EResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Throwable;

class EResourceService
{
    /**
     * List all resources
     */
    public function list()
    {
        return EResource::orderBy('sort_order')->get();
    }

    /**
     * Create a new E-Resource
     */
    public function create(Request $request): EResource
    {
        return DB::transaction(function () use ($request) {
            try {
                $validated = $this->validate($request);

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

    /**
     * Update an existing E-Resource
     */
    public function update(Request $request, EResource $resource): EResource
    {
        return DB::transaction(function () use ($request, $resource) {
            try {
                $validated = $this->validate($request);

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
     * Delete an E-Resource
     */
    public function delete(EResource $resource): void
    {
        DB::transaction(function () use ($resource) {
            $resource->delete();
        });
    }

    /**
     * Validate request
     */
    private function validate(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'link_url' => 'nullable|string|max:1000',
            'sort_order' => 'nullable|integer',
            'is_active' => 'required|boolean',
        ]);
    }
}
