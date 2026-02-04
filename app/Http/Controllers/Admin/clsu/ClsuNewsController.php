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

