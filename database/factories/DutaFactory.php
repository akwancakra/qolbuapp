<?php

namespace Database\Factories;

use App\Models\Duta;
use Illuminate\Database\Eloquent\Factories\Factory;

class DutaFactory extends Factory
{
    protected $model = Duta::class;

    public function definition()
    {
        return [
            'id' => $this->faker->unique()->lexify('DUTA???'),
            'name' => $this->faker->name,
            'code' => $this->faker->unique()->lexify('CODE??'),
            'phone_number' => $this->faker->phoneNumber,
        ];
    }
}
