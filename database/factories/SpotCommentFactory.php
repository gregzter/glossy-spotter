<?php

namespace Database\Factories;

use App\Models\Spot;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpotCommentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'spot_id' => Spot::factory(),
            'user_id' => User::factory(),
            'content' => $this->faker->paragraph(),
            'is_edited' => false,
            'edited_at' => null
        ];
    }
}
