<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLevelRequest;
use App\Http\Requests\UpdateLevelRequest;
use App\Models\Level;

class LevelController extends Controller
{
    public function index()
    {
        return view('levels.index');
    }

    public function create()
    {
        return view('levels.create');
    }

    public function store(StoreLevelRequest $request)
    {
        $validated = $request->validated();
        $validated['tiles'] = json_decode($validated['tiles'], true);
        $validated['user_id'] = auth()->id();

        Level::create($validated);

        return redirect()->route('admin.levels.index')->with('success', 'Level created successfully.');
    }

    public function show(Level $level)
    {
        $level->load('user');
        $tiles = \App\Models\Tile::all();
        return view('levels.show', compact('level', 'tiles'));
    }

    public function edit(Level $level)
    {
        return view('levels.edit', compact('level'));
    }

    public function update(UpdateLevelRequest $request, Level $level)
    {
        $validated = $request->validated();
        $validated['tiles'] = json_decode($validated['tiles'], true);

        $level->update($validated);

        return redirect()->route('admin.levels.index')->with('success', 'Level updated successfully.');
    }

    public function destroy(Level $level)
    {
        $level->delete();

        return redirect()->route('admin.levels.index')->with('success', 'Level deleted successfully.');
    }
}
