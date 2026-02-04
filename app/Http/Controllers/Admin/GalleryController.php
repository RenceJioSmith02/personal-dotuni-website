<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;
use App\Services\GalleryService;

class GalleryController extends Controller
{
    protected GalleryService $service;

    public function __construct(GalleryService $service)
    {
        $this->service = $service;
    }

    /**
     * List gallery items
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->service->datatable($request);
        }

        return view('admin.gallery.index');
    }

    /**
     * Store a new gallery item
     */
    public function store(Request $request)
    {
        $this->service->create($request);
        return response()->json(['message' => 'Gallery item created successfully']);
    }

    /**
     * Edit a gallery item
     */
    public function edit(Gallery $gallery)
    {
        return response()->json($gallery->load('asset'));
    }

    /**
     * Update a gallery item
     */
    public function update(Request $request, Gallery $gallery)
    {
        $this->service->update($request, $gallery);
        return response()->json(['message' => 'Gallery item updated successfully']);
    }

    /**
     * Delete a gallery item
     */
    public function destroy(Gallery $gallery)
    {
        $this->service->delete($gallery);
        return response()->json(['message' => 'Gallery item deleted successfully']);
    }
}

