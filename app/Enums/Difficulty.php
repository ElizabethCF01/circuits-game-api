<?php

namespace App\Enums;

enum Difficulty: string
{
    case Easy = 'easy';
    case Medium = 'medium';
    case Hard = 'hard';

    public function baseXp(): int
    {
        return match ($this) {
            self::Easy => 20,
            self::Medium => 50,
            self::Hard => 100,
        };
    }
}
