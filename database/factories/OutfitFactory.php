<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OutfitFactory extends Factory
{
    public function definition(): array
    {
        return [
            'description' => fake()->sentence(),
        ];
    }
}
