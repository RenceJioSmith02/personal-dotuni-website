<?php

namespace App\Services\Faqs;

use App\Models\FaqAnswer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use DomainException;

class FaqAnswerService
{
    public function list()
    {
        return FaqAnswer::with('question')->get();
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
