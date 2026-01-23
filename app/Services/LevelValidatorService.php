<?php

namespace App\Services;

use SplQueue;

class LevelValidatorService
{
    private const VALID_TILE_TYPES = ['empty', 'circuit', 'obstacle'];

    private const DIRECTIONS = [
        [0, -1],  // up
        [0, 1],   // down
        [-1, 0],  // left
        [1, 0],   // right
    ];

    private array $errors = [];
    private array $warnings = [];
    private array $metadata = [];

    /**
     * Full validation of a level configuration
     */
    public function fullValidation(array $levelData): LevelValidationResult
    {
        $this->reset();

        $tiles = $levelData['tiles'] ?? [];
        $gridWidth = (int) ($levelData['grid_width'] ?? 0);
        $gridHeight = (int) ($levelData['grid_height'] ?? 0);
        $startX = (int) ($levelData['start_x'] ?? 0);
        $startY = (int) ($levelData['start_y'] ?? 0);
        $requiredCircuits = (int) ($levelData['required_circuits'] ?? 0);

        // Validate grid structure
        $this->validateGridStructure($tiles, $gridWidth, $gridHeight);

        // Validate tile types
        $this->validateTileTypes($tiles);

        // Validate start position bounds
        $this->validateStartPositionBounds($startX, $startY, $gridWidth, $gridHeight);

        // Validate start position is walkable
        if (empty($this->errors)) {
            $this->validateStartPositionWalkable($startX, $startY, $tiles, $gridWidth);
        }

        // Count and validate circuits
        $circuitCount = $this->countCircuits($tiles);
        $this->metadata['circuit_count'] = $circuitCount;
        $this->validateRequiredCircuits($requiredCircuits, $circuitCount);

        // Validate circuit reachability (only if no structural errors)
        if (empty($this->errors)) {
            $reachabilityResult = $this->findReachableTiles($startX, $startY, $tiles, $gridWidth, $gridHeight);
            $this->metadata['reachable_tiles'] = count($reachabilityResult['reachable']);
            $this->metadata['unreachable_circuits'] = $reachabilityResult['unreachable_circuits'];
            $this->metadata['circuit_distances'] = $reachabilityResult['circuit_distances'];

            $reachableCircuits = array_filter(
                $reachabilityResult['reachable'],
                fn($index) => isset($tiles[$index]) && $tiles[$index]['type'] === 'circuit'
            );
            $this->metadata['reachable_circuits'] = count($reachableCircuits);

            // Calculate suggested max commands
            $this->metadata['suggested_max_commands'] = $this->calculateSuggestedMaxCommands(
                $reachabilityResult['circuit_distances'],
                $requiredCircuits
            );

            if (!empty($reachabilityResult['unreachable_circuits'])) {
                $count = count($reachabilityResult['unreachable_circuits']);
                $this->addError(
                    'circuits_unreachable',
                    "{$count} circuit(s) cannot be reached from the start position."
                );
            }
        }

        return new LevelValidationResult(
            isValid: empty($this->errors),
            errors: $this->errors,
            warnings: $this->warnings,
            metadata: $this->metadata
        );
    }

    /**
     * Validate grid has correct number of tiles
     */
    public function validateGridStructure(array $tiles, int $gridWidth, int $gridHeight): bool
    {
        $expectedTiles = $gridWidth * $gridHeight;
        $actualTiles = count($tiles);

        if ($expectedTiles === 0) {
            $this->addError('grid_empty', 'The grid cannot be empty.');
            return false;
        }

        if ($actualTiles !== $expectedTiles) {
            $this->addError(
                'grid_structure_invalid',
                "Grid structure is invalid. Expected {$expectedTiles} tiles but got {$actualTiles}."
            );
            return false;
        }

        return true;
    }

    /**
     * Validate all tiles have valid types
     */
    public function validateTileTypes(array $tiles): bool
    {
        $valid = true;

        foreach ($tiles as $index => $tile) {
            $type = $tile['type'] ?? null;

            if (!in_array($type, self::VALID_TILE_TYPES, true)) {
                $typeStr = $type ?? 'null';
                $this->addError(
                    'tile_type_invalid',
                    "Invalid tile type '{$typeStr}' at position {$index}."
                );
                $valid = false;
            }
        }

        return $valid;
    }

    /**
     * Validate start position is within grid bounds
     */
    public function validateStartPositionBounds(int $startX, int $startY, int $gridWidth, int $gridHeight): bool
    {
        $valid = true;

        if ($startX < 0 || $startX >= $gridWidth) {
            $max = $gridWidth - 1;
            $this->addError(
                'start_x_out_of_bounds',
                "Start X position ({$startX}) is out of bounds. Must be between 0 and {$max}."
            );
            $valid = false;
        }

        if ($startY < 0 || $startY >= $gridHeight) {
            $max = $gridHeight - 1;
            $this->addError(
                'start_y_out_of_bounds',
                "Start Y position ({$startY}) is out of bounds. Must be between 0 and {$max}."
            );
            $valid = false;
        }

        return $valid;
    }

    /**
     * Validate start position is not on an obstacle
     */
    public function validateStartPositionWalkable(int $startX, int $startY, array $tiles, int $gridWidth): bool
    {
        $index = $this->coordinatesToIndex($startX, $startY, $gridWidth);
        $tile = $tiles[$index] ?? null;

        if ($tile === null || !$this->isWalkable($tile['type'] ?? '')) {
            $this->addError('start_on_obstacle', 'Start position cannot be on an obstacle.');
            return false;
        }

        return true;
    }

    /**
     * Count circuits in the grid
     */
    public function countCircuits(array $tiles): int
    {
        return count(array_filter($tiles, fn($tile) => ($tile['type'] ?? '') === 'circuit'));
    }

    /**
     * Validate required circuits doesn't exceed available
     */
    public function validateRequiredCircuits(int $requiredCircuits, int $availableCircuits): bool
    {
        if ($requiredCircuits > 0 && $availableCircuits === 0) {
            $this->addError('no_circuits_in_grid', 'No circuits placed in the grid.');
            return false;
        }

        if ($requiredCircuits > $availableCircuits) {
            $this->addError(
                'required_circuits_exceeds_available',
                "Required circuits ({$requiredCircuits}) exceeds available circuits ({$availableCircuits})."
            );
            return false;
        }

        return true;
    }

    /**
     * BFS algorithm to find all reachable tiles from start position
     * Also calculates distances to each tile
     */
    public function findReachableTiles(int $startX, int $startY, array $tiles, int $gridWidth, int $gridHeight): array
    {
        $startIndex = $this->coordinatesToIndex($startX, $startY, $gridWidth);

        // Check if start position is valid
        if (!isset($tiles[$startIndex]) || !$this->isWalkable($tiles[$startIndex]['type'] ?? '')) {
            return [
                'reachable' => [],
                'unreachable_circuits' => $this->getAllCircuitIndices($tiles),
                'distances' => [],
                'circuit_distances' => [],
            ];
        }

        $visited = [];
        $distances = [];
        $queue = new SplQueue();
        $queue->enqueue([$startX, $startY, 0]); // x, y, distance
        $visited[$startIndex] = true;
        $distances[$startIndex] = 0;
        $reachable = [$startIndex];

        while (!$queue->isEmpty()) {
            [$currentX, $currentY, $currentDist] = $queue->dequeue();

            foreach (self::DIRECTIONS as [$dx, $dy]) {
                $newX = $currentX + $dx;
                $newY = $currentY + $dy;

                // Check bounds
                if ($newX < 0 || $newX >= $gridWidth || $newY < 0 || $newY >= $gridHeight) {
                    continue;
                }

                $newIndex = $this->coordinatesToIndex($newX, $newY, $gridWidth);

                // Check if already visited
                if (isset($visited[$newIndex])) {
                    continue;
                }

                // Check if walkable
                if (!isset($tiles[$newIndex]) || !$this->isWalkable($tiles[$newIndex]['type'] ?? '')) {
                    continue;
                }

                $visited[$newIndex] = true;
                $distances[$newIndex] = $currentDist + 1;
                $reachable[] = $newIndex;
                $queue->enqueue([$newX, $newY, $currentDist + 1]);
            }
        }

        // Find unreachable circuits and circuit distances
        $allCircuits = $this->getAllCircuitIndices($tiles);
        $unreachableCircuits = array_values(array_diff($allCircuits, $reachable));

        $circuitDistances = [];
        foreach ($allCircuits as $circuitIndex) {
            if (isset($distances[$circuitIndex])) {
                $circuitDistances[$circuitIndex] = $distances[$circuitIndex];
            }
        }

        return [
            'reachable' => $reachable,
            'unreachable_circuits' => $unreachableCircuits,
            'distances' => $distances,
            'circuit_distances' => $circuitDistances,
        ];
    }

    /**
     * Calculate suggested max commands based on circuit distances
     */
    public function calculateSuggestedMaxCommands(array $circuitDistances, int $requiredCircuits): int
    {
        if (empty($circuitDistances) || $requiredCircuits === 0) {
            return 1;
        }

        // Sort distances and take the required number of closest circuits
        $sortedDistances = array_values($circuitDistances);
        sort($sortedDistances);

        $distancesToCollect = array_slice($sortedDistances, 0, $requiredCircuits);

        // Sum of distances to required circuits
        $totalDistance = array_sum($distancesToCollect);

        // Add a margin: minimum moves + 50% buffer for non-optimal paths
        $suggestedCommands = (int) ceil($totalDistance * 1.5);

        // Minimum of 3 commands
        return max(3, $suggestedCommands);
    }

    /**
     * Quick validation for API - returns validation result
     */
    public function validate(array $levelData): LevelValidationResult
    {
        return $this->fullValidation($levelData);
    }

    /**
     * Convert x,y coordinates to array index
     */
    private function coordinatesToIndex(int $x, int $y, int $width): int
    {
        return $y * $width + $x;
    }

    /**
     * Convert array index to x,y coordinates
     */
    public function indexToCoordinates(int $index, int $width): array
    {
        return [
            'x' => $index % $width,
            'y' => intdiv($index, $width),
        ];
    }

    /**
     * Check if tile type is walkable
     */
    private function isWalkable(string $tileType): bool
    {
        return in_array($tileType, ['empty', 'circuit'], true);
    }

    /**
     * Get all circuit indices in the grid
     */
    private function getAllCircuitIndices(array $tiles): array
    {
        $circuits = [];
        foreach ($tiles as $index => $tile) {
            if (($tile['type'] ?? '') === 'circuit') {
                $circuits[] = $index;
            }
        }
        return $circuits;
    }

    /**
     * Add an error to the list
     */
    private function addError(string $key, string $message): void
    {
        $this->errors[] = [
            'key' => $key,
            'message' => $message,
        ];
    }

    /**
     * Add a warning to the list
     */
    private function addWarning(string $key, string $message): void
    {
        $this->warnings[] = [
            'key' => $key,
            'message' => $message,
        ];
    }

    /**
     * Reset validation state
     */
    private function reset(): void
    {
        $this->errors = [];
        $this->warnings = [];
        $this->metadata = [];
    }
}
