<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Linkage;
use App\Models\LinkageCategory;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LinkageController extends Controller
{
    public function index()
    {
        $linkages = Linkage::with(['category', 'logo'])
            ->orderBy('sort_order')
            ->get();

        $categories = LinkageCategory::orderBy('name')->get();

        return view('admin.linkages.index', compact('linkages', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:linkage_categories,id',
            'title' => 'required|string|max:150',
            'url' => 'required|url|max:1000',
            'description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
            'is_active' => 'required|boolean',
            'logo' => 'nullable|image|max:2048',
        ]);

        $assetId = null;

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $path = $file->store('linkages', 'public');

            $asset = Asset::create([
                'kind' => 'image',
                'file_name' => $file->getClientOriginalName(),
                'storage_path' => $path,
                'mime_type' => $file->getMimeType(),
                'file_size_kb' => round($file->getSize() / 1024),
                'uploaded_by' => Auth::id(),
            ]);

            $assetId = $asset->id;
        }

        Linkage::create([
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'url' => $validated['url'],
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'],
            'logo_asset_id' => $assetId,
        ]);

        return response()->json([
            'message' => 'Linkage created successfully',
        ]);
    }

    public function edit(Linkage $linkage)
    {
        return response()->json(
            $linkage->load(['category', 'logo'])
        );
    }

    public function update(Request $request, Linkage $linkage)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:linkage_categories,id',
            'title' => 'required|string|max:150',
            'url' => 'required|url|max:1000',
            'description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
            'is_active' => 'required|boolean',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {

            // Remove old logo
            if ($linkage->logo) {
                Storage::disk('public')->delete($linkage->logo->storage_path);
                $linkage->logo->delete();
            }

            $file = $request->file('logo');
            $path = $file->store('linkages', 'public');

            $asset = Asset::create([
                'kind' => 'image',
                'file_name' => $file->getClientOriginalName(),
                'storage_path' => $path,
                'mime_type' => $file->getMimeType(),
                'file_size_kb' => round($file->getSize() / 1024),
                'uploaded_by' => Auth::id(),
            ]);

            $linkage->logo_asset_id = $asset->id;
        }

        $linkage->update([
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'url' => $validated['url'],
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'],
        ]);

        return response()->json([
            'message' => 'Linkage updated successfully',
        ]);
    }

    public function destroy(Linkage $linkage)
    {
        if ($linkage->logo) {
            Storage::disk('public')->delete($linkage->logo->storage_path);
            $linkage->logo->delete();
        }

        $linkage->delete();

        return response()->json([
            'message' => 'Linkage deleted successfully',
        ]);
    }
}
