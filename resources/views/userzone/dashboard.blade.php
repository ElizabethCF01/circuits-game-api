<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
         Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Welcome Card -->
                <div class="{{ Auth::user()->is_admin ? 'md:col-span-3' : 'md:col-span-2' }} bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-2">Welcome, {{ Auth::user()->name }}!</h3>
                        <p class="text-gray-600 mb-4"> You're logged in! </p>
                        <a href="https://elizabethcf01.github.io/circuits-web-game/" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Play the Circuits Game
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Player Profile Card -->
                @if(Auth::user()->player)
                    <div class="bg-gradient-to-br from-blue-500 to-purple-600 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-white">
                            <div class="flex flex-col items-center">
                                <img src="https://robohash.org/{{ Auth::user()->email }}.png?size=200x200"
                                     alt="{{ Auth::user()->player->nickname }}"
                                     class="w-20 h-20 rounded-lg border-3 border-white mb-3">
                                <h4 class="text-lg font-bold mb-1">{{ Auth::user()->player->nickname }}</h4>
                                <div class="bg-white/20 px-3 py-1 rounded-full mb-3">
                                    <span class="text-sm font-semibold">{{ Auth::user()->player->xp }} XP</span>
                                </div>
                                <a href="{{ route('userzone.player.show') }}" class="text-white hover:text-gray-200 text-sm font-medium underline">
                                    View Profile →
                                </a>
                            </div>
                        </div>
                    </div>
                @elseif(!Auth::user()->is_admin)
                    <div class="bg-gradient-to-br from-green-500 to-teal-600 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-white">
                            <div class="flex flex-col items-center text-center">
                                <svg class="w-16 h-16 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                </svg>
                                <h4 class="text-lg font-bold mb-2">Create Your Player</h4>
                                <p class="text-sm mb-3 text-white/90">Start your journey by creating a player profile!</p>
                                <a href="{{ route('userzone.player.create') }}" class="bg-white text-green-600 px-4 py-2 rounded-md hover:bg-gray-100 font-medium text-sm">
                                    Create Profile
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="mb-6">
                <livewire:trivia-card />
            </div>

            @if(Auth::user()->is_admin)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Admin Quick Access</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="{{ route('admin.levels.index') }}" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-200">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Levels</dt>
                                            <dd class="text-lg font-semibold text-gray-900">Manage Levels</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('admin.tiles.index') }}" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-200">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Tiles</dt>
                                            <dd class="text-lg font-semibold text-gray-900">Manage Tiles</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('admin.players.index') }}" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-200">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Players</dt>
                                            <dd class="text-lg font-semibold text-gray-900">Manage Players</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="{{ route('admin.levels.create') }}" class="block bg-blue-50 border-2 border-blue-200 overflow-hidden shadow-sm sm:rounded-lg hover:bg-blue-100 transition-colors duration-200">
                        <div class="p-4 text-center">
                            <span class="text-blue-700 font-semibold">+ Create New Level</span>
                        </div>
                    </a>

                    <a href="{{ route('admin.tiles.create') }}" class="block bg-green-50 border-2 border-green-200 overflow-hidden shadow-sm sm:rounded-lg hover:bg-green-100 transition-colors duration-200">
                        <div class="p-4 text-center">
                            <span class="text-green-700 font-semibold">+ Create New Tile</span>
                        </div>
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
