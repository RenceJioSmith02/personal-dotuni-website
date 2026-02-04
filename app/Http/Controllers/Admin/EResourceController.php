<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EResource;
use Illuminate\Http\Request;
use App\Services\EResourceService;

class EResourceController extends Controller
{
    protected EResourceService $service;

    public function __construct(EResourceService $service)
    {
        $this->service = $service;
    }

    /**
     * List all E-Resources
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->service->datatable($request);
        }

        return view('admin.e_resources.index');
    }

    /**
     * Store a new E-Resource
     */
    public function store(Request $request)
    {
        $this->service->create($request);
        return response()->json(['message' => 'E-Resource created successfully'], 201);
    }

    /**
     * Edit an E-Resource
     */
    public function edit(EResource $eResource)
    {
        return response()->json($eResource);
    }

    /**
     * Update an existing E-Resource
     */
    public function update(Request $request, EResource $eResource)
    {
        $this->service->update($request, $eResource);
        return response()->json(['message' => 'E-Resource updated successfully']);
    }

    /**
     * Delete an E-Resource
     */
    public function destroy(EResource $eResource)
    {
        $this->service->delete($eResource);
        return response()->json(['message' => 'E-Resource deleted successfully']);
    }
}

