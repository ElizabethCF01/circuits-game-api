<?php

namespace App\Livewire;

use App\Models\Tile;
use Livewire\Component;
use Livewire\Attributes\Modelable;

class GridEditor extends Component
{
    #[Modelable]
    public array $tiles = [];

    public int $gridWidth = 5;
    public int $gridHeight = 5;

    public string $selectedTileType = 'empty';
    public ?int $selectedTileId = null;

    public array $availableTiles = [];

    public function mount(array $tiles = [], int $gridWidth = 5, int $gridHeight = 5): void
    {
        $this->gridWidth = $gridWidth;
        $this->gridHeight = $gridHeight;
        $this->tiles = $tiles;

        $this->loadAvailableTiles();
        $this->initializeGrid();
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
    }

    public function updateGridSize(): void
    {
        // Clamp values
        $this->gridWidth = max(1, min(20, $this->gridWidth));
        $this->gridHeight = max(1, min(20, $this->gridHeight));

        $this->initializeGrid();
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

    public function render()
    {
        return view('livewire.grid-editor');
    }
}
