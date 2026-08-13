<?php

namespace Tests\Feature;

use App\Enums\LeadStatus;
use App\Models\Manager;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class LeadApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_lead_can_be_created_with_new_status(): void
    {
        $manager = Manager::factory()->create();

        $response = $this->postJson('/api/leads', [
            'name' => 'Jane Lead',
            'phone' => '+380501112233',
            'manager_id' => $manager->id,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.name', 'Jane Lead')
            ->assertJsonPath('data.phone', '+380501112233')
            ->assertJsonPath('data.status', LeadStatus::New->value)
            ->assertJsonPath('data.manager_id', $manager->id);

        $this->assertDatabaseHas('leads', [
            'name' => 'Jane Lead',
            'phone' => '+380501112233',
            'status' => LeadStatus::New->value,
            'manager_id' => $manager->id,
        ]);
    }

    public function test_lead_payload_is_validated(): void
    {
        $response = $this->postJson('/api/leads', [
            'name' => '',
            'phone' => '',
            'manager_id' => 999,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'phone', 'manager_id']);
    }

    public function test_lead_status_cannot_be_set_directly(): void
    {
        $response = $this->postJson('/api/leads', [
            'name' => 'Jane Lead',
            'phone' => '+380501112233',
            'status' => LeadStatus::Won->value,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);
    }
}
