<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Estate>
 */
class EstateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'supervisor_user_id' => User::factory()->create()->getKey(),
            'building_number' => fake()->numberBetween(1, 100),
            'city' => fake()->city(),
            'street' => fake()->streetName(),
            'zip' =>fake()->postcode(),
        ];
    }
}
