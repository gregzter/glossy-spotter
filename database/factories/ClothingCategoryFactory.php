<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClothingCategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['tops', 'bottoms', 'dresses', 'full_outfits'])
        ];
    }
}
