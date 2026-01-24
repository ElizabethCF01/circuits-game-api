<?php

namespace App\Services;

use App\Enums\Command;
use App\Models\Level;

class LevelSimulatorService
{
    private int $posX;
    private int $posY;
    private array $collectedCircuits = [];
    private int $commandsUsed = 0;

    public function simulate(Level $level, array $commands): LevelSimulationResult
    {
        $this->reset();
        $this->posX = $level->start_x;
        $this->posY = $level->start_y;

        $tiles = $level->tiles;
        $gridWidth = $level->grid_width;
        $gridHeight = $level->grid_height;

        foreach ($commands as $commandValue) {
            $command = Command::tryFrom($commandValue);

            if ($command === null) {
                return new LevelSimulationResult(
                    success: false,
                    completed: false,
                    circuitsCollected: count($this->collectedCircuits),
                    commandsUsed: $this->commandsUsed,
                    error: "Invalid command: {$commandValue}"
                );
            }

            $this->commandsUsed++;
            $this->executeCommand($command, $tiles, $gridWidth, $gridHeight);
        }

        $completed = count($this->collectedCircuits) >= $level->required_circuits;

        return new LevelSimulationResult(
            success: true,
            completed: $completed,
            circuitsCollected: count($this->collectedCircuits),
            commandsUsed: $this->commandsUsed,
            error: $completed ? null : 'Not all required circuits were collected'
        );
    }

    private function executeCommand(Command $command, array $tiles, int $gridWidth, int $gridHeight): void
    {
        match ($command) {
            Command::Up => $this->move(0, -1, $tiles, $gridWidth, $gridHeight),
            Command::Down => $this->move(0, 1, $tiles, $gridWidth, $gridHeight),
            Command::Left => $this->move(-1, 0, $tiles, $gridWidth, $gridHeight),
            Command::Right => $this->move(1, 0, $tiles, $gridWidth, $gridHeight),
            Command::ActivateCircuit => $this->activateCircuit($tiles, $gridWidth),
        };
    }

    private function move(int $dx, int $dy, array $tiles, int $gridWidth, int $gridHeight): void
    {
        $newX = $this->posX + $dx;
        $newY = $this->posY + $dy;

        // Check bounds - if out of bounds, stay in place
        if ($newX < 0 || $newX >= $gridWidth || $newY < 0 || $newY >= $gridHeight) {
            return;
        }

        $newIndex = $this->coordinatesToIndex($newX, $newY, $gridWidth);

        // Check if walkable - if obstacle, stay in place
        if (! isset($tiles[$newIndex]) || ! $this->isWalkable($tiles[$newIndex]['type'] ?? '')) {
            return;
        }

        // Move is valid
        $this->posX = $newX;
        $this->posY = $newY;
    }

    private function activateCircuit(array $tiles, int $gridWidth): void
    {
        $currentIndex = $this->coordinatesToIndex($this->posX, $this->posY, $gridWidth);
        $tile = $tiles[$currentIndex] ?? null;

        // Check if standing on a circuit and haven't collected it yet
        if ($tile !== null
            && ($tile['type'] ?? '') === 'circuit'
            && ! in_array($currentIndex, $this->collectedCircuits, true)
        ) {
            $this->collectedCircuits[] = $currentIndex;
        }
    }

    private function coordinatesToIndex(int $x, int $y, int $width): int
    {
        return $y * $width + $x;
    }

    private function isWalkable(string $tileType): bool
    {
        return in_array($tileType, ['empty', 'circuit'], true);
    }

    private function reset(): void
    {
        $this->posX = 0;
        $this->posY = 0;
        $this->collectedCircuits = [];
        $this->commandsUsed = 0;
    }
}
