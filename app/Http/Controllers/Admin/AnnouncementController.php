<?php

namespace App\Http\Controllers\Admin;

use App\Models\Announcement;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AnnouncementService;

class AnnouncementController extends Controller
{
    protected AnnouncementService $service;

    public function __construct(AnnouncementService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->service->datatable($request);
        }

        return view('admin.clsu.announcement.index');
    }

    public function store(Request $request)
    {
        $announcement = $this->service->create($request);
        return response()->json(['message' => 'Announcement created successfully'], 201);
    }

    public function edit(Announcement $announcement)
    {
        $announcement->load(['author', 'assets']);
        return response()->json($this->service->transformForEdit($announcement));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $announcement = $this->service->update($request, $announcement);
        return response()->json(['message' => 'Announcement updated successfully']);
    }

    public function destroy(Announcement $announcement)
    {
        try {
            $this->service->delete($announcement);

            return response()->json(['message' => 'Announcement deleted successfully']);
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
    
}

