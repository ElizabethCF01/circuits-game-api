<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TileFactory extends Factory
{
    public function definition(): array
    {
        $types = ['obstacle', 'circuit', 'empty'];

        return [
            'type' => fake()->randomElement($types),
            'image' => fake()->optional()->imageUrl(64, 64, 'tiles', true),
        ];
    }
}
