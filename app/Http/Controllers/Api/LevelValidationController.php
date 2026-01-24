<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LevelValidatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Level Validation
 *
 * APIs for validating level configurations (used by level editor)
 */
class LevelValidationController extends Controller
{
    public function __construct(
        private LevelValidatorService $validator
    ) {}

    /**
     * Validate level configuration
     *
     * Validate a level configuration including grid dimensions, tile placement, and circuit reachability.
     *
     * @unauthenticated
     *
     * @bodyParam tiles object[] required Array of tile objects. Example: [{"type": "empty"}, {"type": "circuit"}, {"type": "obstacle"}]
     * @bodyParam tiles[].type string required Tile type: empty, circuit, or obstacle. Example: empty
     * @bodyParam grid_width integer required Grid width (1-20). Example: 5
     * @bodyParam grid_height integer required Grid height (1-20). Example: 5
     * @bodyParam start_x integer required Player start X position. Example: 0
     * @bodyParam start_y integer required Player start Y position. Example: 0
     * @bodyParam required_circuits integer required Number of circuits required to complete. Example: 3
     *
     * @response {
     *   "valid": true,
     *   "errors": [],
     *   "warnings": [],
     *   "metadata": {
     *     "total_tiles": 25,
     *     "circuit_count": 3,
     *     "reachable_circuits": 3,
     *     "unreachable_circuits": []
     *   }
     * }
     * @response 422 scenario="Validation failed" {
     *   "valid": false,
     *   "errors": ["Start position is outside grid boundaries"],
     *   "warnings": ["Circuit at position 12 is unreachable"],
     *   "metadata": {
     *     "total_tiles": 25,
     *     "circuit_count": 3,
     *     "reachable_circuits": 2,
     *     "unreachable_circuits": [12]
     *   }
     * }
     */
    public function validate(Request $request): JsonResponse
    {
        $request->validate([
            'tiles' => 'required|array',
            'tiles.*.type' => 'required|string|in:empty,circuit,obstacle',
            'grid_width' => 'required|integer|min:1|max:20',
            'grid_height' => 'required|integer|min:1|max:20',
            'start_x' => 'required|integer|min:0',
            'start_y' => 'required|integer|min:0',
            'required_circuits' => 'required|integer|min:0',
        ]);

        $result = $this->validator->fullValidation($request->all());

        return response()->json([
            'valid' => $result->isValid,
            'errors' => $result->errors,
            'warnings' => $result->warnings,
            'metadata' => [
                'total_tiles' => count($request->input('tiles', [])),
                'circuit_count' => $result->metadata['circuit_count'] ?? 0,
                'reachable_circuits' => $result->metadata['reachable_circuits'] ?? 0,
                'unreachable_circuits' => $result->metadata['unreachable_circuits'] ?? [],
            ],
        ], $result->isValid ? 200 : 422);
    }

    /**
     * Check tile reachability
     *
     * Check which tiles are reachable from the start position and identify any unreachable circuits.
     *
     * @unauthenticated
     *
     * @bodyParam tiles object[] required Array of tile objects. Example: [{"type": "empty"}, {"type": "circuit"}, {"type": "obstacle"}]
     * @bodyParam tiles[].type string required Tile type: empty, circuit, or obstacle. Example: empty
     * @bodyParam grid_width integer required Grid width (1-20). Example: 5
     * @bodyParam grid_height integer required Grid height (1-20). Example: 5
     * @bodyParam start_x integer required Start X position for reachability check. Example: 0
     * @bodyParam start_y integer required Start Y position for reachability check. Example: 0
     *
     * @response {
     *   "reachable_count": 20,
     *   "reachable_indices": [0, 1, 2, 3, 5, 6, 7, 10, 11, 12],
     *   "unreachable_circuits": [8, 16]
     * }
     */
    public function checkReachability(Request $request): JsonResponse
    {
        $request->validate([
            'tiles' => 'required|array',
            'tiles.*.type' => 'required|string|in:empty,circuit,obstacle',
            'grid_width' => 'required|integer|min:1|max:20',
            'grid_height' => 'required|integer|min:1|max:20',
            'start_x' => 'required|integer|min:0',
            'start_y' => 'required|integer|min:0',
        ]);

        $reachability = $this->validator->findReachableTiles(
            $request->input('start_x'),
            $request->input('start_y'),
            $request->input('tiles'),
            $request->input('grid_width'),
            $request->input('grid_height')
        );

        return response()->json([
            'reachable_count' => count($reachability['reachable']),
            'reachable_indices' => $reachability['reachable'],
            'unreachable_circuits' => $reachability['unreachable_circuits'],
        ]);
    }
}
