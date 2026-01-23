<div>
    {{-- Validation Feedback Panel --}}
    @if($this->hasValidationIssues)
    <div class="mb-4 space-y-2">
        @foreach($validationErrors as $error)
        <div class="p-3 bg-red-100 border border-red-400 text-red-700 rounded text-sm">
            <span class="font-semibold">Error:</span> {{ $error['message'] }}
        </div>
        @endforeach

        @foreach($validationWarnings as $warning)
        <div class="p-3 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded text-sm">
            <span class="font-semibold">Warning:</span> {{ $warning['message'] }}
        </div>
        @endforeach
    </div>
    @endif

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
        <button
            type="button"
            wire:click="validateGrid"
            class="px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm"
        >
            Validate
        </button>
    </div>

    {{-- Stats with validation info --}}
    <div class="mb-3 text-sm text-gray-600 flex gap-4 flex-wrap">
        <span>Circuits placed: <span class="font-semibold">{{ $this->getCircuitCount() }}</span></span>
        <span>Required: <span class="font-semibold">{{ $requiredCircuits }}</span></span>
        <span>Start: <span class="font-semibold">({{ $startX }}, {{ $startY }})</span></span>
        <span class="text-blue-600">
            Suggested max commands: <span class="font-semibold">{{ $suggestedMaxCommands }}</span>
        </span>
        @if(count($unreachableTileIndices) > 0)
        <span class="text-red-600">
            Unreachable: <span class="font-semibold">{{ count($unreachableTileIndices) }}</span>
        </span>
        @endif
    </div>

    {{-- Legend --}}
    <div class="mb-3 text-xs text-gray-500 flex gap-4">
        <span class="flex items-center gap-1">
            <span class="w-4 h-4 bg-green-400 border border-green-600 rounded"></span> Start
        </span>
        <span class="flex items-center gap-1">
            <span class="w-4 h-4 bg-red-400 border border-red-600 rounded"></span> Unreachable
        </span>
    </div>

    {{-- Grid with visual feedback --}}
    <div
        class="inline-grid gap-0.5 p-2 bg-gray-100 border-2 border-gray-400 rounded"
        style="grid-template-columns: repeat({{ $gridWidth }}, 40px);"
    >
        @foreach($tiles as $index => $tile)
            @php
                $isStart = $this->isStartPosition($index);
                $isUnreachable = $this->isUnreachable($index);
                $isCircuit = $tile['type'] === 'circuit';
            @endphp
            <button
                type="button"
                wire:click="paintCell({{ $index }})"
                class="w-10 h-10 border overflow-hidden hover:opacity-80 transition-opacity relative
                    {{ $isStart ? 'ring-2 ring-green-500 ring-offset-1' : '' }}
                    {{ $isUnreachable && $isCircuit ? 'ring-2 ring-red-500' : 'border-gray-400' }}
                    {{ $tile['type'] === 'obstacle' ? 'bg-gray-600' : 'bg-gray-200' }}"
                title="Cell ({{ $index % $gridWidth }}, {{ intdiv($index, $gridWidth) }}): {{ $tile['type'] }}{{ $isStart ? ' [START]' : '' }}{{ $isUnreachable ? ' [UNREACHABLE]' : '' }}"
            >
                @php $image = $this->getTileImage($index); @endphp
                @if($image)
                    <img src="{{ $image }}" alt="{{ $tile['type'] }}" class="w-full h-full object-cover">
                @endif

                {{-- Start position indicator --}}
                @if($isStart)
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <span class="bg-green-500 text-white text-xs font-bold px-1.5 py-0.5 rounded shadow">S</span>
                </div>
                @endif

                {{-- Unreachable circuit indicator --}}
                @if($isUnreachable && $isCircuit)
                <div class="absolute inset-0 flex items-center justify-center bg-red-500 bg-opacity-50 pointer-events-none">
                    <span class="text-white text-lg font-bold">!</span>
                </div>
                @endif
            </button>
        @endforeach
    </div>

    {{-- Hidden inputs for form submission --}}
    <input type="hidden" name="tiles" value="{{ json_encode($tiles) }}">
    <input type="hidden" name="grid_width" value="{{ $gridWidth }}">
    <input type="hidden" name="grid_height" value="{{ $gridHeight }}">
    <input type="hidden" name="max_commands" value="{{ $suggestedMaxCommands }}">
</div>
