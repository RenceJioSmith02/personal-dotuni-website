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
        return FaqAnswer::with('question')->get();
    }

    public function datatable(Request $request)
    {
        $query = FaqAnswer::with('question');

        $total = $query->count();

        // Search
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('answer', 'like', "%{$search}%")
                    ->orWhereHas('question', function ($q2) use ($search) {
                        $q2->where('question', 'like', "%{$search}%");
                    });
            });
        }

        $filtered = $query->count();

        // Ordering
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
                    'actions' => view('admin.faqs.answers.partials.actions', ['item' => $item])->render(),
                ];
            }),
        ];
    }


    public function create(array $data): FaqAnswer
    {
        return DB::transaction(function () use ($data) {
            $existing = FaqAnswer::withTrashed()
                ->where('faq_id', $data['faq_id'])
                ->where('answer', $data['answer'])
                ->first();

            if ($existing) {
                $existing->restore();
                $existing->update([
                    'is_active' => $data['is_active'] ?? 1,
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
            $conflict = FaqAnswer::withTrashed()
                ->where('faq_id', $data['faq_id'])
                ->where('answer', $data['answer'])
                ->where('id', '!=', $answer->id)
                ->first();

            if ($conflict) {
                throw new DomainException('This FAQ answer already exists (including archived records).');
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

    public function delete(FaqAnswer $answer): void
    {
        DB::transaction(function () use ($answer) {
            $answer->delete();
        });
    }
}
