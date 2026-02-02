<?php

namespace App\Services\Rule;

use App\Models\RuleClause;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class RuleClauseService
{
    public function list()
    {
        return RuleClause::with('subSection.section.article')
            ->orderBy('sort_order')
            ->get();
    }

    public function storeOrRestore(array $data): RuleClause
    {
        return DB::transaction(function () use ($data) {
            try {
                $data['updated_by'] = Auth::id();

                $existing = RuleClause::withTrashed()
                    ->where('sub_section_id', $data['sub_section_id'])
                    ->where('number', $data['number'])
                    ->first();

                if ($existing) {
                    if ($existing->trashed()) {
                        $existing->restore();
                    }

                    $existing->update($data);
                    return $existing;
                }

                return RuleClause::create($data);

            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    public function updateOrRestore(RuleClause $current, array $data): RuleClause
    {
        return DB::transaction(function () use ($current, $data) {
            try {
                $data['updated_by'] = Auth::id();

                $conflict = RuleClause::withTrashed()
                    ->where('sub_section_id', $data['sub_section_id'])
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

    public function delete(RuleClause $clause): void
    {
        DB::transaction(fn() => $clause->delete());
    }
}
