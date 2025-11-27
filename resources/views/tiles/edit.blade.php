<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Tile
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.tiles.update', $tile) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <x-breeze.input-label for="type" value="Type" />
                            <x-breeze.text-input id="type" class="block mt-1 w-full" type="text" name="type" :value="old('type', $tile->type)" required autofocus />
                            <x-breeze.input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-breeze.input-label for="image" value="Image" />

                            @if($tile->getFirstMediaUrl('images'))
                                <div class="mb-2">
                                    <img src="{{ $tile->getFirstMediaUrl('images') }}" alt="Current image" class="w-32 h-32 object-cover rounded">
                                    <p class="text-sm text-gray-500 mt-1">Current image</p>
                                </div>
                            @endif

                            <input id="image" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" type="file" name="image" accept="image/*" />
                            <p class="mt-1 text-sm text-gray-500">Max size: 2MB. Leave empty to keep current image.</p>
                            <x-breeze.input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.tiles.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-3">
                                Cancel
                            </a>
                            <x-breeze.primary-button>
                                Update Tile
                            </x-breeze.primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
