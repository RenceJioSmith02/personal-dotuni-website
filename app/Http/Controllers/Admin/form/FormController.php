<?php

namespace App\Http\Controllers\Admin\Form;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormCategory;
use App\Services\Form\FormService;
use Illuminate\Http\Request;

class FormController extends Controller
{
    public function __construct(protected FormService $service)
    {
    }

    public function index()
    {
        $forms = $this->service->list();
        $categories = FormCategory::orderBy('name')->get();

        return view('admin.form.forms.index', compact('forms', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'form_category_id' => 'required|exists:form_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
            'is_active' => 'required|boolean',
            'file' => 'required|file|max:10240',
        ]);

        $this->service->create($validated, $request->file('file'));

        return response()->json(['message' => 'Form created successfully'], 201);
    }

    public function edit(Form $form)
    {
        return response()->json($form->load(['category', 'asset']));
    }

    public function update(Request $request, Form $form)
    {
        $validated = $request->validate([
            'form_category_id' => 'required|exists:form_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
            'is_active' => 'required|boolean',
            'file' => 'nullable|file|max:10240',
        ]);

        $this->service->update($form, $validated, $request->file('file'));

        return response()->json(['message' => 'Form updated successfully']);
    }

    public function destroy(Form $form)
    {
        $this->service->delete($form);

        return response()->json(['message' => 'Form deleted successfully']);
    }
}


// namespace App\Http\Controllers\Admin\Form;

// use App\Http\Controllers\Controller;
// use App\Models\Form;
// use App\Models\FormCategory;
// use App\Models\Asset;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Str;

// class FormController extends Controller
// {
//     public function index()
//     {
//         $forms = Form::with(['category', 'asset'])
//             ->orderBy('sort_order')
//             ->get();

//         $categories = FormCategory::orderBy('name')->get();

//         return view('admin.form.forms.index', compact('forms', 'categories'));
//     }

//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'form_category_id' => 'required|exists:form_categories,id',
//             'name' => 'required|string|max:255',
//             'description' => 'nullable|string|max:500',
//             'sort_order' => 'nullable|integer',
//             'is_active' => 'required|boolean',
//             'file' => 'required|file|max:10240', // 10MB
//         ]);

//         // Upload file with custom filename
//         $file = $request->file('file');
//         $filename = Str::slug($validated['name']) . '-' . Str::random(8) . '.' . $file->getClientOriginalExtension();
//         $path = $file->storeAs('forms', $filename, 'public');

//         $asset = Asset::create([
//             'kind' => 'document',
//             'file_name' => $file->getClientOriginalName(),
//             'storage_path' => $path,
//             'mime_type' => $file->getMimeType(),
//             'file_size_kb' => round($file->getSize() / 1024),
//             'uploaded_by' => Auth::id(),
//         ]);

//         Form::create([
//             'form_category_id' => $validated['form_category_id'],
//             'name' => $validated['name'],
//             'slug' => Str::slug($validated['name']),
//             'description' => $validated['description'] ?? null,
//             'asset_id' => $asset->id,
//             'sort_order' => $validated['sort_order'] ?? 0,
//             'is_active' => $validated['is_active'],
//             'updated_by' => Auth::id(),
//         ]);

//         return response()->json([
//             'message' => 'Form created successfully',
//         ], 201);
//     }

//     public function edit(Form $form)
//     {
//         return response()->json(
//             $form->load(['category', 'asset'])
//         );
//     }

//     public function update(Request $request, Form $form)
//     {
//         $validated = $request->validate([
//             'form_category_id' => 'required|exists:form_categories,id',
//             'name' => 'required|string|max:255',
//             'description' => 'nullable|string|max:500',
//             'sort_order' => 'nullable|integer',
//             'is_active' => 'required|boolean',
//             'file' => 'nullable|file|max:10240',
//         ]);

//         // Replace file if uploaded
//         if ($request->hasFile('file')) {

//             if ($form->asset) {
//                 Storage::disk('public')->delete($form->asset->storage_path);
//                 $form->asset->delete();
//             }

//             $file = $request->file('file');
//             $filename = Str::slug($validated['name']) . '-' . Str::random(8) . '.' . $file->getClientOriginalExtension();
//             $path = $file->storeAs('forms', $filename, 'public');

//             $asset = Asset::create([
//                 'kind' => 'document',
//                 'file_name' => $file->getClientOriginalName(),
//                 'storage_path' => $path,
//                 'mime_type' => $file->getMimeType(),
//                 'file_size_kb' => round($file->getSize() / 1024),
//                 'uploaded_by' => Auth::id(),
//             ]);

//             $form->asset_id = $asset->id;
//         }

//         $form->update([
//             'form_category_id' => $validated['form_category_id'],
//             'name' => $validated['name'],
//             'slug' => Str::slug($validated['name']),
//             'description' => $validated['description'] ?? null,
//             'sort_order' => $validated['sort_order'] ?? 0,
//             'is_active' => $validated['is_active'],
//             'updated_by' => Auth::id(),
//         ]);

//         return response()->json([
//             'message' => 'Form updated successfully',
//         ]);
//     }

//     public function destroy(Form $form)
//     {
//         if ($form->asset) {
//             Storage::disk('public')->delete($form->asset->storage_path);
//             $form->asset->delete();
//         }

//         $form->delete();

//         return response()->json([
//             'message' => 'Form deleted successfully',
//         ]);
//     }
// }
