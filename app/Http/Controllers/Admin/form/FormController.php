<?php

namespace App\Http\Controllers\Admin\Form;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormCategory;
use App\Services\Form\FormService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class FormController extends Controller
{
    public function __construct(protected FormService $service)
    {
    }


    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->service->datatable($request);
        }

        // still needed for modal dropdowns
        $categories = FormCategory::orderBy('name')->get();

        return view('admin.form.forms.index', compact('categories'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'form_category_id' => 'required|exists:form_categories,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('forms', 'name')->whereNull('deleted_at'),
            ],
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
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('forms', 'name')
                    ->ignore($form->id)
                    ->whereNull('deleted_at'),
            ],
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

