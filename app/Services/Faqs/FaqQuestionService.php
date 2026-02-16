<?php

namespace App\Services\Faqs;

use DomainException;
use App\Models\FaqQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FaqQuestionService
{
    public function list()
    {
        // Load the question with all related answers
        return FaqQuestion::with([
            'answers' => function ($query) {
                $query->where('is_active', 1); // optional: only active answers
            }
        ])
            ->orderBy('sort_order')
            ->get();
    }




    public function datatable(Request $request)
    {
        $query = FaqQuestion::with('answers');

        // Total records BEFORE filtering
        $total = $query->count();

        // Search
        if ($search = $request->input('search.value')) {
            $query->where('question', 'like', "%{$search}%");
        }

        // Total records AFTER filtering
        $filtered = $query->count();

        // Ordering
        $columns = ['question', 'sort_order', 'status', 'created_at', 'updated_at'];
        $orderColumnIndex = (int) $request->input('order.0.column', 0);
        $orderColumn = $columns[$orderColumnIndex] ?? 'sort_order';
        $orderDir = $request->input('order.0.dir') === 'desc' ? 'desc' : 'asc';
        if (!in_array($orderColumn, ['actions'])) {
            $query->orderBy($orderColumn, $orderDir);
        }

        // Pagination
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        $items = $query->skip($start)->take($length)->get();

        $data = $items->map(function ($item) {
            return [
                'id' => $item->id,
                'question' => $item->question,
                'status' => $item->is_active
                    ? '<span class="badge badge-success">Active</span>'
                    : '<span class="badge badge-danger">Inactive</span>',
                'created_at' => $item->created_at->toDateTimeString(),
                'actions' => view('admin.faqs.questions.partials.actions', compact('item'))->render(),
                'answers' => $item->answers->map(function ($answer) {
                    return [
                        'id' => $answer->id,
                        'answer' => $answer->answer,
                        'status' => $answer->is_active,
                    ];
                }),
            ];
        });

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data,
        ]);
    }



    public function create(array $data): FaqQuestion
    {
        return DB::transaction(function () use ($data) {
            $existing = FaqQuestion::withTrashed()
                ->where('question', $data['question'])
                ->first();

            if ($existing) {
                $existing->restore();
                $existing->update([
                    'sort_order' => $data['sort_order'] ?? $existing->sort_order,
                    'is_active' => $data['is_active'] ?? 1,
                    'updated_by' => Auth::id(),
                ]);
                return $existing;
            }

            return FaqQuestion::create([
                'question' => $data['question'],
                'sort_order' => $data['sort_order'] ?? 0,
                'is_active' => $data['is_active'] ?? 1,
                'updated_by' => Auth::id(),
            ]);
        });
    }

    public function update(FaqQuestion $question, array $data): FaqQuestion
    {
        return DB::transaction(function () use ($question, $data) {
            $conflict = FaqQuestion::withTrashed()
                ->where('question', $data['question'])
                ->where('id', '!=', $question->id)
                ->first();

            if ($conflict) {
                throw new DomainException('This FAQ question already exists (including archived records).');
            }

            $question->update([
                'question' => $data['question'],
                'sort_order' => $data['sort_order'] ?? $question->sort_order,
                'is_active' => $data['is_active'],
                'updated_by' => Auth::id(),
            ]);

            return $question;
        });
    }

    public function delete(FaqQuestion $question): void
    {
        try {
            DB::transaction(function () use ($question) {

                // Count linked answers
                $answerCount = $question->answers()->count();

                if ($answerCount > 0) {
                    // User-friendly message
                    throw new DomainException(
                        "Cannot delete this FAQ question because it has {$answerCount} answer(s) in the FAQ Answers table. Please delete the answers first."
                    );
                }

                // Safe to delete
                $question->delete();
            });
        } catch (DomainException $e) {
            throw $e; // Controller will handle this
        } catch (\Exception $e) {
            report($e);
            throw new DomainException('Failed to delete FAQ question: ' . $e->getMessage());
        }
    }


}
