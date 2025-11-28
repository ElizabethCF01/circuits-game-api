<?php

namespace App\Http\Controllers;

use App\Models\Level;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    public function index()
    {
        $levels = Level::with('user')->paginate(10);
        return view('levels.index', compact('levels'));
    }

    public function create()
    {
        $tiles = \App\Models\Tile::all();
        return view('levels.create', compact('tiles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_x' => 'required|integer|min:0',
            'start_y' => 'required|integer|min:0',
            'required_circuits' => 'required|integer|min:0',
            'max_commands' => 'required|integer|min:1',
            'difficulty' => 'required|in:easy,medium,hard',
            'grid_width' => 'required|integer|min:1',
            'grid_height' => 'required|integer|min:1',
            'tiles' => 'required|json',
        ]);

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
        $tiles = \App\Models\Tile::all();
        return view('levels.edit', compact('level', 'tiles'));
    }

    public function update(Request $request, Level $level)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_x' => 'required|integer|min:0',
            'start_y' => 'required|integer|min:0',
            'required_circuits' => 'required|integer|min:0',
            'max_commands' => 'required|integer|min:1',
            'difficulty' => 'required|in:easy,medium,hard',
            'grid_width' => 'required|integer|min:1',
            'grid_height' => 'required|integer|min:1',
            'tiles' => 'required|json',
        ]);

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
