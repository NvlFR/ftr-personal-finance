<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FinancialGoal>
 */
class FinancialGoalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Dana Darurat', 'Beli Rumah', 'Liburan Keluarga', 'Pendidikan Anak', 'Mobil Baru']),
            'description' => $this->faker->optional()->sentence(),
            'target_amount' => $this->faker->randomElement([25000000, 50000000, 100000000, 500000000]),
            'current_amount' => 0,
            'target_date' => $this->faker->dateTimeBetween('+1 year', '+10 years'),
        ];
    }
}