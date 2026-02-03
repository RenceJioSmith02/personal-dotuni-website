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


// namespace App\Http\Controllers\Admin;

// use App\Http\Controllers\Controller;
// use App\Models\EResource;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;

// class EResourceController extends Controller
// {
//     public function index()
//     {
//         $resources = EResource::orderBy('sort_order')->get();
//         return view('admin.e_resources.index', compact('resources'));
//     }

//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'name' => 'required|string|max:255',
//             'description' => 'nullable|string|max:500',
//             'link_url' => 'nullable|string|max:1000',
//             'sort_order' => 'nullable|integer',
//             'is_active' => 'required|boolean',
//         ]);

//         EResource::create([
//             'name' => $validated['name'],
//             'description' => $validated['description'] ?? null,
//             'link_url' => $validated['link_url'] ?? null,
//             'sort_order' => $validated['sort_order'] ?? 0,
//             'is_active' => $validated['is_active'],
//             'updated_by' => Auth::id(),
//         ]);

//         return response()->json(['message' => 'E-Resource created successfully'], 201);
//     }

//     public function edit(EResource $eResource)
//     {
//         return response()->json($eResource);
//     }

//     public function update(Request $request, EResource $eResource)
//     {
//         $validated = $request->validate([
//             'name' => 'required|string|max:255',
//             'description' => 'nullable|string|max:500',
//             'link_url' => 'nullable|string|max:1000',
//             'sort_order' => 'nullable|integer',
//             'is_active' => 'required|boolean',
//         ]);

//         $eResource->update([
//             'name' => $validated['name'],
//             'description' => $validated['description'] ?? null,
//             'link_url' => $validated['link_url'] ?? null,
//             'sort_order' => $validated['sort_order'] ?? 0,
//             'is_active' => $validated['is_active'],
//             'updated_by' => Auth::id(),
//         ]);

//         return response()->json(['message' => 'E-Resource updated successfully']);
//     }

//     public function destroy(EResource $eResource)
//     {
//         $eResource->delete();
//         return response()->json(['message' => 'E-Resource deleted successfully']);
//     }
// }
