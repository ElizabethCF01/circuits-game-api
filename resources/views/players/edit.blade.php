<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Player
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.players.update', $player) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <x-breeze.input-label for="user_id" value="User" />
                            <select id="user_id" name="user_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id', $player->user_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            <x-breeze.input-error :messages="$errors->get('user_id')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-breeze.input-label for="nickname" value="Nickname" />
                            <x-breeze.text-input id="nickname" class="block mt-1 w-full" type="text" name="nickname" :value="old('nickname', $player->nickname)" required autofocus />
                            <x-breeze.input-error :messages="$errors->get('nickname')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-breeze.input-label for="xp" value="XP" />
                            <x-breeze.text-input id="xp" class="block mt-1 w-full" type="number" name="xp" :value="old('xp', $player->xp)" min="0" />
                            <x-breeze.input-error :messages="$errors->get('xp')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.players.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-3">
                                Cancel
                            </a>
                            <x-breeze.primary-button>
                                Update Player
                            </x-breeze.primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
