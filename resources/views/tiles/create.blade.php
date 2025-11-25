<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
           Create New Tile
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('tiles.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <x-breeze.input-label for="type" value="Type" />
                            <x-breeze.text-input id="type" class="block mt-1 w-full" type="text" name="type" :value="old('type')" required autofocus />
                            <x-breeze.input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-breeze.input-label for="image" value="Image (URL or path)" />
                            <x-breeze.text-input id="image" class="block mt-1 w-full" type="text" name="image" :value="old('image')" />
                            <x-breeze.input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('tiles.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-3">
                                Cancel
                            </a>
                            <x-breeze.primary-button>
                                Create Tile
                            </x-breeze.primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
