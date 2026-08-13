<?php

namespace Tests\Feature;

use App\Enums\CallResult;
use App\Enums\LeadStatus;
use App\Models\Call;
use App\Models\Lead;
use App\Models\Manager;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class LeadCallApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_first_call_moves_new_lead_to_in_progress_and_assigns_manager(): void
    {
        $manager = Manager::factory()->create();
        $lead = Lead::factory()->create();

        $response = $this->postJson("/api/leads/{$lead->id}/calls", [
            'duration' => 120,
            'result' => CallResult::CallbackLater->value,
            'manager_id' => $manager->id,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.duration', 120)
            ->assertJsonPath('data.result', CallResult::CallbackLater->value);

        $lead->refresh();

        $this->assertSame(LeadStatus::InProgress, $lead->status);
        $this->assertSame($manager->id, $lead->manager_id);
    }

    public function test_call_does_not_overwrite_existing_manager(): void
    {
        $existingManager = Manager::factory()->create();
        $callingManager = Manager::factory()->create();
        $lead = Lead::factory()->assigned($existingManager)->create();

        $this->postJson("/api/leads/{$lead->id}/calls", [
            'duration' => 90,
            'result' => CallResult::CallbackLater->value,
            'manager_id' => $callingManager->id,
        ])->assertCreated();

        $this->assertSame($existingManager->id, $lead->refresh()->manager_id);
    }

    public function test_success_call_marks_lead_as_won(): void
    {
        $manager = Manager::factory()->create();
        $lead = Lead::factory()->status(LeadStatus::InProgress)->create();

        $this->postJson("/api/leads/{$lead->id}/calls", [
            'duration' => 60,
            'result' => CallResult::Success->value,
            'manager_id' => $manager->id,
        ])->assertCreated();

        $this->assertSame(LeadStatus::Won, $lead->refresh()->status);
    }

    public function test_last_three_no_answer_calls_mark_lead_as_lost(): void
    {
        $manager = Manager::factory()->create();
        $lead = Lead::factory()->status(LeadStatus::InProgress)->create();

        Call::factory()
            ->for($lead)
            ->result(CallResult::NoAnswer)
            ->count(2)
            ->sequence(
                ['created_at' => now()->subMinutes(3), 'updated_at' => now()->subMinutes(3)],
                ['created_at' => now()->subMinutes(2), 'updated_at' => now()->subMinutes(2)],
            )
            ->create();

        $this->postJson("/api/leads/{$lead->id}/calls", [
            'duration' => 30,
            'result' => CallResult::NoAnswer->value,
            'manager_id' => $manager->id,
        ])->assertCreated();

        $this->assertSame(LeadStatus::Lost, $lead->refresh()->status);
    }

    public function test_non_consecutive_no_answer_calls_do_not_mark_lead_as_lost(): void
    {
        $manager = Manager::factory()->create();
        $lead = Lead::factory()->status(LeadStatus::InProgress)->create();

        Call::factory()->for($lead)->result(CallResult::NoAnswer)->create([
            'created_at' => now()->subMinutes(3),
            'updated_at' => now()->subMinutes(3),
        ]);
        Call::factory()->for($lead)->result(CallResult::CallbackLater)->create([
            'created_at' => now()->subMinutes(2),
            'updated_at' => now()->subMinutes(2),
        ]);

        $this->postJson("/api/leads/{$lead->id}/calls", [
            'duration' => 30,
            'result' => CallResult::NoAnswer->value,
            'manager_id' => $manager->id,
        ])->assertCreated();

        $this->assertSame(LeadStatus::InProgress, $lead->refresh()->status);
    }

    public function test_call_payload_is_validated(): void
    {
        $lead = Lead::factory()->create();

        $response = $this->postJson("/api/leads/{$lead->id}/calls", [
            'duration' => -1,
            'result' => 'invalid',
            'manager_id' => 999,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['duration', 'result', 'manager_id']);
    }
}
