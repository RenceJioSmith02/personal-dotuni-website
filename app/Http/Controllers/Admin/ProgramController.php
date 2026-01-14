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

    public function create()
    {
        return view('admin.programs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:250',
            'description' => 'required|string',
            'type' => 'required|string|max:20',
            'total_units' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $assetId = null;

        // Upload image
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
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'total_units' => $request->total_units,
            'is_active' => 1,
            'program_asset_id' => $assetId,
        ]);

        return redirect()
            ->route('admin.programs.index')
            ->with('success', 'Program created successfully');
    }

    public function edit(Program $program)
    {
        $program->load('asset');
        return view('admin.programs.edit', compact('program'));
    }

    public function update(Request $request, Program $program)
    {
        $request->validate([
            'title' => 'required|string|max:250',
            'description' => 'required|string',
            'type' => 'required|string|max:20',
            'total_units' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // ✅ IMAGE REPLACEMENT LOGIC
        if ($request->hasFile('image')) {

            // Delete old image + asset
            if ($program->asset) {
                Storage::disk('public')->delete($program->asset->storage_path);
                $program->asset->delete();
            }

            // Save new image
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

        // Update program fields
        $program->update([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'total_units' => $request->total_units,
            'is_active' => $request->is_active ?? $program->is_active,
        ]);

        return redirect()
            ->route('admin.programs.index')
            ->with('success', 'Program updated successfully');
    }

    public function destroy(Program $program)
    {
        // ✅ AUTO DELETE ASSET + FILE
        if ($program->asset) {
            Storage::disk('public')->delete($program->asset->storage_path);
            $program->asset->delete();
        }

        $program->delete();

        return back()->with('success', 'Program deleted successfully');
    }
}
