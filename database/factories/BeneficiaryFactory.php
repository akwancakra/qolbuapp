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
            'nik' => $this->faker->unique()->lexify('################'),
            'name' => $this->faker->name(),
            'place_of_birth' => $this->faker->city(),
            'date_of_birth' => $this->faker->date(),
            'gender' => $this->faker->randomElement(['L', 'P']),
            'neighborhood_unit' => $this->faker->address(),
            'father_name' => $this->faker->name(),
            'mother_name' => $this->faker->name(),
            'education_level' => $this->faker->randomElement(['SD', 'SMP', 'SMA']),
            'school_grade' => $this->faker->numberBetween(1, 3),
            'shirt_size' => $this->faker->randomElement(['S', 'M', 'L', 'XL']),
            'shoe_size' => $this->faker->numberBetween(24, 44),
        ];
    }
}
