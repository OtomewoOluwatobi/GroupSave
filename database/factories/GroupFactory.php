<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Group>
 */
class GroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'owner_id' => User::factory(),
            'title' => fake()->unique()->words(3, true),
            'total_users' => fake()->numberBetween(1, 50),
            'target_amount' => fake()->numberBetween(10000, 100000),
            'payable_amount' => fake()->numberBetween(10000, 100000),
            'expected_start_date' => fake()->dateTimeBetween('now', '+1 month'),
            'expected_end_date' => fake()->dateTimeBetween('+2 months', '+6 months'),
            'payment_out_day' => fake()->numberBetween(1, 28),
            'status' => 'active',
        ];
    }

    /**
     * Indicate that the group is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}
