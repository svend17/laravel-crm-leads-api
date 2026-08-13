<?php

namespace App\Contracts\Repositories;

use App\Enums\CallResult;
use App\Models\Call;
use App\Models\Lead;
use Illuminate\Support\Collection;

interface CallRepositoryInterface
{
    /**
     * @param  array{duration: int, result: string}  $data
     */
    public function createForLead(Lead $lead, array $data): Call;

    public function countForLead(Lead $lead): int;

    /**
     * @return Collection<int, CallResult|string>
     */
    public function latestResultsForLead(Lead $lead, int $limit): Collection;
}
