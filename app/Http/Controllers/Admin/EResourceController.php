<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EResource;
use DomainException;
use Illuminate\Http\Request;
use App\Services\EResourceService;

class EResourceController extends Controller
{
    public function __construct(protected EResourceService $service)
    {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->service->datatable($request);
        }

        return view('admin.e_resources.index');
    }

    public function store(Request $request)
    {
        $this->service->create($request);

        return response()->json(['message' => 'E-Resource created successfully'], 201);
    }

    public function edit(EResource $eResource)
    {
        return response()->json($eResource);
    }

    public function update(Request $request, EResource $eResource)
    {
        $this->service->update($request, $eResource);

        return response()->json(['message' => 'E-Resource updated successfully']);
    }

    public function destroy(EResource $eResource)
    {
        try {
            $this->service->delete($eResource);

            return response()->json(['message' => 'E-Resource deleted successfully']);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    // ✅ New
    public function archive(EResource $eResource)
    {
        try {
            $resource = $this->service->archive($eResource);

            return response()->json([
                'message' => 'E-Resource archived successfully',
                'eResource' => $resource,
            ]);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    // ✅ New
    public function unarchive(EResource $eResource)
    {
        try {
            $resource = $this->service->unarchive($eResource);

            return response()->json([
                'message' => 'E-Resource unarchived successfully',
                'eResource' => $resource,
            ]);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}