@props([
    'tiles' => [],
    'gridData' => null,
    'gridWidth' => 5,
    'gridHeight' => 5,
])

<div class="mb-6">
    <x-breeze.input-label value="Grid Editor" />
    <p class="text-sm text-gray-600 mb-3">Click on tiles to change their type. Update grid dimensions above to resize.</p>

    <div class="mb-3 flex gap-2">
        @foreach($tiles as $tile)
            <button type="button"
                    onclick="gridEditor.setCurrentTileType('{{ $tile->type }}', '{{ $tile->getFirstMediaUrl('images') }}')"
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

<input type="hidden" id="tiles" name="tiles" value="{{ $gridData ? json_encode($gridData) : '[]' }}" required />

<script>
    window.gridEditor = (function() {
        let currentTileType = 'empty';
        let currentTileImage = '';
        let currentTileId = null;
        let gridData = @json($gridData ?? []);

        const tileImages = @json($tiles->mapWithKeys(function($tile) {
            return [$tile->type => $tile->getFirstMediaUrl('images')];
        }));

        const tileIds = @json($tiles->mapWithKeys(function($tile) {
            return [$tile->type => $tile->id];
        }));

        function setCurrentTileType(type, imageUrl) {
            currentTileType = type;
            currentTileImage = imageUrl;
            currentTileId = tileIds[type] || null;
            document.querySelectorAll('[id^="btn-"]').forEach(btn => {
                btn.classList.remove('ring-4', 'ring-blue-500', 'border-blue-500');
            });
            document.getElementById('btn-' + type).classList.add('ring-4', 'ring-blue-500', 'border-blue-500');
        }

        function createGrid() {
            const width = parseInt(document.getElementById('grid_width').value) || {{ $gridWidth }};
            const height = parseInt(document.getElementById('grid_height').value) || {{ $gridHeight }};
            const container = document.getElementById('gridContainer');

            const expectedTiles = width * height;

            // Initialize or resize gridData
            if (gridData.length === 0 || gridData.length !== expectedTiles) {
                const emptyTileId = tileIds['empty'] || null;
                const newGridData = [];

                for (let i = 0; i < expectedTiles; i++) {
                    if (gridData[i]) {
                        newGridData.push(gridData[i]);
                    } else {
                        newGridData.push({type: 'empty', tile_id: emptyTileId});
                    }
                }
                gridData = newGridData;
            }

            container.innerHTML = '';
            container.style.display = 'grid';
            container.style.gridTemplateColumns = `repeat(${width}, 40px)`;
            container.style.gap = '2px';

            for (let i = 0; i < expectedTiles; i++) {
                const tile = document.createElement('div');
                const tileType = gridData[i]?.type || 'empty';
                const tileImageUrl = tileImages[tileType] || '';
                const tileTileId = gridData[i]?.tile_id || tileIds[tileType] || null;

                tile.style.width = '40px';
                tile.style.height = '40px';
                tile.style.border = '1px solid #9ca3af';
                tile.style.cursor = 'pointer';
                tile.style.overflow = 'hidden';
                tile.style.backgroundColor = '#e5e7eb';
                tile.dataset.index = i;
                tile.dataset.type = tileType;
                tile.dataset.image = tileImageUrl;
                tile.dataset.tileId = tileTileId;

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
                    this.dataset.tileId = currentTileId;
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
                tile_id: parseInt(tile.dataset.tileId) || tileIds[tile.dataset.type] || null
            }));
            document.getElementById('tiles').value = JSON.stringify(gridData);
        }

        function init() {
            const gridWidthInput = document.getElementById('grid_width');
            const gridHeightInput = document.getElementById('grid_height');

            if (gridWidthInput) {
                gridWidthInput.addEventListener('change', createGrid);
            }
            if (gridHeightInput) {
                gridHeightInput.addEventListener('change', createGrid);
            }

            setCurrentTileType('empty', tileImages['empty'] || '');
            createGrid();
        }

        // Auto-initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }

        return {
            setCurrentTileType,
            createGrid,
            updateTilesData,
            init
        };
    })();
</script>
