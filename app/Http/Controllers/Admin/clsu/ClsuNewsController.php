<?php

namespace App\Http\Controllers\Admin\clsu;

use App\Http\Controllers\Controller;
use App\Models\ClsuNews;
use Illuminate\Http\Request;
use App\Services\Clsu\ClsuNewsService;

class ClsuNewsController extends Controller
{
    public function __construct(protected ClsuNewsService $service)
    {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return response()->json($this->service->datatable($request));
        }

        return view('admin.clsu.news.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'url' => 'nullable|string|max:1000',
            'description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
            'is_active' => 'required|boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        $news = $this->service->create($validated, $request->file('image'));

        return response()->json(['message' => 'News created successfully', 'news' => $news]);
    }

    public function edit(ClsuNews $clsuNews)
    {
        return response()->json($clsuNews->load('thumbnail'));
    }

    public function update(Request $request, ClsuNews $clsuNews)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'url' => 'nullable|string|max:1000',
            'description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
            'is_active' => 'required|boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        $news = $this->service->update($clsuNews, $validated, $request->file('image'));

        return response()->json(['message' => 'News updated successfully', 'news' => $news]);
    }

    public function destroy(ClsuNews $clsuNews)
    {
        $this->service->delete($clsuNews);

        return response()->json(['message' => 'News deleted successfully']);
    }
}


// namespace App\Http\Controllers\Admin\clsu;

// use App\Http\Controllers\Controller;
// use App\Models\ClsuNews;
// use App\Models\Asset;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Storage;

// class ClsuNewsController extends Controller
// {
//     public function index()
//     {
//         $news = ClsuNews::with('thumbnail')->orderBy('sort_order')->get();
//         return view('admin.clsu.news.index', compact('news'));
//     }

//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'title' => 'required|string|max:150',
//             'url' => 'nullable|string|max:1000',
//             'description' => 'nullable|string|max:500',
//             'sort_order' => 'nullable|integer',
//             'is_active' => 'required|boolean',
//             'image' => 'nullable|image|max:2048',
//         ]);

//         $assetId = null;

//         if ($request->hasFile('image')) {
//             $file = $request->file('image');
//             $path = $file->store('news', 'public');

//             $asset = Asset::create([
//                 'kind' => 'image',
//                 'file_name' => $file->getClientOriginalName(),
//                 'storage_path' => $path,
//                 'mime_type' => $file->getMimeType(),
//                 'file_size_kb' => round($file->getSize() / 1024),
//                 'uploaded_by' => Auth::id(),
//             ]);

//             $assetId = $asset->id;
//         }

//         ClsuNews::create([
//             'title' => $validated['title'],
//             'url' => $validated['url'] ?? null,
//             'description' => $validated['description'] ?? null,
//             'sort_order' => $validated['sort_order'] ?? 0,
//             'is_active' => $validated['is_active'],
//             'thumbnail_asset_id' => $assetId,
//             'updated_by' => Auth::id(),
//         ]);

//         return response()->json(['message' => 'News created successfully']);
//     }

//     public function edit(ClsuNews $clsuNews)
//     {
//         return response()->json(
//             $clsuNews->load('thumbnail')
//         );
//     }

//     public function update(Request $request, ClsuNews $clsuNews)
//     {
//         $validated = $request->validate([
//             'title' => 'required|string|max:150',
//             'url' => 'nullable|string|max:1000',
//             'description' => 'nullable|string|max:500',
//             'sort_order' => 'nullable|integer',
//             'is_active' => 'required|boolean',
//             'image' => 'nullable|image|max:2048',
//         ]);

//         if ($request->hasFile('image')) {
//             if ($clsuNews->thumbnail) {
//                 Storage::disk('public')->delete($clsuNews->thumbnail->storage_path);
//                 $clsuNews->thumbnail->delete();
//             }

//             $file = $request->file('image');
//             $path = $file->store('news', 'public');

//             $asset = Asset::create([
//                 'kind' => 'image',
//                 'file_name' => $file->getClientOriginalName(),
//                 'storage_path' => $path,
//                 'mime_type' => $file->getMimeType(),
//                 'file_size_kb' => round($file->getSize() / 1024),
//                 'uploaded_by' => Auth::id(),
//             ]);

//             $clsuNews->thumbnail_asset_id = $asset->id;
//         }

//         $clsuNews->update([
//             'title' => $validated['title'],
//             'url' => $validated['url'] ?? null,
//             'description' => $validated['description'] ?? null,
//             'sort_order' => $validated['sort_order'] ?? 0,
//             'is_active' => $validated['is_active'],
//             'updated_by' => Auth::id(),
//         ]);

//         return response()->json(['message' => 'News updated successfully']);
//     }

//     public function destroy(ClsuNews $clsuNews)
//     {
//         if ($clsuNews->thumbnail) {
//             Storage::disk('public')->delete($clsuNews->thumbnail->storage_path);
//             $clsuNews->thumbnail->delete();
//         }

//         $clsuNews->delete();

//         return response()->json(['message' => 'News deleted successfully']);
//     }
// }
