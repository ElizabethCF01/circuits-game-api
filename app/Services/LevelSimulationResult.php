<?php

namespace App\Services;

readonly class LevelSimulationResult
{
    public function __construct(
        public bool $success,
        public bool $completed,
        public int $circuitsCollected,
        public int $commandsUsed,
        public ?string $error = null,
    ) {}
}
