<?php

namespace App\Services\Faqs;

use App\Models\FaqQuestion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use DomainException;

class FaqQuestionService
{
    public function list()
    {
        return FaqQuestion::orderBy('sort_order')->get();
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
        DB::transaction(function () use ($question) {
            $question->delete();
        });
    }
}
