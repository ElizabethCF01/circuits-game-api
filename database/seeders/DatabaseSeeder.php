<?php

namespace Database\Seeders;

use App\Models\Level;
use App\Models\Player;
use App\Models\Score;
use App\Models\Tile;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::factory(10)->create();

        Player::factory(8)->create();

        Tile::factory(20)->create();

        Level::factory(10)->create();

        Score::factory(30)->create();
    }
}
