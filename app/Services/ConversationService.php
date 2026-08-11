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
    public static function reply(array $conversationHistory, string $scenario): ?array
    {
        $systemPrompt = <<<PROMPT
You are a friendly, patient English conversation partner. You are role-playing a "{$scenario}" scenario.

Rules:
1. Stay in character for the scenario. Be natural and conversational.
2. Keep your replies to 2-4 sentences — short and conversational, like real speech.
3. If the user makes grammar, vocabulary, or pronunciation mistakes in their message, note corrections.
4. Ask follow-up questions to keep the conversation flowing naturally.
5. Adjust your language complexity to match the learner's apparent level.

Return ONLY a raw JSON object with NO markdown, NO code blocks:
{
  "reply": "Your natural conversational response here",
  "corrections": [
    {"wrong": "what user said wrong", "correct": "correct version", "reason": "brief explanation"}
  ]
}

If there are no corrections, return an empty array for corrections: "corrections": []
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
    public static function openConversation(string $scenario): ?array
    {
        $history = [
            ['role' => 'user', 'content' => "Please start the conversation. You are initiating a \"{$scenario}\" scenario. Greet me and set the scene with your opening line. Remember: stay in character, keep it short (1-2 sentences), and make it feel natural."]
        ];

        return self::reply($history, $scenario);
    }
}
