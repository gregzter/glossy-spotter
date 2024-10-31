<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ColorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->colorName(),
            'is_glossy' => $this->faker->boolean(),
            'category' => $this->faker->randomElement(['basic', 'glossy'])
        ];
    }
}
