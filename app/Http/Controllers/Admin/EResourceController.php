<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EResourceController extends Controller
{
    public function index()
    {
        $resources = EResource::orderBy('sort_order')->get();
        return view('admin.e_resources.index', compact('resources'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'link_url' => 'nullable|string|max:1000',
            'sort_order' => 'nullable|integer',
            'is_active' => 'required|boolean',
        ]);

        EResource::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'link_url' => $validated['link_url'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'],
            'updated_by' => Auth::id(),
        ]);

        return response()->json(['message' => 'E-Resource created successfully'], 201);
    }

    public function edit(EResource $eResource)
    {
        return response()->json($eResource);
    }

    public function update(Request $request, EResource $eResource)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'link_url' => 'nullable|string|max:1000',
            'sort_order' => 'nullable|integer',
            'is_active' => 'required|boolean',
        ]);

        $eResource->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'link_url' => $validated['link_url'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'],
            'updated_by' => Auth::id(),
        ]);

        return response()->json(['message' => 'E-Resource updated successfully']);
    }

    public function destroy(EResource $eResource)
    {
        $eResource->delete();
        return response()->json(['message' => 'E-Resource deleted successfully']);
    }
}
