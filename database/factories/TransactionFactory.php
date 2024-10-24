<?php

namespace Database\Factories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition()
    {
        return [
            'id' => $this->faker->unique()->lexify('TRANS???'),
            'created_at' => now(),
            'updated_at' => now(),
            'transfer_date' => $this->faker->date(),
            'donor_name' => $this->faker->name,
            'method' => $this->faker->randomElement(['Bank Transfer', 'Cash']),
            'type' => $this->faker->randomElement(['Donation', 'Sponsorship']),
            'amount' => $this->faker->numberBetween(10000, 1000000),
        ];
    }
}
