<div>
    {{-- Flash message --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('message') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="mb-4 flex flex-wrap gap-4">
        <div class="flex-1 min-w-[200px]">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search levels..."
                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
        </div>
        <div>
            <select
                wire:model.live="difficulty"
                class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
                <option value="">All Difficulties</option>
                <option value="easy">Easy</option>
                <option value="medium">Medium</option>
                <option value="hard">Hard</option>
            </select>
        </div>
    </div>

    {{-- Table --}}
    @if($levels->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('id')">
                            ID
                            @if($sortBy === 'id')
                                <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('name')">
                            Name
                            @if($sortBy === 'name')
                                <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Creator
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('difficulty')">
                            Difficulty
                            @if($sortBy === 'difficulty')
                                <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Grid Size
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Circuits
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($levels as $level)
                        <tr wire:key="level-{{ $level->id }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $level->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $level->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $level->user->name ?? 'Unknown' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($level->difficulty->value === 'easy') bg-green-100 text-green-800
                                    @elseif($level->difficulty->value === 'medium') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($level->difficulty->value) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $level->grid_width }}x{{ $level->grid_height }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $level->required_circuits }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <button
                                    wire:click="togglePublic({{ $level->id }})"
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full cursor-pointer
                                        {{ $level->is_public ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}"
                                >
                                    {{ $level->is_public ? 'Public' : 'Draft' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.levels.show', $level) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                <a href="{{ route('admin.levels.edit', $level) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $levels->links() }}
        </div>
    @else
        <p class="text-gray-500">
            @if($search || $difficulty)
                No levels found matching your filters.
                <button wire:click="$set('search', ''); $set('difficulty', '')" class="text-blue-600 hover:text-blue-900">Clear filters</button>
            @else
                No levels available.
                <a href="{{ route('admin.levels.create') }}" class="text-blue-600 hover:text-blue-900">Create the first one</a>
            @endif
        </p>
    @endif
</div>
