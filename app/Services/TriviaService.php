<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class TriviaService
{
    public function getQuestion(): ?array
    {
        $questions = Cache::get('trivia_questions', []);

        if (empty($questions)) {
            $questions = $this->fetchBatchFromApi();

            if (empty($questions)) {
                return null;
            }

            Cache::put('trivia_questions', $questions, now()->addMinutes(10));
        }

        // Pick a random question and remove it from cache so it doesn't repeat
        $index = array_rand($questions);
        $question = $questions[$index];
        unset($questions[$index]);
        Cache::put('trivia_questions', array_values($questions), now()->addMinutes(10));

        return $question;
    }

    private function fetchBatchFromApi(): array
    {
        try {
            $response = Http::withoutVerifying()
                ->withOptions(['verify' => false])
                ->timeout(10)
                ->get(config('services.opentdb.url'), [
                    'amount' => 20,
                    'category' => 18,
                    'type' => 'boolean',
                ]);
        } catch (\Exception $e) {
            return [];
        }

        if ($response->failed() || $response->json('response_code') !== 0) {
            return [];
        }

        return collect($response->json('results'))->map(fn ($result) => [
            'question' => html_entity_decode($result['question']),
            'difficulty' => $result['difficulty'],
            'correct_answer' => $result['correct_answer'],
            'answers' => ['True', 'False'],
        ])->all();
    }
}
