<?php

namespace App\Http\Controllers;

use App\Models\Tile;
use Illuminate\Http\Request;

class TileController extends Controller
{
    public function index()
    {
        $tiles = Tile::latest()->paginate(15);

        return view('tiles.index', compact('tiles'));
    }

    public function create()
    {
        return view('tiles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'image' => 'nullable|string|max:255',
        ]);

        $tile = Tile::create($validated);

        return redirect()->route('tiles.index')
            ->with('success', 'Tile created successfully.');
    }

    public function show(Tile $tile)
    {
        return view('tiles.show', compact('tile'));
    }

    public function edit(Tile $tile)
    {
        return view('tiles.edit', compact('tile'));
    }

    public function update(Request $request, Tile $tile)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'image' => 'nullable|string|max:255',
        ]);

        $tile->update($validated);

        return redirect()->route('tiles.index')
            ->with('success', 'Tile updated successfully.');
    }

    public function destroy(Tile $tile)
    {
        $tile->delete();

        return redirect()->route('tiles.index')
            ->with('success', 'Tile deleted successfully.');
    }
}
