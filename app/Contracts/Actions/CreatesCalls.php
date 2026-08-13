<?php

namespace App\Contracts\Actions;

use App\Models\Call;
use App\Models\Lead;

interface CreatesCalls
{
    /**
     * Create a call for the lead and apply the related lead status rules.
     *
     * @param  array{duration: int, result: string, manager_id: int}  $data
     */
    public function handle(Lead $lead, array $data): Call;
}
