<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class, 'scores')
            ->withPivot('xp_earned', 'commands_used', 'completed_at')
            ->withTimestamps();
    }
}
