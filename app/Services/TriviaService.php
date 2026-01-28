<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TriviaService
{
    public function getQuestion(): ?array
    {
        try {
            $response = Http::withoutVerifying()->get(config('services.opentdb.url'), [
                'amount' => 1,
                'category' => 18,
                'type' => 'boolean',
            ]);
        } catch (\Exception $e) {
            return null;
        }

        if ($response->failed() || $response->json('response_code') !== 0) {
            return null;
        }

        $result = $response->json('results.0');

        return [
            'question' => html_entity_decode($result['question']),
            'difficulty' => $result['difficulty'],
            'correct_answer' => $result['correct_answer'],
            'answers' => ['True', 'False'],
        ];
    }
}
