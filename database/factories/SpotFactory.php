<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Outfit;
use App\Models\Source;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpotFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => null,  // On laissera le test définir cela
            'outfit_id' => null, // On laissera le test définir cela
            'person_id' => null,
            'source_id' => null, // On laissera le test définir cela
            'status' => fake()->randomElement(['draft', 'pending', 'published', 'rejected']),
            'visibility' => fake()->randomElement(['public', 'member', 'premium', 'private']),
            'is_adult_content' => fake()->boolean(),
            'validated' => false,
            'validation_user_id' => null,
            'validation_date' => null,
            'rejection_reason' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'visibility' => 'public',
            'validated' => true,
            'validation_date' => now(),
        ]);
    }
}
