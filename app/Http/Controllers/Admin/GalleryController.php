<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use DomainException;
use Illuminate\Http\Request;
use App\Services\GalleryService;

class GalleryController extends Controller
{
    public function __construct(protected GalleryService $service)
    {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->service->datatable($request);
        }

        return view('admin.gallery.index');
    }

    public function toggleBanner(Gallery $gallery)
    {
        $gallery->update([
            'is_homepage_banner' => !$gallery->is_homepage_banner,
        ]);

        return response()->json([
            'success' => true,
            'is_homepage_banner' => $gallery->is_homepage_banner,
        ]);
    }

    public function store(Request $request)
    {
        $this->service->create($request);

        return response()->json(['message' => 'Gallery item created successfully']);
    }

    public function edit(Gallery $gallery)
    {
        return response()->json($gallery->load('asset'));
    }

    public function update(Request $request, Gallery $gallery)
    {
        $this->service->update($request, $gallery);

        return response()->json(['message' => 'Gallery item updated successfully']);
    }

    public function destroy(Gallery $gallery)
    {
        try {
            $this->service->delete($gallery);

            return response()->json(['message' => 'Gallery item deleted successfully']);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    // ✅ New
    public function archive(Gallery $gallery)
    {
        try {
            $gallery = $this->service->archive($gallery);

            return response()->json([
                'message' => 'Gallery item archived successfully',
                'gallery' => $gallery,
            ]);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    // ✅ New
    public function unarchive(Gallery $gallery)
    {
        try {
            $gallery = $this->service->unarchive($gallery);

            return response()->json([
                'message' => 'Gallery item unarchived successfully',
                'gallery' => $gallery,
            ]);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}