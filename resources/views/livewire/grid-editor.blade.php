<div>
    {{-- Tile selector --}}
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Select Tile Type</label>
        <div class="flex gap-2 flex-wrap">
            @foreach($availableTiles as $type => $tile)
                <button
                    type="button"
                    wire:click="selectTile('{{ $type }}')"
                    class="p-2 border-2 rounded flex flex-col items-center gap-1 transition-all
                        {{ $selectedTileType === $type ? 'border-blue-500 ring-2 ring-blue-300' : 'border-gray-300 hover:border-gray-400' }}"
                >
                    @if($tile['image'])
                        <img src="{{ $tile['image'] }}" alt="{{ $type }}" class="w-12 h-12 object-cover">
                    @else
                        <div class="w-12 h-12 bg-gray-200 flex items-center justify-center">
                            <span class="text-xs text-gray-500">?</span>
                        </div>
                    @endif
                    <span class="text-xs capitalize">{{ $type }}</span>
                </button>
            @endforeach
        </div>
    </div>

    {{-- Grid size controls --}}
    <div class="mb-4 flex gap-4 items-end">
        <div>
            <label for="lw_grid_width" class="block text-sm font-medium text-gray-700">Width</label>
            <input
                type="number"
                id="lw_grid_width"
                wire:model.live="gridWidth"
                wire:change="updateGridSize"
                min="1"
                max="20"
                class="mt-1 w-20 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
        </div>
        <div>
            <label for="lw_grid_height" class="block text-sm font-medium text-gray-700">Height</label>
            <input
                type="number"
                id="lw_grid_height"
                wire:model.live="gridHeight"
                wire:change="updateGridSize"
                min="1"
                max="20"
                class="mt-1 w-20 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
        </div>
        <button
            type="button"
            wire:click="clearGrid"
            class="px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm"
        >
            Clear Grid
        </button>
    </div>

    {{-- Stats --}}
    <div class="mb-3 text-sm text-gray-600">
        Circuits placed: <span class="font-semibold">{{ $this->getCircuitCount() }}</span>
    </div>

    {{-- Grid --}}
    <div
        class="inline-grid gap-0.5 p-2 bg-gray-100 border-2 border-gray-400 rounded"
        style="grid-template-columns: repeat({{ $gridWidth }}, 40px);"
    >
        @foreach($tiles as $index => $tile)
            <button
                type="button"
                wire:click="paintCell({{ $index }})"
                class="w-10 h-10 border border-gray-400 bg-gray-200 overflow-hidden hover:opacity-80 transition-opacity"
                title="Cell {{ $index }}: {{ $tile['type'] }}"
            >
                @php $image = $this->getTileImage($index); @endphp
                @if($image)
                    <img src="{{ $image }}" alt="{{ $tile['type'] }}" class="w-full h-full object-cover">
                @endif
            </button>
        @endforeach
    </div>

    {{-- Hidden input for form submission --}}
    <input type="hidden" name="tiles" value="{{ json_encode($tiles) }}">
    <input type="hidden" name="grid_width" value="{{ $gridWidth }}">
    <input type="hidden" name="grid_height" value="{{ $gridHeight }}">
</div>
