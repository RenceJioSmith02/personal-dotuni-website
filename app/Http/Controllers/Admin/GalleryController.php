<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index()
    {
        $items = Gallery::with('asset')->orderBy('sort_order')->get();
        return view('admin.gallery.index', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sort_order' => 'nullable|integer',
            'image' => 'required|image|max:2048',
        ]);

        // Upload image
        $file = $request->file('image');
        $path = $file->store('gallery', 'public');

        $asset = Asset::create([
            'kind' => 'image',
            'file_name' => $file->getClientOriginalName(),
            'storage_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size_kb' => round($file->getSize() / 1024),
            'alt_text' => null,
            'uploaded_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        Gallery::create([
            'asset_id' => $asset->id,
            'sort_order' => $validated['sort_order'] ?? 0,
            'updated_by' => Auth::id(),
        ]);

        return response()->json(['message' => 'Gallery item created successfully']);
    }

    public function edit(Gallery $gallery)
    {
        return response()->json(
            $gallery->load('asset')
        );
    }

    public function update(Request $request, Gallery $gallery)
    {
        $validated = $request->validate([
            'sort_order' => 'nullable|integer',
            'image' => 'nullable|image|max:2048',
        ]);

        // Replace image if uploaded
        if ($request->hasFile('image')) {
            if ($gallery->asset) {
                Storage::disk('public')->delete($gallery->asset->storage_path);
                $gallery->asset->delete();
            }

            $file = $request->file('image');
            $path = $file->store('gallery', 'public');

            $asset = Asset::create([
                'kind' => 'image',
                'file_name' => $file->getClientOriginalName(),
                'storage_path' => $path,
                'mime_type' => $file->getMimeType(),
                'file_size_kb' => round($file->getSize() / 1024),
                'alt_text' => null,
                'uploaded_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $gallery->asset_id = $asset->id;
        }

        $gallery->update([
            'sort_order' => $validated['sort_order'] ?? 0,
            'updated_by' => Auth::id(),
        ]);

        return response()->json(['message' => 'Gallery item updated successfully']);
    }

    public function destroy(Gallery $gallery)
    {
        if ($gallery->asset) {
            Storage::disk('public')->delete($gallery->asset->storage_path);
            $gallery->asset->delete();
        }

        $gallery->delete();

        return response()->json(['message' => 'Gallery item deleted successfully']);
    }
}
