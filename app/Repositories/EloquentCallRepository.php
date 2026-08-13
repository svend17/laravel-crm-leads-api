<?php

namespace App\Repositories;

use App\Contracts\Repositories\CallRepositoryInterface;
use App\Enums\CallResult;
use App\Models\Call;
use App\Models\Lead;
use Illuminate\Support\Collection;

class EloquentCallRepository implements CallRepositoryInterface
{
    /**
     * @param  array{duration: int, result: string}  $data
     */
    public function createForLead(Lead $lead, array $data): Call
    {
        return $lead->calls()->create($data);
    }

    public function countForLead(Lead $lead): int
    {
        return $lead->calls()->count();
    }

    /**
     * @return Collection<int, CallResult|string>
     */
    public function latestResultsForLead(Lead $lead, int $limit): Collection
    {
        return $lead->calls()
            ->latest('created_at')
            ->latest('id')
            ->limit($limit)
            ->pluck('result');
    }
}
