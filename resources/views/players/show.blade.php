<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Player Details
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-700">ID:</h3>
                        <p class="text-gray-900">{{ $player->id }}</p>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-700">User:</h3>
                        <p class="text-gray-900">{{ $player->user->name }} ({{ $player->user->email }})</p>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-700">Nickname:</h3>
                        <p class="text-gray-900">{{ $player->nickname }}</p>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-700">XP:</h3>
                        <p class="text-gray-900">{{ $player->xp }}</p>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-700">Created:</h3>
                        <p class="text-gray-900">{{ $player->created_at->format('d/m/Y H:i:s') }}</p>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-700">Updated:</h3>
                        <p class="text-gray-900">{{ $player->updated_at->format('d/m/Y H:i:s') }}</p>
                    </div>

                    @if($player->scores->count() > 0)
                        <div class="mt-8">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Completed Levels</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">XP Earned</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commands Used</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed At</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($player->scores as $score)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    <a href="{{ route('admin.levels.show', $score->level) }}" class="text-blue-600 hover:text-blue-900">
                                                        {{ $score->level->name }}
                                                    </a>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $score->xp_earned }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $score->commands_used }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $score->completed_at->format('d/m/Y H:i:s') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center justify-start mt-6">
                        <a href="{{ route('admin.players.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-3">
                            Back
                        </a>
                        <a href="{{ route('admin.players.edit', $player) }}" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 focus:bg-blue-600 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-3">
                            Edit
                        </a>
                        <form action="{{ route('admin.players.destroy', $player) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-600 focus:bg-red-600 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150" onclick="return confirm('Are you sure you want to delete this player?')">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
