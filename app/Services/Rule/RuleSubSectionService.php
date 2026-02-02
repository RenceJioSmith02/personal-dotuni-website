<?php

namespace App\Services\Rule;

use App\Models\RuleSubSection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class RuleSubSectionService
{
    public function list()
    {
        return RuleSubSection::with('section.article')
            ->orderBy('sort_order')
            ->get();
    }

    public function storeOrRestore(array $data): RuleSubSection
    {
        return DB::transaction(function () use ($data) {
            try {
                $data['updated_by'] = Auth::id();

                $existing = RuleSubSection::withTrashed()
                    ->where('section_id', $data['section_id'])
                    ->where('number', $data['number'])
                    ->first();

                if ($existing) {
                    if ($existing->trashed()) {
                        $existing->restore();
                    }

                    $existing->update($data);
                    return $existing;
                }

                return RuleSubSection::create($data);

            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    public function updateOrRestore(RuleSubSection $current, array $data): RuleSubSection
    {
        return DB::transaction(function () use ($current, $data) {
            try {
                $data['updated_by'] = Auth::id();

                $conflict = RuleSubSection::withTrashed()
                    ->where('section_id', $data['section_id'])
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

    public function delete(RuleSubSection $subSection): void
    {
        DB::transaction(fn() => $subSection->delete());
    }
}
