<?php

namespace App\Services\Faqs;

use DomainException;
use App\Models\FaqAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FaqAnswerService
{
    public function list()
    {
        return FaqAnswer::with('question')
            ->whereNull('deleted_at') // ✅ Exclude archived
            ->get();
    }

    public function datatable(Request $request)
    {
        // ✅ Show ALL records including archived
        $query = FaqAnswer::with('question');

        $total = $query->count();

        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('answer', 'like', "%{$search}%")
                    ->orWhereHas('question', fn($q2) => $q2->where('question', 'like', "%{$search}%"));
            });
        }

        $filtered = $query->count();

        $columns = ['question', 'answer', 'status', 'created_at', 'updated_at', 'actions'];
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderColumn = $columns[$orderColumnIndex] ?? 'answer';
        $orderDir = $request->input('order.0.dir', 'asc');
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        if (!in_array($orderColumn, ['actions'])) {
            if ($orderColumn === 'question') {
                $query->leftJoin('faqs_questions', 'faqs_answers.faq_id', '=', 'faqs_questions.id')
                    ->select('faqs_answers.*')
                    ->orderBy('faqs_questions.question', $orderDir)
                    ->with('question');
            } else {
                $query->orderBy($orderColumn, $orderDir);
            }
        }

        $data = $query->offset($start)->limit($length)->get();

        return [
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data->map(function ($item) {
                return [
                    'question' => $item->question->question ?? '—',
                    'answer' => \Str::limit($item->answer, 120),
                    'status' => $item->is_active
                        ? '<span class="badge badge-success">Active</span>'
                        : '<span class="badge badge-danger">Inactive</span>',
                    'created_at' => $item->created_at->toDateTimeString(),
                    'updated_at' => $item->updated_at->toDateTimeString(),
                    'archived' => !is_null($item->deleted_at),
                    'actions' => view('admin.faqs.answers.partials.actions', ['item' => $item])->render(),
                ];
            }),
        ];
    }

    public function create(array $data): FaqAnswer
    {
        return DB::transaction(function () use ($data) {
            // ✅ Restore if same answer was archived
            $existing = FaqAnswer::where('faq_id', $data['faq_id'])
                ->where('answer', $data['answer'])
                ->first();

            if ($existing && !is_null($existing->deleted_at)) {
                $existing->update([
                    'is_active' => $data['is_active'] ?? 1,
                    'deleted_at' => null,
                    'updated_by' => Auth::id(),
                ]);
                return $existing;
            }

            return FaqAnswer::create([
                'faq_id' => $data['faq_id'],
                'answer' => $data['answer'],
                'is_active' => $data['is_active'] ?? 1,
                'updated_by' => Auth::id(),
            ]);
        });
    }

    public function update(FaqAnswer $answer, array $data): FaqAnswer
    {
        return DB::transaction(function () use ($answer, $data) {
            $conflict = FaqAnswer::where('faq_id', $data['faq_id'])
                ->where('answer', $data['answer'])
                ->where('id', '!=', $answer->id)
                ->first();

            if ($conflict) {
                throw new DomainException('This FAQ answer already exists.');
            }

            $answer->update([
                'faq_id' => $data['faq_id'],
                'answer' => $data['answer'],
                'is_active' => $data['is_active'],
                'updated_by' => Auth::id(),
            ]);

            return $answer;
        });
    }

    /**
     * ✅ Hard delete — must be inactive first
     */
    public function delete(FaqAnswer $answer): void
    {
        try {
            DB::transaction(function () use ($answer) {

                // ✅ Guard: must be inactive before hard deleting
                if ($answer->is_active) {
                    throw new DomainException(
                        "Cannot delete this FAQ answer. Please deactivate it before deleting."
                    );
                }

                $answer->delete();
            });
        } catch (DomainException $e) {
            throw $e;
        } catch (\Exception $e) {
            report($e);
            throw new DomainException('Failed to delete FAQ answer: ' . $e->getMessage());
        }
    }

    /**
     * ✅ Archive — sets is_active = false + deleted_at = now()
     */
    public function archive(FaqAnswer $answer): FaqAnswer
    {
        try {
            return DB::transaction(function () use ($answer) {
                $answer->update([
                    'is_active' => false,
                    'deleted_at' => now(),
                ]);
                return $answer;
            });
        } catch (\Throwable $e) {
            report($e);
            throw new DomainException('Failed to archive FAQ answer.');
        }
    }

    /**
     * ✅ Unarchive — sets is_active = true + deleted_at = null
     */
    public function unarchive(FaqAnswer $answer): FaqAnswer
    {
        try {
            return DB::transaction(function () use ($answer) {
                $answer->update([
                    'is_active' => true,
                    'deleted_at' => null,
                ]);
                return $answer;
            });
        } catch (\Throwable $e) {
            report($e);
            throw new DomainException('Failed to unarchive FAQ answer.');
        }
    }
}