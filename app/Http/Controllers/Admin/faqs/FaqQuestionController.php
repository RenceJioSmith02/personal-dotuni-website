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

    public function index()
    {
        $questions = $this->service->list();
        return view('admin.faqs.questions.index', compact('questions'));
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
        $this->service->delete($faqs_question);
        return response()->json(['message' => 'FAQ question deleted successfully', 'id' => $faqs_question->id]);
    }
}

// namespace App\Http\Controllers\Admin\faqs;

// use App\Http\Controllers\Controller;
// use App\Models\FaqQuestion;
// use Illuminate\Http\Request;
// use Illuminate\Validation\Rule;

// class FaqQuestionController extends Controller
// {
//     /**
//      * Display a listing of FAQ questions.
//      */
//     public function index()
//     {
//         $questions = FaqQuestion::orderBy('sort_order')->get();

//         return view('admin.faqs.questions.index', compact('questions'));
//     }

//     /**
//      * Store a newly created FAQ question (AJAX modal, soft-delete aware).
//      */
//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'question' => [
//                 'required',
//                 'string',
//                 'max:500',
//                 Rule::unique('faqs_questions')
//                     ->whereNull('deleted_at'),
//             ],
//             'sort_order' => 'nullable|integer',
//             'is_active' => 'sometimes|boolean',
//         ]);

//         // Check if soft-deleted question exists
//         $existing = FaqQuestion::withTrashed()
//             ->where('question', $validated['question'])
//             ->first();

//         if ($existing) {
//             $existing->restore();
//             $existing->update([
//                 'sort_order' => $validated['sort_order'] ?? $existing->sort_order,
//                 'is_active' => $validated['is_active'] ?? 1,
//                 'updated_by' => auth()->id(),
//             ]);

//             return response()->json([
//                 'message' => 'FAQ question restored successfully',
//                 'question' => $existing,
//             ]);
//         }

//         $question = FaqQuestion::create([
//             'question' => $validated['question'],
//             'sort_order' => $validated['sort_order'] ?? 0,
//             'is_active' => $validated['is_active'] ?? 1,
//             'updated_by' => auth()->id(),
//         ]);

//         return response()->json([
//             'message' => 'FAQ question created successfully',
//             'question' => $question,
//         ], 201);
//     }

//     /**
//      * Get FAQ question data for editing (AJAX modal).
//      */
//     public function edit(FaqQuestion $faqs_question)
//     {
//         return response()->json([
//             'id' => $faqs_question->id,
//             'question' => $faqs_question->question,
//             'sort_order' => $faqs_question->sort_order,
//             'is_active' => $faqs_question->is_active,
//         ]);
//     }

//     /**
//      * Update the specified FAQ question (AJAX modal, soft-delete aware).
//      */
//     public function update(Request $request, FaqQuestion $faqs_question)
//     {
//         $validated = $request->validate([
//             'question' => [
//                 'required',
//                 'string',
//                 'max:500',
//                 Rule::unique('faqs_questions')
//                     ->ignore($faqs_question->id)
//                     ->whereNull('deleted_at'),
//             ],
//             'sort_order' => 'nullable|integer',
//             'is_active' => 'required|boolean',
//         ]);

//         // Check soft-deleted conflicts
//         $conflict = FaqQuestion::withTrashed()
//             ->where('question', $validated['question'])
//             ->where('id', '!=', $faqs_question->id)
//             ->first();

//         if ($conflict) {
//             return response()->json([
//                 'message' => 'This FAQ question already exists (including archived records).'
//             ], 422);
//         }

//         $faqs_question->update([
//             'question' => $validated['question'],
//             'sort_order' => $validated['sort_order'] ?? $faqs_question->sort_order,
//             'is_active' => $validated['is_active'],
//             'updated_by' => auth()->id(),
//         ]);

//         return response()->json([
//             'message' => 'FAQ question updated successfully',
//             'question' => $faqs_question,
//         ]);
//     }

//     /**
//      * Soft delete the specified FAQ question.
//      */
//     public function destroy(FaqQuestion $faqs_question)
//     {
//         $faqs_question->delete();

//         return response()->json([
//             'message' => 'FAQ question deleted successfully',
//             'id' => $faqs_question->id,
//         ]);
//     }
// }
