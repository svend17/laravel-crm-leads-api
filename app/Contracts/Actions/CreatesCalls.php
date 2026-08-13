<?php

namespace App\Contracts\Actions;

use App\Models\Call;
use App\Models\Lead;

interface CreatesCalls
{
    public function handle(Lead $lead, array $data): Call;
}
