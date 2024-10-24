<?php

namespace Database\Factories;

use App\Models\Beneficiary;
use Illuminate\Database\Eloquent\Factories\Factory;

class BeneficiaryFactory extends Factory
{
    protected $model = Beneficiary::class;

    public function definition()
    {
        return [
            'id' => $this->faker->unique()->lexify('BENEF???'),
            'name' => $this->faker->name,
            'date_of_birth' => $this->faker->date(),
            'age' => $this->faker->numberBetween(1, 20),
            'address' => $this->faker->address,
            'father_name' => $this->faker->name,
            'mother_name' => $this->faker->name,
            'school_level' => $this->faker->randomElement(['SD', 'SMP', 'SMA']),
            'school_name' => $this->faker->company,
            'shirt_size' => $this->faker->randomElement(['S', 'M', 'L', 'XL']),
            'shoe_size' => $this->faker->randomElement(['36', '37', '38', '39']),
        ];
    }
}
