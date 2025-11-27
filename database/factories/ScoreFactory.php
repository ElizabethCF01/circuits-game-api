<?php

namespace Database\Factories;

use App\Models\Level;
use App\Models\Player;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScoreFactory extends Factory
{
    public function definition(): array
    {
        return [
            'level_id' => Level::inRandomOrder()->first()?->id ?? Level::factory(),
            'player_id' => Player::inRandomOrder()->first()?->id ?? Player::factory(),
            'xp_earned' => fake()->numberBetween(10, 100),
            'commands_used' => fake()->numberBetween(1, 50),
            'completed_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
