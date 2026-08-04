<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIReadingService
{
    /**
     * Generate a levelled reading article via a single Groq API call.
     *
     * @param  string  $topic      The article topic (from rotating list)
     * @param  string  $cefrLevel  The learner's CEFR level (e.g. "B1")
     * @return array|null          Parsed result array or null on failure
     */
    public static function generate(string $topic, string $cefrLevel): ?array
    {
        $level = strtoupper(trim($cefrLevel));

        $prompt = <<<PROMPT
You are an expert English language teacher and writer. Write a short, engaging informational article for an English learner at the {$level} CEFR level.

Topic: "{$topic}"

Requirements:
1. Length: strictly between 300 and 600 words.
2. Vocabulary and sentence complexity MUST be appropriate for the {$level} level — not simpler, not harder.
3. Format: flowing prose paragraphs only. Do NOT use headers, bullet points, numbered lists, or markdown formatting of any kind inside the article.
4. Tone: informative, engaging, and encouraging for a language learner.
5. Choose a specific, interesting angle on the topic — not just a generic overview.
6. Write a clear, descriptive title for the article.
7. Calculate the exact word count of the article body (excluding the title).
8. Estimate reading time in minutes (assume 150 words per minute for a learner at this level, rounded up to the nearest whole number).

Return ONLY a raw JSON object with NO markdown, NO code blocks, NO extra text — just the JSON:
{
  "title": "A clear, specific article title",
  "article": "The full article text here as flowing prose paragraphs separated by newlines...",
  "word_count": 420,
  "estimated_read_time": 3,
  "cefr_level": "{$level}"
}
PROMPT;

        try {
            $response = Http::withToken(env('GROQ_API_KEY'))
                ->timeout(60)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'           => 'llama-3.3-70b-versatile',
                    'response_format' => ['type' => 'json_object'],
                    'messages'        => [
                        [
                            'role'    => 'system',
                            'content' => 'You are an expert English language teacher. Always return valid JSON only — no markdown, no code blocks.',
                        ],
                        [
                            'role'    => 'user',
                            'content' => $prompt,
                        ],
                    ],
                ]);

            if ($response->successful()) {
                $raw  = $response->json()['choices'][0]['message']['content'] ?? null;
                $data = $raw ? json_decode($raw, true) : null;

                if (!$data) return null;

                // Normalise: article might come back as an array of paragraphs
                if (is_array($data['article'])) {
                    $data['article'] = implode("\n\n", $data['article']);
                }

                // Validate required keys
                $required = ['title', 'article', 'word_count', 'estimated_read_time', 'cefr_level'];
                foreach ($required as $key) {
                    if (empty($data[$key])) return null;
                }

                // Clamp/sanitise numeric fields
                $data['word_count']           = max(1, (int) $data['word_count']);
                $data['estimated_read_time']  = max(1, (int) $data['estimated_read_time']);

                return $data;
            }

            Log::error('AIReadingService: Groq API error', ['body' => $response->body()]);
            return null;

        } catch (\Exception $e) {
            Log::error('AIReadingService exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * 13B: Evaluate a learner's summary against the original article.
     *
     * @param  string  $articleText   The full generated article
     * @param  string  $userSummary   What the learner wrote
     * @param  string  $cefrLevel     Learner's CEFR level (for feedback calibration)
     * @return array|null
     */
    public static function evaluateSummary(string $articleText, string $userSummary, string $cefrLevel): ?array
    {
        $level = strtoupper(trim($cefrLevel));

        $prompt = <<<PROMPT
You are an expert English language teacher evaluating a learner's reading summary.

The learner is at the {$level} CEFR level. They read the following article and then wrote a summary from memory — without looking at the article.

--- ORIGINAL ARTICLE ---
{$articleText}
--- END OF ARTICLE ---

--- LEARNER'S SUMMARY ---
{$userSummary}
--- END OF SUMMARY ---

Evaluate the summary on these dimensions:
1. Accuracy: Did they correctly capture the key ideas? Were there any significant misunderstandings?
2. Completeness: What important points from the article are missing from the summary?
3. Grammar: Are the learner's sentences grammatically correct and well-structured?
4. Vocabulary: Could they have used more precise or advanced vocabulary? Give specific upgrade suggestions.
5. Overall: Write an encouraging 2–3 sentence overall assessment appropriate for a {$level} learner.

Return ONLY a raw JSON object — no markdown, no code blocks, just JSON:
{
  "score": 72,
  "overall_feedback": "Good effort! You identified the main theme clearly. Keep practising to catch more specific details.",
  "accuracy_feedback": "Your summary is mostly accurate. One key idea was slightly misrepresented: ...",
  "grammar_feedback": "Your sentences are generally clear and well-structured. One small issue: ...",
  "missing_ideas": [
    "Music has been used as a tool for social movements and protests throughout history.",
    "The article specifically mentioned how music connects people across cultural boundaries."
  ],
  "vocabulary_suggestions": [
    "Instead of 'good', try 'significant' or 'meaningful' — these are more precise at the {$level} level.",
    "Instead of 'show', 'demonstrate' or 'reflect' would sound more natural here."
  ]
}
PROMPT;

        try {
            $response = Http::withToken(env('GROQ_API_KEY'))
                ->timeout(60)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'           => 'llama-3.3-70b-versatile',
                    'response_format' => ['type' => 'json_object'],
                    'messages'        => [
                        [
                            'role'    => 'system',
                            'content' => 'You are an expert English language teacher. Evaluate reading summaries fairly and encouragingly. Return valid JSON only.',
                        ],
                        [
                            'role'    => 'user',
                            'content' => $prompt,
                        ],
                    ],
                ]);

            if ($response->successful()) {
                $raw  = $response->json()['choices'][0]['message']['content'] ?? null;
                $data = $raw ? json_decode($raw, true) : null;

                if (!$data) return null;

                // Normalise arrays — Groq occasionally returns strings
                if (is_string($data['missing_ideas'] ?? '')) {
                    $data['missing_ideas'] = array_filter(explode("\n", $data['missing_ideas']));
                }
                if (is_string($data['vocabulary_suggestions'] ?? '')) {
                    $data['vocabulary_suggestions'] = array_filter(explode("\n", $data['vocabulary_suggestions']));
                }
                if (!is_array($data['missing_ideas'])) {
                    $data['missing_ideas'] = [];
                }
                if (!is_array($data['vocabulary_suggestions'])) {
                    $data['vocabulary_suggestions'] = [];
                }

                // Validate required keys
                $required = ['score', 'overall_feedback', 'accuracy_feedback', 'grammar_feedback'];
                foreach ($required as $key) {
                    if (!isset($data[$key])) return null;
                }

                // Clamp score
                $data['score'] = max(0, min(100, (int) $data['score']));

                return $data;
            }

            Log::error('AIReadingService::evaluateSummary Groq error', ['body' => $response->body()]);
            return null;

        } catch (\Exception $e) {
            Log::error('AIReadingService::evaluateSummary exception: ' . $e->getMessage());
            return null;
        }
    }
}
