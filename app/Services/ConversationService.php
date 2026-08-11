<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ConversationService
{
    /**
     * Transcribe audio file using Groq Whisper API.
     */
    public static function transcribe(string $audioPath): ?string
    {
        try {
            $response = Http::withToken(env('GROQ_API_KEY'))
                ->retry(3, 300)
                ->timeout(30)
                ->attach('file', file_get_contents($audioPath), 'audio.webm')
                ->post('https://api.groq.com/openai/v1/audio/transcriptions', [
                    'model' => 'whisper-large-v3-turbo',
                    'language' => 'en',
                    'response_format' => 'json',
                ]);

            if ($response->successful()) {
                return $response->json()['text'] ?? null;
            }

            Log::error('Groq Whisper failed', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        } catch (\Exception $e) {
            Log::error('Groq Whisper exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Generate AI conversation reply using Groq chat completion.
     * Returns structured JSON with reply text and corrections.
     */
    public static function reply(array $conversationHistory, string $scenario, string $level = 'B1-B2'): ?array
    {
        $systemPrompt = <<<PROMPT
You are a friendly, patient English conversation partner. You are role-playing a "{$scenario}" scenario.

Rules:
1. Stay in character for the scenario. Be natural, warm, and conversational.
2. Keep your replies to 2-4 sentences — short and conversational, like real human speech.
3. Target complexity level for learner: {$level}. Adjust your vocabulary depth, sentence length, and idioms to match this level.
4. CORRECTION RULES:
   - ONLY correct genuine English grammar, verb tense, preposition, or word choice errors (e.g. "I went to join" -> "I came to join").
   - NEVER correct proper nouns, personal names, brand names, or company names (e.g., do NOT correct "Serka" or "Google").
   - Do NOT be overly pedantic or invent silly corrections. If the sentence is grammatically natural and clear, do not add fake corrections.
5. Always end with a natural follow-up question or thought to keep the conversation flowing.

Return ONLY a raw JSON object with NO markdown, NO code blocks:
{
  "reply": "Your natural conversational response here",
  "corrections": [
    {"wrong": "actual mistake", "correct": "natural correction", "reason": "brief helpful explanation"}
  ]
}

If there are no actual grammar or vocabulary mistakes, return: "corrections": []
PROMPT;

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        foreach ($conversationHistory as $msg) {
            $messages[] = [
                'role' => $msg['role'],
                'content' => $msg['content'],
            ];
        }

        try {
            $response = Http::withToken(env('GROQ_API_KEY'))
                ->retry(3, 300)
                ->timeout(30)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'llama-3.3-70b-versatile',
                    'response_format' => ['type' => 'json_object'],
                    'messages' => $messages,
                ]);

            if ($response->successful()) {
                $raw = $response->json()['choices'][0]['message']['content'] ?? null;
                if (!$raw) return null;

                $data = json_decode($raw, true);
                if (!$data || !isset($data['reply'])) return null;

                if (!isset($data['corrections']) || !is_array($data['corrections'])) {
                    $data['corrections'] = [];
                }

                return $data;
            }

            Log::error('Groq Chat failed', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        } catch (\Exception $e) {
            Log::error('Groq Chat exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Generate the AI's opening message for a scenario.
     */
    public static function openConversation(string $scenario, string $level = 'B1-B2'): ?array
    {
        $history = [
            ['role' => 'user', 'content' => "Please start the conversation. You are initiating a \"{$scenario}\" scenario for a {$level} level learner. Greet me in-character with your opening line. Keep it short (1-2 sentences) and inviting."]
        ];

        return self::reply($history, $scenario, $level);
    }
}
