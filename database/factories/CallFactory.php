<?php

namespace Database\Factories;

use App\Enums\CallResult;
use App\Models\Call;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Call>
 */
class CallFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lead_id' => Lead::factory(),
            'duration' => fake()->numberBetween(0, 900),
            'result' => fake()->randomElement(CallResult::cases()),
        ];
    }

    public function result(CallResult $result): static
    {
        return $this->state(fn (): array => [
            'result' => $result,
        ]);
    }
}
