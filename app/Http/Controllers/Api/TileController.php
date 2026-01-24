<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tile;
use Illuminate\Http\JsonResponse;

class TileController extends Controller
{
    /**
     * List all tile types with their images
     *
     * GET /api/tiles
     */
    public function index(): JsonResponse
    {
        $tiles = Tile::all()->map(fn(Tile $tile) => [
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
