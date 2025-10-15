<?php

namespace Database\Factories;

use App\Enums\RepairOrderStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RepairOrder>
 */
class RepairOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'status' => fake()->randomElement(RepairOrderStatus::cases()),
            'problem_description' => fake()->paragraph(),
            'started_at' => fake()->optional()->dateTimeBetween('-30 days', 'now'),
            'finished_at' => null,
        ];
    }
}
