<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MaterialFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['latex', 'leather', 'satin', 'vinyl']),
        ];
    }
}
