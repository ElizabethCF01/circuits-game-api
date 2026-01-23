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
                    <form action="{{ route('admin.levels.store') }}" method="POST">
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

                        <div class="grid grid-cols-3 gap-4" x-data="{ startX: {{ old('start_x', 0) }}, startY: {{ old('start_y', 0) }}, requiredCircuits: {{ old('required_circuits', 1) }} }">
                            <div class="mb-4">
                                <x-breeze.input-label for="start_x" value="Start X" />
                                <x-breeze.text-input
                                    id="start_x"
                                    class="block mt-1 w-full"
                                    type="number"
                                    name="start_x"
                                    x-model="startX"
                                    @change="$dispatch('update-start-position', { x: parseInt(startX), y: parseInt(startY) })"
                                    min="0"
                                    required
                                />
                                <x-breeze.input-error :messages="$errors->get('start_x')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-breeze.input-label for="start_y" value="Start Y" />
                                <x-breeze.text-input
                                    id="start_y"
                                    class="block mt-1 w-full"
                                    type="number"
                                    name="start_y"
                                    x-model="startY"
                                    @change="$dispatch('update-start-position', { x: parseInt(startX), y: parseInt(startY) })"
                                    min="0"
                                    required
                                />
                                <x-breeze.input-error :messages="$errors->get('start_y')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-breeze.input-label for="required_circuits" value="Required Circuits" />
                                <x-breeze.text-input
                                    id="required_circuits"
                                    class="block mt-1 w-full"
                                    type="number"
                                    name="required_circuits"
                                    x-model="requiredCircuits"
                                    @change="$dispatch('update-required-circuits', { count: parseInt(requiredCircuits) })"
                                    min="0"
                                    required
                                />
                                <x-breeze.input-error :messages="$errors->get('required_circuits')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mb-4">
                            <x-breeze.input-label value="Grid Editor" class="mb-2" />
                            <x-breeze.input-error :messages="$errors->get('tiles')" class="mt-2" />

                            <livewire:grid-editor
                                :tiles="old('tiles') ? json_decode(old('tiles'), true) : []"
                                :grid-width="old('grid_width', 5)"
                                :grid-height="old('grid_height', 5)"
                                :start-x="old('start_x', 0)"
                                :start-y="old('start_y', 0)"
                                :required-circuits="old('required_circuits', 1)"
                            />
                        </div>

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
