<?php

namespace App\Enums;

enum Command: string
{
    case Left = 'left';
    case Right = 'right';
    case Up = 'up';
    case Down = 'down';
    case ActivateCircuit = 'activate_circuit';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
