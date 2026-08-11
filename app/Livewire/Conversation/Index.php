<?php

namespace App\Livewire\Conversation;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\ConversationSession;
use App\Models\ConversationMessage;
use App\Models\Mistake;
use App\Services\ConversationService;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithFileUploads;

    public $audioFile;
    public $userTextInput = '';
    public $sessionId = null;
    public $scenario = '';
    public $targetLevel = 'B1-B2'; // A1-A2, B1-B2, C1-C2
    public $messages = [];
    public $isLoading = false;
    public $errorMessage = null;
    public $state = 'scenarios'; // scenarios | chat | recap
    public $totalCorrectionsCount = 0;
    public $inputMode = 'voice'; // voice | text

    public const SCENARIOS = [
        ['id' => 'casual', 'icon' => '☕', 'title' => 'Casual Chat', 'desc' => 'Friendly everyday conversation'],
        ['id' => 'interview', 'icon' => '💼', 'title' => 'Job Interview', 'desc' => 'Practice professional interview skills'],
        ['id' => 'restaurant', 'icon' => '🍕', 'title' => 'Ordering Food', 'desc' => 'Order at a restaurant or café'],
        ['id' => 'travel', 'icon' => '✈️', 'title' => 'Travel', 'desc' => 'Ask for directions, book hotels'],
        ['id' => 'phone', 'icon' => '📞', 'title' => 'Phone Call', 'desc' => 'Practice phone conversations'],
        ['id' => 'doctor', 'icon' => '🏥', 'title' => 'At the Doctor', 'desc' => 'Describe symptoms, understand advice'],
    ];

    public const LEVELS = [
        ['id' => 'A1-A2', 'title' => 'A1-A2 (Beginner)', 'desc' => 'Simple sentences, easy vocabulary'],
        ['id' => 'B1-B2', 'title' => 'B1-B2 (Intermediate)', 'desc' => 'Natural pace, everyday idioms'],
        ['id' => 'C1-C2', 'title' => 'C1-C2 (Advanced)', 'desc' => 'Complex topics, rich expressions'],
    ];

    public function startConversation($scenarioId)
    {
        $scenarioData = collect(self::SCENARIOS)->firstWhere('id', $scenarioId);
        if (!$scenarioData) return;

        $this->scenario = $scenarioData['title'];
        $this->isLoading = true;
        $this->errorMessage = null;
        $this->state = 'chat';
        $this->totalCorrectionsCount = 0;

        // Create session
        $session = ConversationSession::create([
            'user_id' => Auth::id(),
            'scenario' => $this->scenario,
        ]);
        $this->sessionId = $session->id;

        // Get AI opening message
        $result = ConversationService::openConversation($this->scenario, $this->targetLevel);

        if ($result) {
            ConversationMessage::create([
                'session_id' => $this->sessionId,
                'role' => 'assistant',
                'transcript_text' => $result['reply'],
                'corrections' => null,
            ]);

            $this->messages[] = [
                'role' => 'assistant',
                'text' => $result['reply'],
                'corrections' => [],
            ];

            // Dispatch browser event to speak the AI reply
            $this->dispatch('speak-reply', text: $result['reply']);
        } else {
            $this->errorMessage = 'Failed to start conversation. Please check your API connection and try again.';
        }

        $this->isLoading = false;
    }

    public function updatedAudioFile()
    {
        $this->processAudio();
    }

    public function processAudio()
    {
        if (!$this->audioFile || !$this->sessionId) return;

        $this->isLoading = true;
        $this->errorMessage = null;

        // Step 1: Transcribe audio with Groq Whisper
        $transcription = ConversationService::transcribe($this->audioFile->getRealPath());

        if (!$transcription || trim($transcription) === '') {
            $this->errorMessage = 'Could not transcribe your audio. Please try speaking more clearly.';
            $this->isLoading = false;
            $this->audioFile = null;
            return;
        }

        $this->handleUserText($transcription);
        $this->audioFile = null;
    }

    public function sendTextMessage()
    {
        $text = trim($this->userTextInput);
        if ($text === '' || !$this->sessionId || $this->isLoading) return;

        $this->userTextInput = '';
        $this->handleUserText($text);
    }

    protected function handleUserText(string $text)
    {
        $this->isLoading = true;
        $this->errorMessage = null;

        // Save user message
        ConversationMessage::create([
            'session_id' => $this->sessionId,
            'role' => 'user',
            'transcript_text' => $text,
            'corrections' => null,
        ]);

        $this->messages[] = [
            'role' => 'user',
            'text' => $text,
            'corrections' => [],
        ];

        // Step 2: Build conversation history for AI
        $history = [];
        foreach ($this->messages as $msg) {
            $history[] = [
                'role' => $msg['role'],
                'content' => $msg['text'],
            ];
        }

        // Step 3: Get AI reply
        $result = ConversationService::reply($history, $this->scenario, $this->targetLevel);

        if ($result) {
            $corrections = $result['corrections'] ?? [];

            // Auto-save corrections into Mistakes table for targeted weakness practice
            foreach ($corrections as $c) {
                if (!empty($c['wrong']) && !empty($c['correct'])) {
                    $this->totalCorrectionsCount++;
                    Mistake::create([
                        'wrong_text' => $c['wrong'],
                        'correct_text' => $c['correct'],
                        'reason' => $c['reason'] ?? 'Grammar correction',
                        'category' => $c['rule'] ?? 'Grammar',
                        'times_reviewed' => 0,
                        'source' => 'Conversation',
                    ]);
                }
            }

            ConversationMessage::create([
                'session_id' => $this->sessionId,
                'role' => 'assistant',
                'transcript_text' => $result['reply'],
                'corrections' => $corrections,
            ]);

            $this->messages[] = [
                'role' => 'assistant',
                'text' => $result['reply'],
                'corrections' => $corrections,
            ];

            // Award +15 XP per message turn
            if (Auth::user()) {
                Auth::user()->addXp(15);
            }

            // Dispatch browser event to speak the AI reply
            $this->dispatch('speak-reply', text: $result['reply']);
        } else {
            $this->errorMessage = 'AI failed to respond. Please try again.';
        }

        $this->isLoading = false;
    }

    public function finishSession()
    {
        // Award +50 XP completion bonus
        if (Auth::user()) {
            Auth::user()->addXp(50);
        }
        $this->state = 'recap';
    }

    public function newConversation()
    {
        $this->reset(['sessionId', 'scenario', 'messages', 'isLoading', 'errorMessage', 'audioFile', 'userTextInput', 'totalCorrectionsCount']);
        $this->state = 'scenarios';
    }

    public function render()
    {
        return view('livewire.conversation.index')
            ->layout('layouts.app', [
                'title' => 'AI Conversation',
                'fullHeight' => true,
            ]);
    }
}
