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
            'nik' => $this->faker->unique()->numerify('################'),
            'name' => $this->faker->name(),
            'birth_pace' => $this->faker->city(),
            'birth_date' => $this->faker->date(),
            'gender' => $this->faker->randomElement(['L', 'P']),
            'neighborhood_unit' => $this->faker->address(),
            'father_name' => $this->faker->name(),
            'mother_name' => $this->faker->name(),
            'education_level' => $this->faker->randomElement(['SD', 'SMP', 'SMA']),
            'school_grade' => $this->faker->numberBetween(1, 3),
            'shirt_size' => $this->faker->randomElement(['S', 'M', 'L', 'XL']),
            'shoe_size' => $this->faker->numberBetween(24, 44),
            'status' => $this->faker->randomElement(['Yatim', 'Piatu', 'Yatim Piatu', 'Dhuafa']),
            'phone_number' => $this->faker->phoneNumber(),
            'death_certificate_number' => $this->faker->bothify('??/##/???/????'),
        ];
    }
}
