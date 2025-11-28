<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Create Player Profile
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-2">Create Your Player Profile</h3>
                        <p class="text-gray-600">Choose a unique nickname to start your gaming journey!</p>
                    </div>

                    <form method="POST" action="{{ route('userzone.player.store') }}">
                        @csrf

                        <div class="mb-6">
                            <x-breeze.input-label for="nickname" :value="'Nickname'" />
                            <x-breeze.text-input id="nickname" class="block mt-1 w-full" type="text" name="nickname" :value="old('nickname')" required autofocus />
                            <x-breeze.input-error :messages="$errors->get('nickname')" class="mt-2" />
                            <p class="text-sm text-gray-500 mt-1">This will be your display name in the game</p>
                        </div>

                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-700 mb-2">Preview your avatar:</p>
                            <div class="flex items-center gap-4">
                                <img id="avatar-preview"
                                     src="https://robohash.org/{{ Auth::user()->email }}.png?size=200x200"
                                     alt="Your avatar"
                                     class="w-24 h-24 rounded-lg border-2 border-gray-300">
                                <div class="text-sm text-gray-600">
                                    <p>Your unique avatar is generated from your email address.</p>
                                    <p class="text-xs text-gray-500 mt-1">Powered by Robohash</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                                Cancel
                            </a>
                            <x-breeze.primary-button>
                              Create Player Profile
                            </x-breeze.primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
