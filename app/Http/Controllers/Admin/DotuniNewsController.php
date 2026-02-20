<?php

namespace App\Http\Controllers\Admin;

use App\Models\DotuniNews;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\DotuniNewsService;

class DotuniNewsController extends Controller
{
    protected DotuniNewsService $service;

    public function __construct(DotuniNewsService $service)
    {
        $this->service = $service;
    }


    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->service->datatable($request);
        }

        return view('admin.dotuni_news.index');
    }

    public function datatable(Request $request)
    {
        return response()->json(
            $this->service->datatable($request)
        );
    }
    
    public function store(Request $request)
    {
        $news = $this->service->create($request);
        return response()->json(['message' => 'DotUni News created successfully'], 201);
    }

    public function edit(DotuniNews $dotuniNews)
    {
        $dotuniNews->load(['author', 'attachments.asset']);
        return response()->json($this->service->transformForEdit($dotuniNews));
    }

    public function update(Request $request, DotuniNews $dotuniNews)
    {
        $news = $this->service->update($request, $dotuniNews);
        return response()->json(['message' => 'DotUni News updated successfully']);
    }

    public function destroy(DotuniNews $dotuniNews)
    {
        $this->service->delete($dotuniNews);
        return response()->json(['message' => 'DotUni News deleted successfully']);
    }


    public function publish(DotuniNews $dotuniNews)
    {
        $this->service->publish($dotuniNews);

        return response()->json([
            'message' => 'News published successfully.'
        ]);
    }


}

