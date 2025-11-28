<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlayerController extends Controller
{
    public function show()
    {
        $player = Auth::user()->player;

        if (!$player) {
            return redirect()->route('userzone.player.create');
        }

        $player->load(['scores.level']);
        return view('userzone.player.show', compact('player'));
    }

    public function create()
    {
        if (Auth::user()->player) {
            return redirect()->route('userzone.player.show');
        }

        return view('userzone.player.create');
    }

    public function store(Request $request)
    {
        if (Auth::user()->player) {
            return redirect()->route('userzone.player.show')
                ->with('error', 'You already have a player profile.');
        }

        $validated = $request->validate([
            'nickname' => 'required|string|max:255|unique:players,nickname',
        ]);

        Player::create([
            'user_id' => Auth::id(),
            'nickname' => $validated['nickname'],
            'xp' => 0,
        ]);

        return redirect()->route('userzone.player.show')
            ->with('success', 'Player profile created successfully!');
    }

    public function edit()
    {
        $player = Auth::user()->player;

        if (!$player) {
            return redirect()->route('userzone.player.create');
        }

        return view('userzone.player.edit', compact('player'));
    }

    public function update(Request $request)
    {
        $player = Auth::user()->player;

        if (!$player) {
            return redirect()->route('userzone.player.create');
        }

        $validated = $request->validate([
            'nickname' => 'required|string|max:255|unique:players,nickname,' . $player->id,
        ]);

        $player->update($validated);

        return redirect()->route('userzone.player.show')
            ->with('success', 'Player profile updated successfully!');
    }
}
