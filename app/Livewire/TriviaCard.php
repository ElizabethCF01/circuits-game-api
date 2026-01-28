<?php

namespace App\Livewire;

use App\Services\TriviaService;
use Livewire\Component;

class TriviaCard extends Component
{
    public ?string $question = null;
    public ?string $difficulty = null;
    public ?string $correctAnswer = null;
    public ?string $selectedAnswer = null;
    public bool $answered = false;
    public bool $error = false;

    public function mount(TriviaService $triviaService): void
    {
        $this->loadQuestion($triviaService);
    }

    public function answer(string $answer): void
    {
        $this->selectedAnswer = $answer;
        $this->answered = true;
    }

    public function nextQuestion(TriviaService $triviaService): void
    {
        $this->loadQuestion($triviaService);
    }

    private function loadQuestion(TriviaService $triviaService): void
    {
        $this->reset(['question', 'difficulty', 'correctAnswer', 'selectedAnswer', 'answered', 'error']);

        $data = $triviaService->getQuestion();

        if ($data) {
            $this->question = $data['question'];
            $this->difficulty = $data['difficulty'];
            $this->correctAnswer = $data['correct_answer'];
        } else {
            $this->error = true;
        }
    }

    public function render()
    {
        return view('livewire.trivia-card');
    }
}
