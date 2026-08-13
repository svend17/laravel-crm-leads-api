<?php

namespace App\Actions;

use App\Contracts\Actions\CreatesCalls;
use App\Contracts\Repositories\CallRepositoryInterface;
use App\Contracts\Repositories\LeadRepositoryInterface;
use App\Enums\CallResult;
use App\Enums\LeadStatus;
use App\Models\Call;
use App\Models\Lead;
use Illuminate\Support\Facades\DB;

class CreateCallAction implements CreatesCalls
{
    private const int CONSECUTIVE_NO_ANSWER_CALLS_TO_LOSE_LEAD = 3;

    public function __construct(
        private readonly LeadRepositoryInterface $leads,
        private readonly CallRepositoryInterface $calls,
    ) {}

    public function handle(Lead $lead, array $data): Call
    {
        return DB::transaction(function () use ($lead, $data): Call {
            $lockedLead = $this->leads->findLocked($lead->id);

            $call = $this->calls->createForLead($lockedLead, [
                'duration' => $data['duration'],
                'result' => $data['result'],
            ]);

            $this->applyLeadBusinessRules($lockedLead, $call, $data['manager_id']);

            return $call->load('lead');
        });
    }

    private function applyLeadBusinessRules(Lead $lead, Call $call, int $managerId): void
    {
        if ($lead->manager_id === null) {
            $lead->manager_id = $managerId;
        }

        if ($this->calls->countForLead($lead) === 1 && $lead->status === LeadStatus::New) {
            $lead->status = LeadStatus::InProgress;
        }

        if ($call->result === CallResult::Success) {
            $lead->status = LeadStatus::Won;
        } elseif ($this->leadReachedNoAnswerLossThreshold($lead)) {
            $lead->status = LeadStatus::Lost;
        }

        if ($lead->isDirty(['manager_id', 'status'])) {
            $this->leads->save($lead);
        }
    }

    private function leadReachedNoAnswerLossThreshold(Lead $lead): bool
    {
        $latestResults = $this->calls->latestResultsForLead($lead, self::CONSECUTIVE_NO_ANSWER_CALLS_TO_LOSE_LEAD);

        // The "lost" rule applies only when the full threshold window failed with no answer.
        return $latestResults->count() === self::CONSECUTIVE_NO_ANSWER_CALLS_TO_LOSE_LEAD
            && $latestResults->every(
                fn (mixed $result): bool => $result instanceof CallResult
                    ? $result === CallResult::NoAnswer
                    : $result === CallResult::NoAnswer->value
            );
    }
}
