<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Player Details
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Player Profile Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex flex-col items-center">
                            <img src="https://robohash.org/{{ $player->user->email }}.png?size=200x200"
                                 alt="{{ $player->nickname }}"
                                 class="w-32 h-32 rounded-lg border-4 border-blue-500 mb-4">
                            <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $player->nickname }}</h3>
                            <p class="text-gray-600 text-sm mb-4">{{ $player->user->name }}</p>
                            <div class="bg-blue-50 px-4 py-2 rounded-full">
                                <span class="text-blue-700 font-semibold">{{ $player->xp }} XP</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Player Info Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg md:col-span-2">
                    <div class="p-6 text-gray-900">
                        <h4 class="text-lg font-semibold mb-4">Player Information</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-500">User Email</h3>
                                <p class="text-gray-900">{{ $player->user->email }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-500">Player ID</h3>
                                <p class="text-gray-900">#{{ $player->id }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-500">Total XP</h3>
                                <p class="text-gray-900">{{ $player->xp }} XP</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-500">Levels Completed</h3>
                                <p class="text-gray-900">{{ $player->scores->count() }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-500">Member Since</h3>
                                <p class="text-gray-900">{{ $player->created_at->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-500">Last Updated</h3>
                                <p class="text-gray-900">{{ $player->updated_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($player->scores->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">Completed Levels</h3>
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
</x-app-layout>
