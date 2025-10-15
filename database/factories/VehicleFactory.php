<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $makes = ['Toyota', 'Honda', 'Ford', 'BMW', 'Mercedes', 'Audi', 'Volkswagen', 'Nissan', 'Mazda', 'Hyundai'];
        $models = ['Corolla', 'Civic', 'Focus', 'Series 3', 'C-Class', 'A4', 'Golf', 'Altima', 'Mazda3', 'Elantra'];

        return [
            'make' => fake()->randomElement($makes),
            'model' => fake()->randomElement($models),
            'year' => fake()->numberBetween(2000, 2024),
            'vin' => strtoupper(fake()->bothify('???##############')),
            'registration_number' => strtoupper(fake()->bothify('??#####')),
        ];
    }
}
