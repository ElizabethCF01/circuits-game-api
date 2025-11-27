<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Level extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'start_x',
        'start_y',
        'required_circuits',
        'max_commands',
        'difficulty',
        'grid_width',
        'grid_height',
        'tiles',
    ];

    protected function casts(): array
    {
        return [
            'start_x' => 'integer',
            'start_y' => 'integer',
            'required_circuits' => 'integer',
            'max_commands' => 'integer',
            'grid_width' => 'integer',
            'grid_height' => 'integer',
            'tiles' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
