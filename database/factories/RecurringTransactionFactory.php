<?php

namespace Database\Factories;

use App\RecurrenceFrequency;
use App\TransactionType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RecurringTransaction>
 */
class RecurringTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => TransactionType::EXPENSE,
            'amount' => $this->faker->randomElement([50000, 150000, 250000, 500000]),
            'description' => $this->faker->randomElement(['Tagihan Internet', 'Langganan Streaming', 'Cicilan', 'Iuran Keamanan']),
            'frequency' => RecurrenceFrequency::MONTHLY,
            'interval' => 1,
            'start_date' => $this->faker->dateTimeBetween('-3 months', '-1 month'),
            'next_due_date' => now()->addDays(rand(5, 25)),
            'is_active' => true,
        ];
    }
}