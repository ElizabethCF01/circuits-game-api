<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CompleteLevelRequest;
use App\Models\Level;
use App\Models\Score;
use App\Services\LevelSimulatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Levels
 *
 * APIs for browsing and completing game levels
 */
class LevelController extends Controller
{
    public function __construct(
        private LevelSimulatorService $simulator
    ) {}

    /**
     * List all levels
     *
     * Get a paginated list of all public levels with optional search and filtering.
     * If a valid Sanctum token is provided, each level will include `played` and `score`
     * fields indicating whether the authenticated player has completed the level.
     *
     * @queryParam search string Search by name or description. Example: tutorial
     * @queryParam difficulty string Filter by difficulty (easy, medium, hard). Example: easy
     * @queryParam per_page integer Items per page (default 15, max 100). Example: 15
     * @queryParam page integer Page number. Example: 1
     *
     * @response {
     *   "levels": [
     *     {
     *       "id": 1,
     *       "name": "Tutorial",
     *       "description": "Learn the basics",
     *       "difficulty": "easy",
     *       "required_circuits": 3,
     *       "max_commands": 10,
     *       "grid_width": 5,
     *       "grid_height": 5,
     *       "played": true,
     *       "score": {
     *         "xp_earned": 25,
     *         "commands_used": 4,
     *         "completed_at": "2026-01-28T10:30:00.000000Z"
     *       }
     *     }
     *   ],
     *   "pagination": {
     *     "current_page": 1,
     *     "last_page": 1,
     *     "per_page": 15,
     *     "total": 1
     *   }
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $query = Level::where('is_public', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        $perPage = min((int) $request->get('per_page', 15), 100);

        $paginator = $query->orderBy('difficulty')
            ->orderBy('name')
            ->paginate($perPage);

        $player = auth('sanctum')->user()?->player;
        $scoresByLevel = collect();

        if ($player) {
            $levelIds = collect($paginator->items())->pluck('id');
            $scoresByLevel = Score::where('player_id', $player->id)
                ->whereIn('level_id', $levelIds)
                ->get()
                ->keyBy('level_id');
        }

        $levels = collect($paginator->items())->map(function (Level $level) use ($scoresByLevel) {
            $score = $scoresByLevel->get($level->id);

            return [
                'id' => $level->id,
                'name' => $level->name,
                'description' => $level->description,
                'difficulty' => $level->difficulty->value,
                'required_circuits' => $level->required_circuits,
                'max_commands' => $level->max_commands,
                'grid_width' => $level->grid_width,
                'grid_height' => $level->grid_height,
                'played' => $score !== null,
                'score' => $score ? [
                    'xp_earned' => $score->xp_earned,
                    'commands_used' => $score->commands_used,
                    'completed_at' => $score->completed_at,
                ] : null,
            ];
        });

        return response()->json([
            'levels' => $levels,
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    /**
     * Get level details
     *
     * Get full details of a specific level including the tile grid.
     *
     * @unauthenticated
     *
     * @urlParam level integer required The level ID. Example: 1
     *
     * @response {
     *   "level": {
     *     "id": 1,
     *     "name": "Tutorial",
     *     "description": "Learn the basics",
     *     "difficulty": "easy",
     *     "start_x": 0,
     *     "start_y": 0,
     *     "required_circuits": 3,
     *     "max_commands": 10,
     *     "grid_width": 5,
     *     "grid_height": 5,
     *     "tiles": [[1, 1, 2], [1, 3, 1], [1, 1, 1]]
     *   }
     * }
     * @response 404 scenario="Level not found" {
     *   "message": "Level not found"
     * }
     */
    public function show(Level $level): JsonResponse
    {
        if (! $level->is_public) {
            return response()->json([
                'message' => 'Level not found',
            ], 404);
        }

        return response()->json([
            'level' => [
                'id' => $level->id,
                'name' => $level->name,
                'description' => $level->description,
                'difficulty' => $level->difficulty->value,
                'start_x' => $level->start_x,
                'start_y' => $level->start_y,
                'required_circuits' => $level->required_circuits,
                'max_commands' => $level->max_commands,
                'grid_width' => $level->grid_width,
                'grid_height' => $level->grid_height,
                'tiles' => $level->tiles,
            ],
        ]);
    }

    /**
     * Complete a level
     *
     * Submit commands to complete a level. The commands are simulated and if the player collects
     * all required circuits, XP is awarded based on difficulty and efficiency.
     *
     * @authenticated
     *
     * @urlParam level integer required The level ID. Example: 1
     *
     * @bodyParam commands string[] required Array of commands to execute. Allowed: left, right, up, down, activate_circuit. Example: ["right", "right", "down", "activate_circuit"]
     *
     * @response 201 scenario="First completion" {
     *   "message": "Level completed!",
     *   "first_completion": true,
     *   "commands_used": 4,
     *   "xp_earned": 25,
     *   "base_xp": 20,
     *   "efficiency_bonus": 5,
     *   "player_total_xp": 175
     * }
     * @response scenario="Improved score" {
     *   "message": "New best score!",
     *   "improved": true,
     *   "commands_used": 3,
     *   "xp_earned": 27,
     *   "xp_bonus": 2,
     *   "player_total_xp": 177
     * }
     * @response scenario="Not improved" {
     *   "message": "Level completed, but not a new best score",
     *   "improved": false,
     *   "commands_used": 5,
     *   "best_commands": 3
     * }
     * @response 422 scenario="Level not completed" {
     *   "message": "Level not completed",
     *   "circuits_collected": 2,
     *   "circuits_required": 3,
     *   "commands_used": 10
     * }
     * @response 422 scenario="Simulation failed" {
     *   "message": "Simulation failed",
     *   "error": "Invalid command: jump"
     * }
     * @response 400 scenario="No player profile" {
     *   "message": "Player profile required. Create a player first."
     * }
     */
    public function complete(CompleteLevelRequest $request, Level $level): JsonResponse
    {
        if (! $level->is_public) {
            return response()->json([
                'message' => 'Level not found',
            ], 404);
        }

        $player = $request->user()->player;

        if (! $player) {
            return response()->json([
                'message' => 'Player profile required. Create a player first.',
            ], 400);
        }

        $result = $this->simulator->simulate($level, $request->commands);

        if (! $result->success) {
            return response()->json([
                'message' => 'Simulation failed',
                'error' => $result->error,
            ], 422);
        }

        if (! $result->completed) {
            return response()->json([
                'message' => 'Level not completed',
                'circuits_collected' => $result->circuitsCollected,
                'circuits_required' => $level->required_circuits,
                'commands_used' => $result->commandsUsed,
            ], 422);
        }

        $baseXp = $level->difficulty->baseXp();
        $efficiencyBonus = 0;

        if ($level->max_commands > 0 && $result->commandsUsed < $level->max_commands) {
            $efficiency = ($level->max_commands - $result->commandsUsed) / $level->max_commands;
            $efficiencyBonus = (int) round($efficiency * ($baseXp / 2));
        }

        $totalXp = $baseXp + $efficiencyBonus;
        $existingScore = $player->scores()->where('level_id', $level->id)->first();

        if ($existingScore) {
            if ($result->commandsUsed < $existingScore->commands_used) {
                $xpDifference = $totalXp - $existingScore->xp_earned;

                $existingScore->update([
                    'commands_used' => $result->commandsUsed,
                    'xp_earned' => $totalXp,
                ]);

                if ($xpDifference > 0) {
                    $player->increment('xp', $xpDifference);
                }

                return response()->json([
                    'message' => 'New best score!',
                    'improved' => true,
                    'commands_used' => $result->commandsUsed,
                    'xp_earned' => $totalXp,
                    'xp_bonus' => $xpDifference > 0 ? $xpDifference : 0,
                    'player_total_xp' => $player->fresh()->xp,
                ]);
            }

            return response()->json([
                'message' => 'Level completed, but not a new best score',
                'improved' => false,
                'commands_used' => $result->commandsUsed,
                'best_commands' => $existingScore->commands_used,
            ]);
        }

        $player->scores()->create([
            'level_id' => $level->id,
            'xp_earned' => $totalXp,
            'commands_used' => $result->commandsUsed,
            'completed_at' => now(),
        ]);

        $player->increment('xp', $totalXp);

        return response()->json([
            'message' => 'Level completed!',
            'first_completion' => true,
            'commands_used' => $result->commandsUsed,
            'xp_earned' => $totalXp,
            'base_xp' => $baseXp,
            'efficiency_bonus' => $efficiencyBonus,
            'player_total_xp' => $player->fresh()->xp,
        ], 201);
    }
}
