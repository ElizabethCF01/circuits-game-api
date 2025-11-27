<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LevelFactory extends Factory
{
    public function definition(): array
    {
        $gridWidth = fake()->numberBetween(5, 10);
        $gridHeight = fake()->numberBetween(5, 10);
        $tiles = [];

        for ($i = 0; $i < $gridWidth * $gridHeight; $i++) {
            $tiles[] = [
                'type' => fake()->randomElement(['obstacle', 'circuit', 'empty']),
                'tile_id' => fake()->numberBetween(1, 20),
            ];
        }

        return [
            'user_id' => User::where('is_admin', true)->inRandomOrder()->first()?->id ?? User::factory()->create(['is_admin' => true])->id,
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'start_x' => fake()->numberBetween(0, $gridWidth - 1),
            'start_y' => fake()->numberBetween(0, $gridHeight - 1),
            'required_circuits' => fake()->numberBetween(1, 5),
            'max_commands' => fake()->numberBetween(10, 50),
            'difficulty' => fake()->randomElement(['easy', 'medium', 'hard']),
            'grid_width' => $gridWidth,
            'grid_height' => $gridHeight,
            'tiles' => $tiles,
        ];
    }
}
