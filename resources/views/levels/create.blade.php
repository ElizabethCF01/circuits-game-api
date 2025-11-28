<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
           Create New Level
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.levels.store') }}" method="POST" id="levelForm">
                        @csrf

                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4">
                                <x-breeze.input-label for="name" value="Name" />
                                <x-breeze.text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                                <x-breeze.input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-breeze.input-label for="difficulty" value="Difficulty" />
                                <select id="difficulty" name="difficulty" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="easy" {{ old('difficulty') === 'easy' ? 'selected' : '' }}>Easy</option>
                                    <option value="medium" {{ old('difficulty') === 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="hard" {{ old('difficulty') === 'hard' ? 'selected' : '' }}>Hard</option>
                                </select>
                                <x-breeze.input-error :messages="$errors->get('difficulty')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mb-4">
                            <x-breeze.input-label for="description" value="Description" />
                            <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                            <x-breeze.input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-4 gap-4">
                            <div class="mb-4">
                                <x-breeze.input-label for="grid_width" value="Grid Width" />
                                <x-breeze.text-input id="grid_width" class="block mt-1 w-full" type="number" name="grid_width" value="{{ old('grid_width', 5) }}" min="1" max="20" required />
                                <x-breeze.input-error :messages="$errors->get('grid_width')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-breeze.input-label for="grid_height" value="Grid Height" />
                                <x-breeze.text-input id="grid_height" class="block mt-1 w-full" type="number" name="grid_height" value="{{ old('grid_height', 5) }}" min="1" max="20" required />
                                <x-breeze.input-error :messages="$errors->get('grid_height')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-breeze.input-label for="start_x" value="Start X" />
                                <x-breeze.text-input id="start_x" class="block mt-1 w-full" type="number" name="start_x" :value="old('start_x', 0)" min="0" required />
                                <x-breeze.input-error :messages="$errors->get('start_x')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-breeze.input-label for="start_y" value="Start Y" />
                                <x-breeze.text-input id="start_y" class="block mt-1 w-full" type="number" name="start_y" :value="old('start_y', 0)" min="0" required />
                                <x-breeze.input-error :messages="$errors->get('start_y')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4">
                                <x-breeze.input-label for="required_circuits" value="Required Circuits" />
                                <x-breeze.text-input id="required_circuits" class="block mt-1 w-full" type="number" name="required_circuits" :value="old('required_circuits', 1)" min="0" required />
                                <x-breeze.input-error :messages="$errors->get('required_circuits')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-breeze.input-label for="max_commands" value="Max Commands" />
                                <x-breeze.text-input id="max_commands" class="block mt-1 w-full" type="number" name="max_commands" :value="old('max_commands', 10)" min="1" required />
                                <x-breeze.input-error :messages="$errors->get('max_commands')" class="mt-2" />
                            </div>
                        </div>

                        <x-grid-editor
                            :tiles="$tiles"
                            :gridWidth="old('grid_width', 5)"
                            :gridHeight="old('grid_height', 5)"
                        />

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.levels.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-3">
                                Cancel
                            </a>
                            <x-breeze.primary-button>
                                Create Level
                            </x-breeze.primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
