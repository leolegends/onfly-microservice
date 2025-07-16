<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TravelRequest>
 */
class TravelRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('+1 week', '+3 months');
        $endDate = $this->faker->dateTimeBetween($startDate, (clone $startDate)->add(new \DateInterval('P2M')));
        
        return [
            'user_id' => 1, // Usar ID fixo para evitar loop
            'requestor_name' => $this->faker->name(),
            'destination' => $this->faker->city() . ', ' . $this->faker->country(),
            'departure_date' => $startDate,
            'return_date' => $endDate,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $this->faker->randomElement(['requested', 'approved', 'cancelled', 'rejected']),
            'purpose' => $this->faker->paragraph(2),
            'estimated_cost' => $this->faker->randomFloat(2, 500, 5000),
            'budget' => $this->faker->randomFloat(2, 500, 5000),
            'notes' => $this->faker->optional()->paragraph(),
            'justification' => $this->faker->paragraph(3),
        ];
    }

    /**
     * Create a requested status travel request
     */
    public function requested(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'requested',
            'approved_at' => null,
            'cancelled_at' => null,
            'approver_id' => null,
        ]);
    }

    /**
     * Create an approved travel request
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'approved_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'approver_id' => \App\Models\User::factory(),
            'cancelled_at' => null,
        ]);
    }

    /**
     * Create a cancelled travel request
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'cancelled_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'approved_at' => null,
        ]);
    }

    /**
     * Create a rejected travel request
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'rejection_reason' => $this->faker->paragraph(2),
            'approver_id' => \App\Models\User::factory(),
            'approved_at' => null,
            'cancelled_at' => null,
        ]);
    }
}
