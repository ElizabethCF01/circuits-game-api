<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Player extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'nickname',
        'xp',
    ];

    protected function casts(): array
    {
        return [
            'xp' => 'integer',
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

    public function levels(): BelongsToMany
    {
        return $this->belongsToMany(Level::class, 'scores')
            ->withPivot('xp_earned', 'commands_used', 'completed_at')
            ->withTimestamps();
    }
}
