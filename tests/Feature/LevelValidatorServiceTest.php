<?php

use App\Services\LevelValidatorService;

beforeEach(function () {
    $this->validator = new LevelValidatorService();
});

test('validates a valid level', function () {
    $levelData = [
        'tiles' => [
            ['type' => 'empty', 'tile_id' => 1],
            ['type' => 'circuit', 'tile_id' => 2],
            ['type' => 'empty', 'tile_id' => 1],
            ['type' => 'empty', 'tile_id' => 1],
        ],
        'grid_width' => 2,
        'grid_height' => 2,
        'start_x' => 0,
        'start_y' => 0,
        'required_circuits' => 1,
    ];

    $result = $this->validator->fullValidation($levelData);

    expect($result->isValid)->toBeTrue();
    expect($result->errors)->toBeEmpty();
});

test('detects start position out of bounds', function () {
    $levelData = [
        'tiles' => [
            ['type' => 'empty', 'tile_id' => 1],
            ['type' => 'empty', 'tile_id' => 1],
            ['type' => 'empty', 'tile_id' => 1],
            ['type' => 'empty', 'tile_id' => 1],
        ],
        'grid_width' => 2,
        'grid_height' => 2,
        'start_x' => 5,
        'start_y' => 0,
        'required_circuits' => 0,
    ];

    $result = $this->validator->fullValidation($levelData);

    expect($result->isValid)->toBeFalse();
    expect($result->errors)->toHaveCount(1);
    expect($result->errors[0]['key'])->toBe('start_x_out_of_bounds');
});

test('detects start position on obstacle', function () {
    $levelData = [
        'tiles' => [
            ['type' => 'obstacle', 'tile_id' => 3],
            ['type' => 'empty', 'tile_id' => 1],
            ['type' => 'empty', 'tile_id' => 1],
            ['type' => 'empty', 'tile_id' => 1],
        ],
        'grid_width' => 2,
        'grid_height' => 2,
        'start_x' => 0,
        'start_y' => 0,
        'required_circuits' => 0,
    ];

    $result = $this->validator->fullValidation($levelData);

    expect($result->isValid)->toBeFalse();
    expect($result->errors[0]['key'])->toBe('start_on_obstacle');
});

test('detects required circuits exceeds available', function () {
    $levelData = [
        'tiles' => [
            ['type' => 'empty', 'tile_id' => 1],
            ['type' => 'circuit', 'tile_id' => 2],
            ['type' => 'empty', 'tile_id' => 1],
            ['type' => 'empty', 'tile_id' => 1],
        ],
        'grid_width' => 2,
        'grid_height' => 2,
        'start_x' => 0,
        'start_y' => 0,
        'required_circuits' => 5,
    ];

    $result = $this->validator->fullValidation($levelData);

    expect($result->isValid)->toBeFalse();
    expect($result->errors[0]['key'])->toBe('required_circuits_exceeds_available');
});

test('detects unreachable circuits', function () {
    // Grid layout:
    // [S] [O]
    // [O] [C]
    // Start at (0,0), circuit at (1,1) blocked by obstacles
    $levelData = [
        'tiles' => [
            ['type' => 'empty', 'tile_id' => 1],    // (0,0) - start
            ['type' => 'obstacle', 'tile_id' => 3], // (1,0)
            ['type' => 'obstacle', 'tile_id' => 3], // (0,1)
            ['type' => 'circuit', 'tile_id' => 2],  // (1,1) - unreachable
        ],
        'grid_width' => 2,
        'grid_height' => 2,
        'start_x' => 0,
        'start_y' => 0,
        'required_circuits' => 1,
    ];

    $result = $this->validator->fullValidation($levelData);

    expect($result->isValid)->toBeFalse();
    expect($result->errors[0]['key'])->toBe('circuits_unreachable');
    expect($result->metadata['unreachable_circuits'])->toContain(3);
});

test('finds all reachable tiles with BFS', function () {
    // Grid layout:
    // [S] [E] [E]
    // [E] [O] [E]
    // [E] [E] [C]
    $levelData = [
        'tiles' => [
            ['type' => 'empty', 'tile_id' => 1],    // (0,0)
            ['type' => 'empty', 'tile_id' => 1],    // (1,0)
            ['type' => 'empty', 'tile_id' => 1],    // (2,0)
            ['type' => 'empty', 'tile_id' => 1],    // (0,1)
            ['type' => 'obstacle', 'tile_id' => 3], // (1,1)
            ['type' => 'empty', 'tile_id' => 1],    // (2,1)
            ['type' => 'empty', 'tile_id' => 1],    // (0,2)
            ['type' => 'empty', 'tile_id' => 1],    // (1,2)
            ['type' => 'circuit', 'tile_id' => 2],  // (2,2)
        ],
        'grid_width' => 3,
        'grid_height' => 3,
        'start_x' => 0,
        'start_y' => 0,
        'required_circuits' => 1,
    ];

    $result = $this->validator->fullValidation($levelData);

    expect($result->isValid)->toBeTrue();
    expect($result->metadata['reachable_tiles'])->toBe(8); // All except obstacle
    expect($result->metadata['unreachable_circuits'])->toBeEmpty();
});

test('validates grid structure', function () {
    $levelData = [
        'tiles' => [
            ['type' => 'empty', 'tile_id' => 1],
            ['type' => 'empty', 'tile_id' => 1],
        ],
        'grid_width' => 3,
        'grid_height' => 3,
        'start_x' => 0,
        'start_y' => 0,
        'required_circuits' => 0,
    ];

    $result = $this->validator->fullValidation($levelData);

    expect($result->isValid)->toBeFalse();
    expect($result->errors[0]['key'])->toBe('grid_structure_invalid');
});
