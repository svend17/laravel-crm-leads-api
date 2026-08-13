<?php

namespace Tests\Feature;

use App\Enums\CallResult;
use App\Enums\LeadStatus;
use App\Models\Call;
use App\Models\Lead;
use App\Models\Manager;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ManagerLeadApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_manager_leads_are_paginated_with_call_aggregates(): void
    {
        $manager = Manager::factory()->create();
        $otherManager = Manager::factory()->create();
        $lead = Lead::factory()
            ->assigned($manager)
            ->status(LeadStatus::InProgress)
            ->create(['name' => 'Target Lead']);

        Lead::factory()->assigned($otherManager)->create(['name' => 'Other Lead']);

        Call::factory()->for($lead)->result(CallResult::CallbackLater)->create(['duration' => 30]);
        Call::factory()->for($lead)->result(CallResult::Success)->create(['duration' => 45]);

        $response = $this->getJson("/api/managers/{$manager->id}/leads?per_page=10");

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $lead->id)
            ->assertJsonPath('data.0.name', 'Target Lead')
            ->assertJsonPath('data.0.status', LeadStatus::InProgress->value)
            ->assertJsonPath('data.0.calls_count', 2)
            ->assertJsonPath('data.0.total_call_duration', 75)
            ->assertJsonPath('meta.per_page', 10)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'status', 'calls_count', 'total_call_duration'],
                ],
                'links',
                'meta',
            ]);
    }

    public function test_manager_lead_without_calls_returns_zero_total_duration(): void
    {
        $manager = Manager::factory()->create();
        $lead = Lead::factory()->assigned($manager)->create();

        $response = $this->getJson("/api/managers/{$manager->id}/leads");

        $response
            ->assertOk()
            ->assertJsonPath('data.0.id', $lead->id)
            ->assertJsonPath('data.0.calls_count', 0)
            ->assertJsonPath('data.0.total_call_duration', 0);
    }

    public function test_per_page_is_validated(): void
    {
        $manager = Manager::factory()->create();

        $response = $this->getJson("/api/managers/{$manager->id}/leads?per_page=101");

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['per_page']);
    }
}
