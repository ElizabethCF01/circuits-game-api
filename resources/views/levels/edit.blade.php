<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Level
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.levels.update', $level) }}" method="POST" id="levelForm">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4">
                                <x-breeze.input-label for="name" value="Name" />
                                <x-breeze.text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $level->name)" required autofocus />
                                <x-breeze.input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-breeze.input-label for="difficulty" value="Difficulty" />
                                <select id="difficulty" name="difficulty" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="easy" {{ old('difficulty', $level->difficulty) === 'easy' ? 'selected' : '' }}>Easy</option>
                                    <option value="medium" {{ old('difficulty', $level->difficulty) === 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="hard" {{ old('difficulty', $level->difficulty) === 'hard' ? 'selected' : '' }}>Hard</option>
                                </select>
                                <x-breeze.input-error :messages="$errors->get('difficulty')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mb-4">
                            <x-breeze.input-label for="description" value="Description" />
                            <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $level->description) }}</textarea>
                            <x-breeze.input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-4 gap-4">
                            <div class="mb-4">
                                <x-breeze.input-label for="grid_width" value="Grid Width" />
                                <x-breeze.text-input id="grid_width" class="block mt-1 w-full" type="number" name="grid_width" value="{{ old('grid_width', $level->grid_width) }}" min="1" max="20" required />
                                <x-breeze.input-error :messages="$errors->get('grid_width')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-breeze.input-label for="grid_height" value="Grid Height" />
                                <x-breeze.text-input id="grid_height" class="block mt-1 w-full" type="number" name="grid_height" value="{{ old('grid_height', $level->grid_height) }}" min="1" max="20" required />
                                <x-breeze.input-error :messages="$errors->get('grid_height')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-breeze.input-label for="start_x" value="Start X" />
                                <x-breeze.text-input id="start_x" class="block mt-1 w-full" type="number" name="start_x" :value="old('start_x', $level->start_x)" min="0" required />
                                <x-breeze.input-error :messages="$errors->get('start_x')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-breeze.input-label for="start_y" value="Start Y" />
                                <x-breeze.text-input id="start_y" class="block mt-1 w-full" type="number" name="start_y" :value="old('start_y', $level->start_y)" min="0" required />
                                <x-breeze.input-error :messages="$errors->get('start_y')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4">
                                <x-breeze.input-label for="required_circuits" value="Required Circuits" />
                                <x-breeze.text-input id="required_circuits" class="block mt-1 w-full" type="number" name="required_circuits" :value="old('required_circuits', $level->required_circuits)" min="0" required />
                                <x-breeze.input-error :messages="$errors->get('required_circuits')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-breeze.input-label for="max_commands" value="Max Commands" />
                                <x-breeze.text-input id="max_commands" class="block mt-1 w-full" type="number" name="max_commands" :value="old('max_commands', $level->max_commands)" min="1" required />
                                <x-breeze.input-error :messages="$errors->get('max_commands')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mb-6">
                            <x-breeze.input-label value="Grid Editor" />
                            <p class="text-sm text-gray-600 mb-3">Click on tiles to change their type. Update grid dimensions above to resize.</p>

                            <div class="mb-3 flex gap-2">
                                @foreach($tiles as $tile)
                                    <button type="button"
                                            onclick="setCurrentTileType('{{ $tile->type }}', '{{ $tile->getFirstMediaUrl('images') }}')"
                                            class="px-3 py-2 border-2 border-gray-300 hover:border-gray-500 rounded flex flex-col items-center gap-1"
                                            id="btn-{{ $tile->type }}">
                                        @if($tile->getFirstMediaUrl('images'))
                                            <img src="{{ $tile->getFirstMediaUrl('images') }}" alt="{{ $tile->type }}" class="w-12 h-12 object-cover">
                                        @else
                                            <div class="w-12 h-12 bg-gray-200"></div>
                                        @endif
                                        <span class="text-xs">{{ ucfirst($tile->type) }}</span>
                                    </button>
                                @endforeach
                            </div>

                            <div id="gridContainer" class="inline-block border-2 border-gray-400 p-2 bg-gray-100"></div>
                        </div>

                        <input type="hidden" id="tiles" name="tiles" value="{{ json_encode($level->tiles) }}" required />

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.levels.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-3">
                                Cancel
                            </a>
                            <x-breeze.primary-button>
                                Update Level
                            </x-breeze.primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentTileType = 'empty';
        let currentTileImage = '';
        let gridData = @json($level->tiles);

        const tileImages = @json($tiles->mapWithKeys(function($tile) {
            return [$tile->type => $tile->getFirstMediaUrl('images')];
        }));

        function setCurrentTileType(type, imageUrl) {
            currentTileType = type;
            currentTileImage = imageUrl;
            document.querySelectorAll('[id^="btn-"]').forEach(btn => {
                btn.classList.remove('ring-4', 'ring-blue-500', 'border-blue-500');
            });
            document.getElementById('btn-' + type).classList.add('ring-4', 'ring-blue-500', 'border-blue-500');
        }

        function createGrid() {
            const width = parseInt(document.getElementById('grid_width').value) || 5;
            const height = parseInt(document.getElementById('grid_height').value) || 5;
            const container = document.getElementById('gridContainer');

            const expectedTiles = width * height;
            if (gridData.length !== expectedTiles) {
                gridData = Array(expectedTiles).fill(null).map(() => ({type: 'empty', tile_id: 1}));
            }

            container.innerHTML = '';
            container.style.display = 'grid';
            container.style.gridTemplateColumns = `repeat(${width}, 40px)`;
            container.style.gap = '2px';

            for (let i = 0; i < expectedTiles; i++) {
                const tile = document.createElement('div');
                const tileType = gridData[i]?.type || 'empty';
                const tileImageUrl = tileImages[tileType] || '';

                tile.style.width = '40px';
                tile.style.height = '40px';
                tile.style.border = '1px solid #9ca3af';
                tile.style.cursor = 'pointer';
                tile.style.overflow = 'hidden';
                tile.style.backgroundColor = '#e5e7eb';
                tile.dataset.index = i;
                tile.dataset.type = tileType;
                tile.dataset.image = tileImageUrl;

                if (tileImageUrl) {
                    const img = document.createElement('img');
                    img.src = tileImageUrl;
                    img.style.width = '100%';
                    img.style.height = '100%';
                    img.style.objectFit = 'cover';
                    tile.appendChild(img);
                }

                tile.addEventListener('click', function() {
                    this.dataset.type = currentTileType;
                    this.dataset.image = currentTileImage;
                    this.innerHTML = '';
                    if (currentTileImage) {
                        const img = document.createElement('img');
                        img.src = currentTileImage;
                        img.style.width = '100%';
                        img.style.height = '100%';
                        img.style.objectFit = 'cover';
                        this.appendChild(img);
                    }
                    updateTilesData();
                });

                container.appendChild(tile);
            }

            updateTilesData();
        }

        function updateTilesData() {
            const tiles = document.querySelectorAll('#gridContainer div');
            gridData = Array.from(tiles).map(tile => ({
                type: tile.dataset.type,
                tile_id: 1
            }));
            document.getElementById('tiles').value = JSON.stringify(gridData);
        }

        document.getElementById('grid_width').addEventListener('change', createGrid);
        document.getElementById('grid_height').addEventListener('change', createGrid);

        setCurrentTileType('empty', tileImages['empty'] || '');
        createGrid();
    </script>
</x-app-layout>
