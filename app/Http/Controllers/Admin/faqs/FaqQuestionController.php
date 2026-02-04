<?php


namespace App\Http\Controllers\Admin\faqs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Faqs\FaqQuestionService;
use App\Models\FaqQuestion;
use Illuminate\Validation\Rule;
use DomainException;

class FaqQuestionController extends Controller
{
    public function __construct(protected FaqQuestionService $service) {}

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->service->datatable($request);
        }

        return view('admin.faqs.questions.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => [
                'required','string','max:500',
                Rule::unique('faqs_questions')->whereNull('deleted_at')
            ],
            'sort_order' => 'nullable|integer',
            'is_active' => 'sometimes|boolean',
        ]);

        $question = $this->service->create($validated);

        return response()->json(['message' => 'FAQ question created successfully', 'question' => $question], 201);
    }

    public function edit(FaqQuestion $faqs_question)
    {
        return response()->json($faqs_question);
    }

    public function update(Request $request, FaqQuestion $faqs_question)
    {
        $validated = $request->validate([
            'question' => [
                'required','string','max:500',
                Rule::unique('faqs_questions')->ignore($faqs_question->id)->whereNull('deleted_at')
            ],
            'sort_order' => 'nullable|integer',
            'is_active' => 'required|boolean',
        ]);

        try {
            $question = $this->service->update($faqs_question, $validated);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'FAQ question updated successfully', 'question' => $question]);
    }

    public function destroy(FaqQuestion $faqs_question)
    {
        try {
            $this->service->delete($faqs_question);

            return response()->json([
                'success' => true,
                'message' => 'FAQ question deleted successfully',
                'id' => $faqs_question->id
            ]);
        } catch (DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

}
