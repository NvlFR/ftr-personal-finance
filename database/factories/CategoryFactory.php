<?php
namespace Database\Factories;
use App\TransactionType;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory {
    public function definition(): array {
        return [
            'name' => $this->faker->word,
            'type' => $this->faker->randomElement(TransactionType::cases()),
            'icon' => $this->faker->randomElement(['🍔', '🚗', '🏠', '🎬', '✈️', '🛒']),
        ];
    }
}