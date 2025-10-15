<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'phone_number' => fake()->phoneNumber(),
            'email' => fake()->optional()->safeEmail(),
            'address_street' => fake()->optional()->streetAddress(),
            'address_city' => fake()->optional()->city(),
            'address_postal_code' => fake()->optional()->postcode(),
            'address_country' => fake()->optional()->country(),
        ];
    }
}
