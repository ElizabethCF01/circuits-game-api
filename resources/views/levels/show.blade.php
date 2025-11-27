<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Level Details
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <h3 class="text-lg font-semibold text-gray-700">ID:</h3>
                                <p class="text-gray-900">{{ $level->id }}</p>
                            </div>

                            <div class="mb-4">
                                <h3 class="text-lg font-semibold text-gray-700">Name:</h3>
                                <p class="text-gray-900">{{ $level->name }}</p>
                            </div>

                            <div class="mb-4">
                                <h3 class="text-lg font-semibold text-gray-700">Creator:</h3>
                                <p class="text-gray-900">{{ $level->user->name }}</p>
                            </div>

                            <div class="mb-4">
                                <h3 class="text-lg font-semibold text-gray-700">Description:</h3>
                                <p class="text-gray-900">{{ $level->description ?? 'N/A' }}</p>
                            </div>

                            <div class="mb-4">
                                <h3 class="text-lg font-semibold text-gray-700">Difficulty:</h3>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($level->difficulty === 'easy') bg-green-100 text-green-800
                                    @elseif($level->difficulty === 'medium') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($level->difficulty) }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <div class="mb-4">
                                <h3 class="text-lg font-semibold text-gray-700">Grid Size:</h3>
                                <p class="text-gray-900">{{ $level->grid_width }} x {{ $level->grid_height }}</p>
                            </div>

                            <div class="mb-4">
                                <h3 class="text-lg font-semibold text-gray-700">Start Position:</h3>
                                <p class="text-gray-900">({{ $level->start_x }}, {{ $level->start_y }})</p>
                            </div>

                            <div class="mb-4">
                                <h3 class="text-lg font-semibold text-gray-700">Required Circuits:</h3>
                                <p class="text-gray-900">{{ $level->required_circuits }}</p>
                            </div>

                            <div class="mb-4">
                                <h3 class="text-lg font-semibold text-gray-700">Max Commands:</h3>
                                <p class="text-gray-900">{{ $level->max_commands }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4 mt-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">Grid Visualization:</h3>
                        <div id="gridContainer" class="inline-block border-2 border-gray-400 p-2 bg-gray-100"></div>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-700">Created:</h3>
                        <p class="text-gray-900">{{ $level->created_at->format('d/m/Y H:i:s') }}</p>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-700">Updated:</h3>
                        <p class="text-gray-900">{{ $level->updated_at->format('d/m/Y H:i:s') }}</p>
                    </div>

                    <div class="flex items-center justify-start mt-6">
                        <a href="{{ route('admin.levels.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-3">
                            Back
                        </a>
                        <a href="{{ route('admin.levels.edit', $level) }}" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 focus:bg-blue-600 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-3">
                            Edit
                        </a>
                        <form action="{{ route('admin.levels.destroy', $level) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-600 focus:bg-red-600 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150" onclick="return confirm('Are you sure you want to delete this level?')">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const gridData = @json($level->tiles);
        const width = {{ $level->grid_width }};
        const height = {{ $level->grid_height }};

        const tileColors = {
            'empty': '#e5e7eb',
            'circuit': '#93c5fd',
            'obstacle': '#fca5a5'
        };

        function renderGrid() {
            const container = document.getElementById('gridContainer');

            container.innerHTML = '';
            container.style.display = 'grid';
            container.style.gridTemplateColumns = `repeat(${width}, 40px)`;
            container.style.gap = '2px';

            for (let i = 0; i < width * height; i++) {
                const tile = document.createElement('div');
                const tileType = gridData[i]?.type || 'empty';

                tile.style.width = '40px';
                tile.style.height = '40px';
                tile.style.backgroundColor = tileColors[tileType];
                tile.style.border = '1px solid #9ca3af';

                container.appendChild(tile);
            }
        }

        renderGrid();
    </script>
</x-app-layout>
