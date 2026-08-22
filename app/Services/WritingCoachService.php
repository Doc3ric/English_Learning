<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WritingCoachService
{
    public static function analyze(string $topic, string $userText, ?string $memoryContext = null): ?array
    {
        $memorySection = $memoryContext
            ? "\n\n{$memoryContext}\n"
            : '';
        $prompt = <<<PROMPT
You are an expert English writing coach. A learner has written a short response to a writing prompt. Analyze their writing carefully.

Writing Prompt: "{$topic}"

Learner's Response:
"{$userText}"{$memorySection}

Your task:
1. Correct the text: fix all grammar, spelling, word choice, and naturalness issues.
2. Explain the key corrections in simple, encouraging language (no more than 5-6 bullet points).
3. Score the original writing on 4 dimensions, each from 0–100:
   - grammar_score: correctness of sentence structure, tenses, and punctuation
   - vocabulary_score: appropriateness and range of word choices
   - naturalness_score: how natural and fluent the English sounds to a native speaker
   - clarity_score: how clear and easy to understand the message is
4. Estimate the learner's CEFR level based on this writing (A1, A2, B1, B2, C1, or C2).
5. Suggest 2–4 vocabulary words: words the learner could have used (but didn't) to make the writing more precise or advanced, PLUS any genuinely useful words from your corrected version. For each, provide the word, its meaning, and its part of speech.
6. List all specific mistakes found in the original text. For each mistake provide: the wrong text as written, the correct replacement, a short reason, and the category (must be exactly one of: Grammar, Vocabulary, Writing).

Return ONLY a raw JSON object with NO markdown, NO code blocks, NO extra text — just the JSON:
{
  "corrected_version": "The fully corrected version of the learner's text",
  "explanation": "Clear bullet-point explanation of what was changed and why, written in plain English",
  "grammar_score": 75,
  "vocabulary_score": 60,
  "naturalness_score": 80,
  "clarity_score": 70,
  "cefr_estimate": "B1",
  "suggested_vocabulary": [
    {"word": "punctual", "meaning": "happening or arriving at the agreed or proper time", "part_of_speech": "adjective"},
    {"word": "deadline", "meaning": "the latest time by which something must be completed", "part_of_speech": "noun"}
  ],
  "mistakes_found": [
    {"wrong_text": "I forget", "correct_text": "I forgot", "reason": "Past simple is needed for a completed action in the past.", "category": "Grammar"},
    {"wrong_text": "very disappoint", "correct_text": "very disappointed", "reason": "The adjective form 'disappointed' is required after a verb.", "category": "Grammar"}
  ]
}
PROMPT;

        try {
            $response = Http::withToken(config('services.groq.key', env('GROQ_API_KEY')))
                ->timeout(60)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => config('services.groq.model', 'groq/compound'),
                    'response_format' => ['type' => 'json_object'],
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are an API that outputs strict JSON only. Never include markdown formatting, code blocks, or any text outside the JSON object.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ]
                ]);

            if ($response->successful()) {
                $raw = $response->json()['choices'][0]['message']['content'] ?? null;
                if (!$raw) return null;

                $raw = trim($raw);
                $raw = preg_replace('/^```(?:json)?\s*/i', '', $raw);
                $raw = preg_replace('/\s*```$/', '', $raw);

                $data = json_decode(trim($raw), true);
                if (!$data) return null;

                // Normalize: Groq sometimes returns explanation as an array of bullet strings
                if (is_array($data['explanation'])) {
                    $data['explanation'] = implode("\n", $data['explanation']);
                }
                if (is_array($data['corrected_version'])) {
                    $data['corrected_version'] = implode("\n", $data['corrected_version']);
                }

                // Ensure new array fields exist even if Groq omits them
                if (!isset($data['suggested_vocabulary']) || !is_array($data['suggested_vocabulary'])) {
                    $data['suggested_vocabulary'] = [];
                }
                if (!isset($data['mistakes_found']) || !is_array($data['mistakes_found'])) {
                    $data['mistakes_found'] = [];
                }

                // Validate core required keys are present
                $required = ['corrected_version', 'explanation', 'grammar_score', 'vocabulary_score', 'naturalness_score', 'clarity_score', 'cefr_estimate'];
                foreach ($required as $key) {
                    if (!isset($data[$key])) return null;
                }

                // Clamp scores to 0-100
                foreach (['grammar_score', 'vocabulary_score', 'naturalness_score', 'clarity_score'] as $score) {
                    $data[$score] = max(0, min(100, (int) $data[$score]));
                }

                return $data;
            }

            Log::error('WritingCoachService: Groq API error', ['body' => $response->body()]);
            return null;
        } catch (\Exception $e) {
            Log::error('WritingCoachService exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * 12E: Rewrite the corrected text in a specific style.
     * $style is either 'professional' or 'native'.
     */
    public static function rewriteInStyle(string $correctedText, string $style): ?string
    {
        $styleDesc = $style === 'professional'
            ? 'formal, professional business English — precise vocabulary, clear structure, no contractions'
            : 'natural, fluent, conversational English that sounds exactly like a native speaker — idiomatic phrases, natural rhythm, feels effortless';

        $prompt = "Rewrite the following English text to sound {$styleDesc}. Keep the same meaning and approximate length. Return ONLY the rewritten text — no explanations, no labels, no extra formatting.\n\nOriginal text:\n\"{$correctedText}\"";

        try {
            $response = \Illuminate\Support\Facades\Http::withToken(config('services.groq.key', env('GROQ_API_KEY')))
                ->timeout(45)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'    => config('services.groq.model', 'groq/compound'),
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are an expert English writing coach. Return only the rewritten text, nothing else.'],
                        ['role' => 'user',   'content' => $prompt],
                    ],
                ]);

            if ($response->successful()) {
                $text = trim($response->json()['choices'][0]['message']['content'] ?? '');
                // Strip any stray quotes the model might wrap around the output
                return trim($text, '"\'');
            }

            Log::error("WritingCoachService::rewriteInStyle ({$style}) error", ['body' => $response->body()]);
            return null;
        } catch (\Exception $e) {
            Log::error("WritingCoachService::rewriteInStyle exception: " . $e->getMessage());
            return null;
        }
    }

    /**
     * 12F: Build a lightweight memory context string from the last 1-2 sessions.
     * Returns null if no previous sessions exist.
     */
    public static function buildMemoryContext(int $userId): ?string
    {
        $recent = \App\Models\WritingSession::where('user_id', $userId)
            ->latest()
            ->limit(2)
            ->get(['prompt_topic', 'user_response', 'cefr_estimate', 'created_at']);

        if ($recent->isEmpty()) return null;

        $lines = $recent->map(function ($s) {
            // Summarise the response to ~20 words to keep tokens low
            $words   = explode(' ', strip_tags($s->user_response));
            $snippet = implode(' ', array_slice($words, 0, 20));
            $snippet = count($words) > 20 ? $snippet . '...' : $snippet;
            $date    = $s->created_at->diffForHumans();
            return "- {$date}: Topic \"{$s->prompt_topic}\" — learner wrote: \"{$snippet}\" (CEFR: {$s->cefr_estimate})";
        })->implode("\n");

        return "Recent writing history for context (do NOT repeat this verbatim, use it only to feel personally connected):\n{$lines}";
    }
}
