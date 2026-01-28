<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TriviaService;
use Illuminate\Http\JsonResponse;

/**
 * @group Trivia
 *
 * APIs for programming trivia questions
 */
class TriviaController extends Controller
{
    /**
     * Get a trivia question
     *
     * Fetch a random true/false programming trivia question from Open Trivia DB.
     */
    public function index(TriviaService $triviaService): JsonResponse
    {
        $question = $triviaService->getQuestion();

        if (! $question) {
            return response()->json([
                'message' => 'Unable to fetch trivia question. Please try again later.',
            ], 503);
        }

        return response()->json($question);
    }
}
