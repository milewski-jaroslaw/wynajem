<?php

namespace Database\Factories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserShift>
 */
class UserShiftFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date_from = fake()->date();
        $date_to = Carbon::createFromTimeString($date_from.' 00:00:00')->addDays(
            fake()->numberBetween(1, 20)
        )->format('Y-m-d');

        return [
            'user_id' => User::factory()->create()->getKey(),
            'substitute_user_id' => User::factory()->create()->getKey(),
            'temp_changes' => [],
            'date_from' => $date_from,
            'date_to' => $date_to,
        ];
    }
}
