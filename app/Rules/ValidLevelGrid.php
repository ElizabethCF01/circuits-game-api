<?php

namespace App\Rules;

use App\Services\LevelValidatorService;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidLevelGrid implements ValidationRule, DataAwareRule
{
    protected array $data = [];

    public function __construct(
        private ?LevelValidatorService $validator = null
    ) {
        $this->validator = $validator ?? app(LevelValidatorService::class);
    }

    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Parse tiles if JSON string
        $tiles = is_string($value) ? json_decode($value, true) : $value;

        if (!is_array($tiles)) {
            $fail('Tiles must be a valid array.');
            return;
        }

        $gridWidth = (int) ($this->data['grid_width'] ?? 0);
        $gridHeight = (int) ($this->data['grid_height'] ?? 0);
        $startX = (int) ($this->data['start_x'] ?? 0);
        $startY = (int) ($this->data['start_y'] ?? 0);
        $requiredCircuits = (int) ($this->data['required_circuits'] ?? 0);

        $result = $this->validator->fullValidation([
            'tiles' => $tiles,
            'grid_width' => $gridWidth,
            'grid_height' => $gridHeight,
            'start_x' => $startX,
            'start_y' => $startY,
            'required_circuits' => $requiredCircuits,
        ]);

        if (!$result->isValid) {
            foreach ($result->errors as $error) {
                $fail($error['message']);
            }
        }
    }
}
