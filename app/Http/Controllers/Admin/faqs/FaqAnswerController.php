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

        $questions = FaqQuestion::where('is_active', 1)->orderBy('sort_order')->get();
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
        $this->service->delete($faqs_answer);
        return response()->json(['message' => 'FAQ answer deleted successfully', 'id' => $faqs_answer->id]);
    }
}


// namespace App\Http\Controllers\Admin\faqs;

// use App\Http\Controllers\Controller;
// use App\Models\FaqAnswer;
// use App\Models\FaqQuestion;
// use Illuminate\Http\Request;

// class FaqAnswerController extends Controller
// {
//     /**
//      * Display a listing of FAQ answers.
//      */
//     public function index()
//     {
//         $answers = FaqAnswer::with('question')->get();
//         $questions = FaqQuestion::where('is_active', 1)->orderBy('sort_order')->get();

//         return view('admin.faqs.answers.index', compact('answers', 'questions'));
//     }

//     /**
//      * Store a newly created FAQ answer (AJAX modal, soft-delete aware).
//      */
//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'faq_id' => 'required|exists:faqs_questions,id',
//             'answer' => 'required|string',
//             'is_active' => 'sometimes|boolean',
//         ]);

//         // Check if soft-deleted answer exists for the same question
//         $existing = FaqAnswer::withTrashed()
//             ->where('faq_id', $validated['faq_id'])
//             ->where('answer', $validated['answer'])
//             ->first();

//         if ($existing) {
//             $existing->restore();
//             $existing->update([
//                 'is_active' => $validated['is_active'] ?? 1,
//                 'updated_by' => auth()->id(),
//             ]);

//             return response()->json([
//                 'message' => 'FAQ answer restored successfully',
//                 'answer' => $existing,
//             ]);
//         }

//         $answer = FaqAnswer::create([
//             'faq_id' => $validated['faq_id'],
//             'answer' => $validated['answer'],
//             'is_active' => $validated['is_active'] ?? 1,
//             'updated_by' => auth()->id(),
//         ]);

//         return response()->json([
//             'message' => 'FAQ answer created successfully',
//             'answer' => $answer,
//         ], 201);
//     }

//     /**
//      * Get FAQ answer data for editing (AJAX modal).
//      */
//     public function edit(FaqAnswer $faqs_answer)
//     {
//         return response()->json([
//             'id' => $faqs_answer->id,
//             'faq_id' => $faqs_answer->faq_id,
//             'answer' => $faqs_answer->answer,
//             'is_active' => $faqs_answer->is_active,
//         ]);
//     }

//     /**
//      * Update the specified FAQ answer (AJAX modal, soft-delete aware).
//      */
//     public function update(Request $request, FaqAnswer $faqs_answer)
//     {
//         $validated = $request->validate([
//             'faq_id' => 'required|exists:faqs_questions,id',
//             'answer' => 'required|string',
//             'is_active' => 'required|boolean',
//         ]);

//         // Check soft-deleted conflicts
//         $conflict = FaqAnswer::withTrashed()
//             ->where('faq_id', $validated['faq_id'])
//             ->where('answer', $validated['answer'])
//             ->where('id', '!=', $faqs_answer->id)
//             ->first();

//         if ($conflict) {
//             return response()->json([
//                 'message' => 'This FAQ answer already exists (including archived records).'
//             ], 422);
//         }

//         $faqs_answer->update([
//             'faq_id' => $validated['faq_id'],
//             'answer' => $validated['answer'],
//             'is_active' => $validated['is_active'],
//             'updated_by' => auth()->id(),
//         ]);

//         return response()->json([
//             'message' => 'FAQ answer updated successfully',
//             'answer' => $faqs_answer,
//         ]);
//     }

//     /**
//      * Soft delete the specified FAQ answer.
//      */
//     public function destroy(FaqAnswer $faqs_answer)
//     {
//         $faqs_answer->delete();

//         return response()->json([
//             'message' => 'FAQ answer deleted successfully',
//             'id' => $faqs_answer->id,
//         ]);
//     }
// }
