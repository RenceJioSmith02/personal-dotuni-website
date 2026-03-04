<?php

namespace App\Http\Controllers\Admin\faqs;

use DomainException;
use App\Models\FaqAnswer;
use App\Models\FaqQuestion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Faqs\FaqAnswerService;

class FaqAnswerController extends Controller
{
    public function __construct(protected FaqAnswerService $service)
    {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->service->datatable($request);
        }

        $questions = FaqQuestion::where('is_active', 1)->whereNull('deleted_at')->orderBy('sort_order')->get();

        return view('admin.faqs.answers.index', compact('questions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'faq_id' => 'required|exists:faqs_questions,id',
            'answer' => 'required|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $answer = $this->service->create($validated);

        return response()->json(['message' => 'FAQ answer created successfully', 'answer' => $answer], 201);
    }

    public function edit(FaqAnswer $faqs_answer)
    {
        return response()->json($faqs_answer);
    }

    public function update(Request $request, FaqAnswer $faqs_answer)
    {
        $validated = $request->validate([
            'faq_id' => 'required|exists:faqs_questions,id',
            'answer' => 'required|string',
            'is_active' => 'required|boolean',
        ]);

        try {
            $answer = $this->service->update($faqs_answer, $validated);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'FAQ answer updated successfully', 'answer' => $answer]);
    }

    public function destroy(FaqAnswer $faqs_answer)
    {
        try {
            $this->service->delete($faqs_answer);

            return response()->json(['message' => 'FAQ answer deleted successfully', 'id' => $faqs_answer->id]);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    // ✅ New
    public function archive(FaqAnswer $faqs_answer)
    {
        try {
            $answer = $this->service->archive($faqs_answer);

            return response()->json(['message' => 'FAQ answer archived successfully', 'answer' => $answer]);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    // ✅ New
    public function unarchive(FaqAnswer $faqs_answer)
    {
        try {
            $answer = $this->service->unarchive($faqs_answer);

            return response()->json(['message' => 'FAQ answer unarchived successfully', 'answer' => $answer]);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}