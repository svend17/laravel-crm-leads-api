<?php

namespace Database\Factories;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\Manager;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => fake()->numerify('+380#########'),
            'status' => LeadStatus::New,
            'manager_id' => null,
        ];
    }

    public function assigned(?Manager $manager = null): static
    {
        return $this->state(fn (): array => [
            'manager_id' => $manager?->id ?? Manager::factory(),
        ]);
    }

    public function status(LeadStatus $status): static
    {
        return $this->state(fn (): array => [
            'status' => $status,
        ]);
    }
}
