<?php

namespace Database\Factories;

use App\Models\Ambassador;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Income>
 */
class IncomeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ambassador_id' => Ambassador::factory(),
            'transfer_date' => $this->faker->date(max:'now'),
            // 'amount' => $this->faker->randomFloat(2, 1000, 100000000000),
            'amount' => $this->faker->numberBetween(1000, 100000000000),
            'donor' => $this->faker->name(),
            'team' => $this->faker->numberBetween(1, 99),
            'payment_method' => $this->faker->randomElement(['Transfer Bank', 'Kartu Kredit', 'Virtual Account', 'Instant Payment']),
            'type' => $this->faker->randomElement(['Donasi', 'Sponsor']),
        ];
    }
}
