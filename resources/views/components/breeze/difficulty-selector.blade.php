@props(['value' => 'easy', 'name' => 'difficulty'])

<div {{ $attributes->merge(['class' => 'flex rounded-md shadow-sm']) }} x-data="{ selected: '{{ $value }}' }">
    <label
        class="flex-1 cursor-pointer"
        :class="selected === 'easy' ? 'z-10' : ''"
    >
        <input type="radio" name="{{ $name }}" value="easy" x-model="selected" class="sr-only" required>
        <span
            class="flex items-center justify-center h-[34px] px-4 text-sm font-medium border rounded-l-md transition-all"
            :class="selected === 'easy'
                ? 'bg-green-500 text-white border-green-500'
                : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
        >Easy</span>
    </label>
    <label
        class="flex-1 cursor-pointer -ml-px"
        :class="selected === 'medium' ? 'z-10' : ''"
    >
        <input type="radio" name="{{ $name }}" value="medium" x-model="selected" class="sr-only">
        <span
            class="flex items-center justify-center h-[34px] px-4 text-sm font-medium border transition-all"
            :class="selected === 'medium'
                ? 'bg-yellow-500 text-white border-yellow-500'
                : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
        >Medium</span>
    </label>
    <label
        class="flex-1 cursor-pointer -ml-px"
        :class="selected === 'hard' ? 'z-10' : ''"
    >
        <input type="radio" name="{{ $name }}" value="hard" x-model="selected" class="sr-only">
        <span
            class="flex items-center justify-center h-[34px] px-4 text-sm font-medium border rounded-r-md transition-all"
            :class="selected === 'hard'
                ? 'bg-red-500 text-white border-red-500'
                : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
        >Hard</span>
    </label>
</div>
