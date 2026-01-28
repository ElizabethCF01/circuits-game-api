<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <div class="flex items-center mb-4">
            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-2">
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
            </div>
            <h3 class="ml-3 text-lg font-semibold text-gray-800">Programming Trivia</h3>
            @if($difficulty)
                <span class="ml-auto text-xs font-medium px-2 py-1 rounded-full
                    {{ $difficulty === 'easy' ? 'bg-green-100 text-green-700' : '' }}
                    {{ $difficulty === 'medium' ? 'bg-yellow-100 text-yellow-700' : '' }}
                    {{ $difficulty === 'hard' ? 'bg-red-100 text-red-700' : '' }}">
                    {{ ucfirst($difficulty) }}
                </span>
            @endif
        </div>

        @if($error)
            <p class="text-gray-500 text-sm">Unable to load trivia question.</p>
            <button wire:click="nextQuestion" class="mt-3 text-sm text-blue-600 hover:text-blue-800 font-medium">
                Try Again
            </button>
        @elseif($question)
            <p class="text-gray-700 mb-4">{{ $question }}</p>

            @if(!$answered)
                <div class="flex gap-3">
                    <button wire:click="answer('True')" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium transition-colors">
                        True
                    </button>
                    <button wire:click="answer('False')" class="flex-1 px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 font-medium transition-colors">
                        False
                    </button>
                </div>
            @else
                <div class="mb-3 p-3 rounded-md {{ $selectedAnswer === $correctAnswer ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
                    <p class="font-medium {{ $selectedAnswer === $correctAnswer ? 'text-green-700' : 'text-red-700' }}">
                        {{ $selectedAnswer === $correctAnswer ? 'Correct!' : 'Incorrect!' }}
                    </p>
                    <p class="text-sm text-gray-600 mt-1">The answer is <span class="font-semibold">{{ $correctAnswer }}</span>.</p>
                </div>
                <button wire:click="nextQuestion" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                    Next Question →
                </button>
            @endif
        @else
            <p class="text-gray-400 text-sm">Loading...</p>
        @endif
    </div>
</div>
