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
}
