<?php

namespace App\Livewire;

use App\Models\Tile;
use App\Services\LevelValidatorService;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\Computed;

class GridEditor extends Component
{
    #[Modelable]
    public array $tiles = [];

    public int $gridWidth = 5;
    public int $gridHeight = 5;

    // Start position for validation
    public int $startX = 0;
    public int $startY = 0;
    public int $requiredCircuits = 1;

    public string $selectedTileType = 'empty';
    public ?int $selectedTileId = null;

    public array $availableTiles = [];

    // Validation state
    public array $validationErrors = [];
    public array $validationWarnings = [];
    public array $unreachableTileIndices = [];
    public int $suggestedMaxCommands = 3;

    public function mount(
        array $tiles = [],
        int $gridWidth = 5,
        int $gridHeight = 5,
        int $startX = 0,
        int $startY = 0,
        int $requiredCircuits = 1
    ): void {
        $this->gridWidth = $gridWidth;
        $this->gridHeight = $gridHeight;
        $this->tiles = $tiles;
        $this->startX = $startX;
        $this->startY = $startY;
        $this->requiredCircuits = $requiredCircuits;

        $this->loadAvailableTiles();
        $this->initializeGrid();
        $this->validateGrid(); // Calculate initial suggested max commands
    }

    public function loadAvailableTiles(): void
    {
        $tiles = Tile::all();

        foreach ($tiles as $tile) {
            $this->availableTiles[$tile->type] = [
                'id' => $tile->id,
                'type' => $tile->type,
                'image' => $tile->getFirstMediaUrl('images'),
            ];
        }

        // Set default selected tile
        if (isset($this->availableTiles['empty'])) {
            $this->selectedTileId = $this->availableTiles['empty']['id'];
        }
    }

    public function initializeGrid(): void
    {
        $totalCells = $this->gridWidth * $this->gridHeight;
        $isNewGrid = empty($this->tiles);

        // Keep existing tiles if we have them, fill rest with empty
        if (count($this->tiles) < $totalCells) {
            $emptyTileId = $this->availableTiles['empty']['id'] ?? null;

            for ($i = count($this->tiles); $i < $totalCells; $i++) {
                $this->tiles[] = [
                    'type' => 'empty',
                    'tile_id' => $emptyTileId,
                ];
            }
        }

        // Trim if grid got smaller
        if (count($this->tiles) > $totalCells) {
            $this->tiles = array_slice($this->tiles, 0, $totalCells);
        }

        // For new grids, place a circuit near the start position
        if ($isNewGrid && $this->requiredCircuits > 0) {
            $this->placeInitialCircuit();
        }
    }

    /**
     * Place an initial circuit near the start position for new grids
     */
    private function placeInitialCircuit(): void
    {
        $circuitTileId = $this->availableTiles['circuit']['id'] ?? null;
        if ($circuitTileId === null) {
            return;
        }

        $startIndex = $this->startY * $this->gridWidth + $this->startX;

        // Try to place circuit adjacent to start (right, down, left, up)
        $adjacentPositions = [
            [$this->startX + 1, $this->startY],     // right
            [$this->startX, $this->startY + 1],     // down
            [$this->startX - 1, $this->startY],     // left
            [$this->startX, $this->startY - 1],     // up
            [$this->startX + 1, $this->startY + 1], // diagonal
        ];

        foreach ($adjacentPositions as [$x, $y]) {
            if ($x >= 0 && $x < $this->gridWidth && $y >= 0 && $y < $this->gridHeight) {
                $index = $y * $this->gridWidth + $x;
                // Don't place on start position
                if ($index !== $startIndex) {
                    $this->tiles[$index] = [
                        'type' => 'circuit',
                        'tile_id' => $circuitTileId,
                    ];
                    return;
                }
            }
        }
    }

    public function selectTile(string $type): void
    {
        $this->selectedTileType = $type;
        $this->selectedTileId = $this->availableTiles[$type]['id'] ?? null;
    }

    public function paintCell(int $index): void
    {
        if ($index < 0 || $index >= count($this->tiles)) {
            return;
        }

        $this->tiles[$index] = [
            'type' => $this->selectedTileType,
            'tile_id' => $this->selectedTileId,
        ];

        $this->validateGrid();
    }

    public function updateGridSize(): void
    {
        // Clamp values
        $this->gridWidth = max(1, min(20, $this->gridWidth));
        $this->gridHeight = max(1, min(20, $this->gridHeight));

        // Adjust start position if out of bounds
        $this->startX = min($this->startX, $this->gridWidth - 1);
        $this->startY = min($this->startY, $this->gridHeight - 1);

        $this->initializeGrid();
        $this->validateGrid();
    }

    public function clearGrid(): void
    {
        $emptyTileId = $this->availableTiles['empty']['id'] ?? null;
        $totalCells = $this->gridWidth * $this->gridHeight;

        $this->tiles = [];
        for ($i = 0; $i < $totalCells; $i++) {
            $this->tiles[] = [
                'type' => 'empty',
                'tile_id' => $emptyTileId,
            ];
        }

        $this->validateGrid();
    }

    public function getTileImage(int $index): string
    {
        $type = $this->tiles[$index]['type'] ?? 'empty';
        return $this->availableTiles[$type]['image'] ?? '';
    }

    public function getCircuitCount(): int
    {
        return count(array_filter($this->tiles, fn($tile) => $tile['type'] === 'circuit'));
    }

    /**
     * Update start position from external input
     */
    #[On('update-start-position')]
    public function updateStartPosition(int $x, int $y): void
    {
        $this->startX = max(0, min($x, $this->gridWidth - 1));
        $this->startY = max(0, min($y, $this->gridHeight - 1));
        $this->validateGrid();
    }

    /**
     * Update required circuits from external input
     */
    #[On('update-required-circuits')]
    public function updateRequiredCircuits(int $count): void
    {
        $this->requiredCircuits = max(0, $count);
        $this->validateGrid();
    }

    /**
     * Validate the current grid configuration
     */
    public function validateGrid(): void
    {
        $validator = new LevelValidatorService();

        $result = $validator->fullValidation([
            'tiles' => $this->tiles,
            'grid_width' => $this->gridWidth,
            'grid_height' => $this->gridHeight,
            'start_x' => $this->startX,
            'start_y' => $this->startY,
            'required_circuits' => $this->requiredCircuits,
        ]);

        $this->validationErrors = $result->errors;
        $this->validationWarnings = $result->warnings;
        $this->unreachableTileIndices = $result->metadata['unreachable_circuits'] ?? [];
        $this->suggestedMaxCommands = $result->metadata['suggested_max_commands'] ?? 3;

        // Dispatch event for parent component/form
        $this->dispatch('grid-validated', [
            'valid' => $result->isValid,
            'errors' => $result->errors,
            'warnings' => $result->warnings,
            'circuitCount' => $result->metadata['circuit_count'] ?? 0,
            'reachableCircuits' => $result->metadata['reachable_circuits'] ?? 0,
            'suggestedMaxCommands' => $this->suggestedMaxCommands,
        ]);
    }

    #[Computed]
    public function hasValidationIssues(): bool
    {
        return !empty($this->validationErrors) || !empty($this->validationWarnings);
    }

    #[Computed]
    public function startPositionIndex(): int
    {
        return $this->startY * $this->gridWidth + $this->startX;
    }

    /**
     * Check if a tile index is unreachable
     */
    public function isUnreachable(int $index): bool
    {
        return in_array($index, $this->unreachableTileIndices, true);
    }

    /**
     * Check if a tile index is the start position
     */
    public function isStartPosition(int $index): bool
    {
        return $index === $this->startPositionIndex;
    }

    public function render()
    {
        return view('livewire.grid-editor');
    }
}
