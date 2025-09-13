<?php
namespace Database\Factories;
use App\TransactionType;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory {
    public function definition(): array {
        return [
            'type' => $this->faker->randomElement(TransactionType::cases()),
            'amount' => $this->faker->numberBetween(10000, 500000),
            'description' => $this->faker->sentence,
            'transaction_date' => $this->faker->dateTimeBetween('-3 months', 'now'),
        ];
    }
}