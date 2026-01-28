<?php

use App\Models\Tile;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tiles = [
            ['type' => 'empty'],
            ['type' => 'circuit'],
            ['type' => 'obstacle'],
        ];

        foreach ($tiles as $tileData) {
            $tile = Tile::firstOrCreate(['type' => $tileData['type']]);

            $imagePath = database_path("seeders/images/{$tileData['type']}.png");

            if (file_exists($imagePath) && $tile->getMedia('images')->isEmpty()) {
                $tile->addMedia($imagePath)
                    ->preservingOriginal()
                    ->toMediaCollection('images');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Tile::whereIn('type', ['empty', 'circuit', 'obstacle'])->each(function (Tile $tile) {
            $tile->clearMediaCollection('images');
            $tile->delete();
        });
    }
};
