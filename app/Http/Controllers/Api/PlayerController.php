<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StorePlayerRequest;
use App\Http\Requests\Api\UpdatePlayerRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Player
 *
 * APIs for managing player profiles
 */
class PlayerController extends Controller
{
    /**
     * Get player profile
     *
     * Get the authenticated user's player profile.
     *
     * @authenticated
     *
     * @response {
     *   "player": {
     *     "id": 1,
     *     "nickname": "CircuitMaster",
     *     "xp": 150,
     *     "created_at": "2024-01-15T10:30:00.000000Z",
     *     "updated_at": "2024-01-15T10:30:00.000000Z"
     *   }
     * }
     * @response 404 scenario="Player not found" {
     *   "message": "Player not found"
     * }
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
     * Create player profile
     *
     * Create a new player profile for the authenticated user. Each user can only have one player.
     *
     * @authenticated
     *
     * @bodyParam nickname string required The player's nickname (unique, 3-20 characters). Example: CircuitMaster
     *
     * @response 201 {
     *   "message": "Player created successfully",
     *   "player": {
     *     "id": 1,
     *     "nickname": "CircuitMaster",
     *     "xp": 0,
     *     "created_at": "2024-01-15T10:30:00.000000Z",
     *     "updated_at": "2024-01-15T10:30:00.000000Z"
     *   }
     * }
     * @response 409 scenario="Player already exists" {
     *   "message": "Player already exists"
     * }
     * @response 422 scenario="Validation error" {
     *   "message": "The nickname has already been taken.",
     *   "errors": {
     *     "nickname": ["The nickname has already been taken."]
     *   }
     * }
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
     * Update player profile
     *
     * Update the authenticated user's player nickname.
     *
     * @authenticated
     *
     * @bodyParam nickname string required The new nickname (unique, 3-20 characters). Example: NewNickname
     *
     * @response {
     *   "message": "Player updated successfully",
     *   "player": {
     *     "id": 1,
     *     "nickname": "NewNickname",
     *     "xp": 150,
     *     "created_at": "2024-01-15T10:30:00.000000Z",
     *     "updated_at": "2024-01-15T12:00:00.000000Z"
     *   }
     * }
     * @response 404 scenario="Player not found" {
     *   "message": "Player not found"
     * }
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
     * Delete player profile
     *
     * Delete the authenticated user's player profile. This will also delete all associated scores.
     *
     * @authenticated
     *
     * @response {
     *   "message": "Player deleted successfully"
     * }
     * @response 404 scenario="Player not found" {
     *   "message": "Player not found"
     * }
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

    /**
     * Get player progress
     *
     * Get the player's progress including all completed levels and scores.
     *
     * @authenticated
     *
     * @response {
     *   "player": {
     *     "nickname": "CircuitMaster",
     *     "total_xp": 150,
     *     "levels_completed": 3
     *   },
     *   "scores": [
     *     {
     *       "level_id": 1,
     *       "level_name": "Tutorial",
     *       "difficulty": "easy",
     *       "xp_earned": 25,
     *       "commands_used": 8,
     *       "completed_at": "2024-01-15T10:30:00.000000Z"
     *     }
     *   ]
     * }
     * @response 404 scenario="Player not found" {
     *   "message": "Player not found"
     * }
     */
    public function progress(Request $request): JsonResponse
    {
        $player = $request->user()->player;

        if (! $player) {
            return response()->json([
                'message' => 'Player not found',
            ], 404);
        }

        $scores = $player->scores()
            ->with('level:id,name,difficulty')
            ->orderBy('completed_at', 'desc')
            ->get()
            ->map(fn ($score) => [
                'level_id' => $score->level_id,
                'level_name' => $score->level->name,
                'difficulty' => $score->level->difficulty->value,
                'xp_earned' => $score->xp_earned,
                'commands_used' => $score->commands_used,
                'completed_at' => $score->completed_at,
            ]);

        return response()->json([
            'player' => [
                'nickname' => $player->nickname,
                'total_xp' => $player->xp,
                'levels_completed' => $scores->count(),
            ],
            'scores' => $scores,
        ]);
    }
}
