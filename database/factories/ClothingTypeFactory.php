<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClothingTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'clothing_category_id' => \App\Models\ClothingCategory::factory(),
            'name' => $this->faker->randomElement(['T-shirt', 'blouse', 'pants', 'skirt']),
        ];
    }
}
