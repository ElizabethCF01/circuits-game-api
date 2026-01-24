<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StorePlayerRequest;
use App\Http\Requests\Api\UpdatePlayerRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    /**
     * Get the authenticated user's player
     *
     * GET /api/player
     */
    public function show(Request $request): JsonResponse
    {
        $player = $request->user()->player;

        if (! $player) {
            return response()->json([
                'message' => 'Player not found',
            ], 404);
        }

        return response()->json([
            'player' => [
                'id' => $player->id,
                'nickname' => $player->nickname,
                'xp' => $player->xp,
                'created_at' => $player->created_at,
                'updated_at' => $player->updated_at,
            ],
        ]);
    }

    /**
     * Create a player for the authenticated user
     *
     * POST /api/player
     */
    public function store(StorePlayerRequest $request): JsonResponse
    {
        if ($request->user()->player) {
            return response()->json([
                'message' => 'Player already exists',
            ], 409);
        }

        $player = $request->user()->player()->create([
            'nickname' => $request->nickname,
            'xp' => 0,
        ]);

        return response()->json([
            'message' => 'Player created successfully',
            'player' => [
                'id' => $player->id,
                'nickname' => $player->nickname,
                'xp' => $player->xp,
                'created_at' => $player->created_at,
                'updated_at' => $player->updated_at,
            ],
        ], 201);
    }

    /**
     * Update the authenticated user's player
     *
     * PUT /api/player
     */
    public function update(UpdatePlayerRequest $request): JsonResponse
    {
        $player = $request->user()->player;

        if (! $player) {
            return response()->json([
                'message' => 'Player not found',
            ], 404);
        }

        $player->update([
            'nickname' => $request->nickname,
        ]);

        return response()->json([
            'message' => 'Player updated successfully',
            'player' => [
                'id' => $player->id,
                'nickname' => $player->nickname,
                'xp' => $player->xp,
                'created_at' => $player->created_at,
                'updated_at' => $player->updated_at,
            ],
        ]);
    }

    /**
     * Delete the authenticated user's player
     *
     * DELETE /api/player
     */
    public function destroy(Request $request): JsonResponse
    {
        $player = $request->user()->player;

        if (! $player) {
            return response()->json([
                'message' => 'Player not found',
            ], 404);
        }

        $player->delete();

        return response()->json([
            'message' => 'Player deleted successfully',
        ]);
    }
}
