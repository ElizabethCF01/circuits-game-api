<?php

namespace Database\Seeders;

use App\Models\Tile;
use Illuminate\Database\Seeder;

class TileSeeder extends Seeder
{
    public function run(): void
    {
        $tiles = [
            ['type' => 'empty'],
            ['type' => 'circuit'],
            ['type' => 'obstacle'],
        ];

        foreach ($tiles as $tileData) {
            $tile = Tile::firstOrCreate(['type' => $tileData['type']]);

            $imagePath = database_path("seeders/images/{$tileData['type']}.png");

            // If image exists and tile doesn't have media yet, attach it
            if (file_exists($imagePath) && $tile->getMedia('images')->isEmpty()) {
                $tile->addMedia($imagePath)
                    ->preservingOriginal()
                    ->toMediaCollection('images');
            }
        }
    }
}
