<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory {
    public function definition(): array {
        return [
            'name' => $this->faker->words(2, true) . ' Account',
            'balance' => $this->faker->randomFloat(2, 500000, 10000000),
            'icon' => $this->faker->randomElement(['🏦', '💵', '💳', '💰']),
            'color' => $this->faker->hexColor(),
        ];
    }
}