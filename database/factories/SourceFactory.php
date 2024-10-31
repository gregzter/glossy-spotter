<?php

namespace Database\Factories;

use App\Models\SourceType;
use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class SourceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'source_type_id' => SourceType::factory(),
            'country_id' => null, // On le rend nullable pour simplifier les tests
            'tv_show_id' => null,
            'tv_episode_id' => null,
            'tv_channel_id' => null,
            'tv_program_id' => null,
            'title' => fake()->sentence(3),
            'original_title' => null,
            'release_date' => fake()->date(),
            'url' => fake()->url()
        ];
    }
}
