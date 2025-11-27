<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Score extends Model
{
    use HasFactory;

    protected $fillable = [
        'level_id',
        'player_id',
        'xp_earned',
        'commands_used',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'xp_earned' => 'integer',
            'commands_used' => 'integer',
            'completed_at' => 'datetime',
        ];
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }
}
