<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\User;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function index()
    {
        $players = Player::with('user')->paginate(10);
        return view('players.index', compact('players'));
    }

    public function create()
    {
        $users = User::doesntHave('player')->get();
        return view('players.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:players,user_id',
            'nickname' => 'required|string|max:255|unique:players,nickname',
            'xp' => 'nullable|integer|min:0',
        ]);

        Player::create($validated);

        return redirect()->route('admin.players.index')->with('success', 'Player created successfully.');
    }

    public function show(Player $player)
    {
        $player->load(['user', 'scores.level']);
        return view('players.show', compact('player'));
    }

    public function edit(Player $player)
    {
        $users = User::doesntHave('player')->orWhere('id', $player->user_id)->get();
        return view('players.edit', compact('player', 'users'));
    }

    public function update(Request $request, Player $player)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:players,user_id,' . $player->id,
            'nickname' => 'required|string|max:255|unique:players,nickname,' . $player->id,
            'xp' => 'nullable|integer|min:0',
        ]);

        $player->update($validated);

        return redirect()->route('admin.players.index')->with('success', 'Player updated successfully.');
    }

    public function destroy(Player $player)
    {
        $player->delete();

        return redirect()->route('admin.players.index')->with('success', 'Player deleted successfully.');
    }
}
