<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LevelValidatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LevelValidationController extends Controller
{
    public function __construct(
        private LevelValidatorService $validator
    ) {}

    /**
     * Validate a level configuration
     *
     * POST /api/levels/validate
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
     * Check reachability from a position
     *
     * POST /api/levels/reachability
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
