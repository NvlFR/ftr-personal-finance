<?php

namespace Database\Factories;

use App\DebtStatus;
use App\DebtType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Debt>
 */
class DebtFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(DebtType::cases()),
            'description' => $this->faker->sentence(4),
            'amount' => $this->faker->numberBetween(100000, 10000000),
            'paid_amount' => 0,
            'due_date' => $this->faker->dateTimeBetween('+1 month', '+2 years'),
            'status' => DebtStatus::UNPAID,
        ];
    }
}