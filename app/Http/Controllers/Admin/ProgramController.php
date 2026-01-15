<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::with('asset')->get();
        return view('admin.programs.index', compact('programs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:250',
            'description'  => 'required|string',
            'type'         => 'required|string|max:50',
            'total_units'  => 'required|numeric',
            'is_active'    => 'required|boolean',
            'image'        => 'nullable|image|max:2048',
        ]);

        $assetId = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $file->store('programs', 'public');

            $asset = Asset::create([
                'kind' => 'image',
                'file_name' => $file->getClientOriginalName(),
                'storage_path' => $path,
                'mime_type' => $file->getMimeType(),
                'file_size_kb' => round($file->getSize() / 1024),
                'uploaded_by' => Auth::id(),
            ]);

            $assetId = $asset->id;
        }

        Program::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'total_units' => $validated['total_units'],
            'is_active' => $validated['is_active'],
            'program_asset_id' => $assetId,
        ]);

        return response()->json(['message' => 'Program created successfully']);
    }

    public function edit(Program $program)
    {
        return response()->json($program->load('asset'));
    }

    public function update(Request $request, Program $program)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:250',
            'description'  => 'required|string',
            'type'         => 'required|string|max:50',
            'total_units'  => 'required|numeric',
            'is_active'    => 'required|boolean',
            'image'        => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($program->asset) {
                Storage::disk('public')->delete($program->asset->storage_path);
                $program->asset->delete();
            }

            $file = $request->file('image');
            $path = $file->store('programs', 'public');

            $asset = Asset::create([
                'kind' => 'image',
                'file_name' => $file->getClientOriginalName(),
                'storage_path' => $path,
                'mime_type' => $file->getMimeType(),
                'file_size_kb' => round($file->getSize() / 1024),
                'uploaded_by' => Auth::id(),
            ]);

            $program->program_asset_id = $asset->id;
        }

        $program->update($validated);

        return response()->json(['message' => 'Program updated successfully']);
    }

    public function destroy(Program $program)
    {
        if ($program->asset) {
            Storage::disk('public')->delete($program->asset->storage_path);
            $program->asset->delete();
        }

        $program->delete();

        return response()->json(['message' => 'Program deleted successfully']);
    }
}
