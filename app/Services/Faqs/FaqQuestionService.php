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
        return FaqQuestion::with([
            'answers' => fn($q) => $q->where('is_active', 1)->whereNull('deleted_at')
        ])
            ->whereNull('deleted_at') // ✅ Exclude archived
            ->orderBy('sort_order')
            ->get();
    }

    public function listPaginated($page = 1, $perPage = 5)
    {
        $query = FaqQuestion::with('answers')
            ->whereNull('deleted_at') // ✅ Exclude archived
            ->orderBy('created_at', 'desc');

        $total = $query->count();
        $items = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

        return [
            'data' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
        ];
    }

    public function datatable(Request $request)
    {
        // ✅ Show ALL records including archived
        $query = FaqQuestion::with('answers');

        $total = $query->count();

        if ($search = $request->input('search.value')) {
            $query->where('question', 'like', "%{$search}%");
        }

        $filtered = $query->count();

        $columns = ['question', 'sort_order', 'status', 'created_at', 'updated_at'];
        $orderColumnIndex = (int) $request->input('order.0.column', 0);
        $orderColumn = $columns[$orderColumnIndex] ?? 'sort_order';
        $orderDir = $request->input('order.0.dir') === 'desc' ? 'desc' : 'asc';

        if (!in_array($orderColumn, ['actions'])) {
            $query->orderBy($orderColumn, $orderDir);
        }

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
                'archived' => !is_null($item->deleted_at), // ✅ Pass archive state
                'actions' => view('admin.faqs.questions.partials.actions', compact('item'))->render(),
                'answers' => $item->answers->map(fn($answer) => [
                    'id' => $answer->id,
                    'answer' => $answer->answer,
                    'status' => $answer->is_active,
                    'archived' => !is_null($answer->deleted_at),
                ]),
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
            // ✅ Restore if same question was archived
            $existing = FaqQuestion::where('question', $data['question'])->first();

            if ($existing && !is_null($existing->deleted_at)) {
                $existing->update([
                    'sort_order' => $data['sort_order'] ?? $existing->sort_order,
                    'is_active' => $data['is_active'] ?? 1,
                    'deleted_at' => null,
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
            $conflict = FaqQuestion::where('question', $data['question'])
                ->where('id', '!=', $question->id)
                ->first();

            if ($conflict) {
                throw new DomainException('This FAQ question already exists.');
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

    /**
     * ✅ Hard delete — must be inactive first
     */
    public function delete(FaqQuestion $question): void
    {
        try {
            DB::transaction(function () use ($question) {

                // ✅ Guard: must be inactive before hard deleting
                if ($question->is_active) {
                    throw new DomainException(
                        "Cannot delete this FAQ question. Please deactivate it before deleting."
                    );
                }

                $answerCount = $question->answers()->count();
                if ($answerCount > 0) {
                    throw new DomainException(
                        "Cannot delete this FAQ question because it has {$answerCount} answer(s). Please delete the answers first."
                    );
                }

                $question->delete();
            });
        } catch (DomainException $e) {
            throw $e;
        } catch (\Exception $e) {
            report($e);
            throw new DomainException('Failed to delete FAQ question: ' . $e->getMessage());
        }
    }

    /**
     * ✅ Archive — sets is_active = false + deleted_at = now()
     */
    public function archive(FaqQuestion $question): FaqQuestion
    {
        try {
            return DB::transaction(function () use ($question) {
                $question->update([
                    'is_active' => false,
                    'deleted_at' => now(),
                ]);
                return $question;
            });
        } catch (\Throwable $e) {
            report($e);
            throw new DomainException('Failed to archive FAQ question.');
        }
    }

    /**
     * ✅ Unarchive — sets is_active = true + deleted_at = null
     */
    public function unarchive(FaqQuestion $question): FaqQuestion
    {
        try {
            return DB::transaction(function () use ($question) {
                $question->update([
                    'is_active' => true,
                    'deleted_at' => null,
                ]);
                return $question;
            });
        } catch (\Throwable $e) {
            report($e);
            throw new DomainException('Failed to unarchive FAQ question.');
        }
    }
}