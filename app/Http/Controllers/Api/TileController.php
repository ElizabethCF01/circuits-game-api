<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tile;
use Illuminate\Http\JsonResponse;

/**
 * @group Tiles
 *
 * APIs for tile type definitions
 */
class TileController extends Controller
{
    /**
     * List all tile types
     *
     * Get all available tile types with their images for rendering the level grid.
     *
     * @unauthenticated
     *
     * @response {
     *   "tiles": [
     *     {
     *       "id": 1,
     *       "type": "floor",
     *       "image": "floor.png",
     *       "media_url": "https://example.com/storage/tiles/floor.png"
     *     },
     *     {
     *       "id": 2,
     *       "type": "wall",
     *       "image": "wall.png",
     *       "media_url": "https://example.com/storage/tiles/wall.png"
     *     },
     *     {
     *       "id": 3,
     *       "type": "circuit",
     *       "image": "circuit.png",
     *       "media_url": "https://example.com/storage/tiles/circuit.png"
     *     }
     *   ]
     * }
     */
    public function index(): JsonResponse
    {
        $tiles = Tile::all()->map(fn (Tile $tile) => [
            'id' => $tile->id,
            'type' => $tile->type,
            'image' => $tile->image,
            'media_url' => $tile->getFirstMediaUrl(),
        ]);

        return response()->json([
            'tiles' => $tiles,
        ]);
    }
}
