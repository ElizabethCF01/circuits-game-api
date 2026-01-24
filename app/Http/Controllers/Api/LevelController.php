<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CompleteLevelRequest;
use App\Models\Level;
use App\Services\LevelSimulatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    public function __construct(
        private LevelSimulatorService $simulator
    ) {}

    /**
     * List all public levels
     *
     * GET /api/levels
     *
     * Query params:
     * - search: Search by name or description
     * - difficulty: Filter by difficulty (easy, medium, hard)
     * - per_page: Items per page (default 15, max 100)
     * - page: Page number
     */
    public function index(Request $request): JsonResponse
    {
        $query = Level::where('is_public', true);

        // Search by name or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by difficulty
        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        // Pagination
        $perPage = min((int) $request->get('per_page', 15), 100);

        $paginator = $query->orderBy('difficulty')
            ->orderBy('name')
            ->paginate($perPage);

        $levels = collect($paginator->items())->map(fn(Level $level) => [
            'id' => $level->id,
            'name' => $level->name,
            'description' => $level->description,
            'difficulty' => $level->difficulty->value,
            'required_circuits' => $level->required_circuits,
            'max_commands' => $level->max_commands,
            'grid_width' => $level->grid_width,
            'grid_height' => $level->grid_height,
        ]);

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
     * Get a single level's details
     *
     * GET /api/levels/{level}
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
     * Submit level completion
     *
     * POST /api/levels/{level}/complete
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

        // Simulate the level with provided commands
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

        // Calculate XP
        $baseXp = $level->difficulty->baseXp();
        $efficiencyBonus = 0;

        if ($level->max_commands > 0 && $result->commandsUsed < $level->max_commands) {
            $efficiency = ($level->max_commands - $result->commandsUsed) / $level->max_commands;
            $efficiencyBonus = (int) round($efficiency * ($baseXp / 2));
        }

        $totalXp = $baseXp + $efficiencyBonus;

        // Check for existing score
        $existingScore = $player->scores()->where('level_id', $level->id)->first();

        if ($existingScore) {
            // Update only if better (fewer commands)
            if ($result->commandsUsed < $existingScore->commands_used) {
                $xpDifference = $totalXp - $existingScore->xp_earned;

                $existingScore->update([
                    'commands_used' => $result->commandsUsed,
                    'xp_earned' => $totalXp,
                ]);

                // Update player's total XP
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

        // Create new score
        $player->scores()->create([
            'level_id' => $level->id,
            'xp_earned' => $totalXp,
            'commands_used' => $result->commandsUsed,
            'completed_at' => now(),
        ]);

        // Add XP to player
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
