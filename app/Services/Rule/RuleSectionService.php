<?php

namespace App\Services\Rule;

use App\Models\RuleSection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class RuleSectionService
{
    public function list()
    {
        return RuleSection::with('article')
            ->orderBy('sort_order')
            ->get();
    }

    public function storeOrRestore(array $data): RuleSection
    {
        return DB::transaction(function () use ($data) {
            try {
                $data['updated_by'] = Auth::id();

                $existing = RuleSection::withTrashed()
                    ->where('article_id', $data['article_id'])
                    ->where('number', $data['number'])
                    ->first();

                if ($existing) {
                    if ($existing->trashed()) {
                        $existing->restore();
                    }

                    $existing->update($data);
                    return $existing;
                }

                return RuleSection::create($data);

            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    public function updateOrRestore(RuleSection $current, array $data): RuleSection
    {
        return DB::transaction(function () use ($current, $data) {
            try {
                $data['updated_by'] = Auth::id();

                $conflict = RuleSection::withTrashed()
                    ->where('article_id', $data['article_id'])
                    ->where('number', $data['number'])
                    ->where('id', '!=', $current->id)
                    ->first();

                if ($conflict) {
                    if ($conflict->trashed()) {
                        $conflict->restore();
                    }

                    $conflict->update($data);
                    $current->delete();

                    return $conflict;
                }

                $current->update($data);
                return $current;

            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    public function delete(RuleSection $section): void
    {
        DB::transaction(fn() => $section->delete());
    }
}
