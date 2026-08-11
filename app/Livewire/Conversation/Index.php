<?php

namespace App\Livewire\Conversation;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\ConversationSession;
use App\Models\ConversationMessage;
use App\Services\ConversationService;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithFileUploads;

    public $audioFile;
    public $sessionId = null;
    public $scenario = '';
    public $messages = [];
    public $isLoading = false;
    public $errorMessage = null;
    public $state = 'scenarios'; // scenarios | chat

    public const SCENARIOS = [
        ['id' => 'casual', 'icon' => '☕', 'title' => 'Casual Chat', 'desc' => 'Friendly everyday conversation'],
        ['id' => 'interview', 'icon' => '💼', 'title' => 'Job Interview', 'desc' => 'Practice professional interview skills'],
        ['id' => 'restaurant', 'icon' => '🍕', 'title' => 'Ordering Food', 'desc' => 'Order at a restaurant or café'],
        ['id' => 'travel', 'icon' => '✈️', 'title' => 'Travel', 'desc' => 'Ask for directions, book hotels'],
        ['id' => 'phone', 'icon' => '📞', 'title' => 'Phone Call', 'desc' => 'Practice phone conversations'],
        ['id' => 'doctor', 'icon' => '🏥', 'title' => 'At the Doctor', 'desc' => 'Describe symptoms, understand advice'],
    ];

    public function startConversation($scenarioId)
    {
        $scenarioData = collect(self::SCENARIOS)->firstWhere('id', $scenarioId);
        if (!$scenarioData) return;

        $this->scenario = $scenarioData['title'];
        $this->isLoading = true;
        $this->errorMessage = null;
        $this->state = 'chat';

        // Create session
        $session = ConversationSession::create([
            'user_id' => Auth::id(),
            'scenario' => $this->scenario,
        ]);
        $this->sessionId = $session->id;

        // Get AI opening message
        $result = ConversationService::openConversation($this->scenario);

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

        // Save user message
        ConversationMessage::create([
            'session_id' => $this->sessionId,
            'role' => 'user',
            'transcript_text' => $transcription,
            'corrections' => null,
        ]);

        $this->messages[] = [
            'role' => 'user',
            'text' => $transcription,
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
        $result = ConversationService::reply($history, $this->scenario);

        if ($result) {
            ConversationMessage::create([
                'session_id' => $this->sessionId,
                'role' => 'assistant',
                'transcript_text' => $result['reply'],
                'corrections' => $result['corrections'] ?? [],
            ]);

            $this->messages[] = [
                'role' => 'assistant',
                'text' => $result['reply'],
                'corrections' => $result['corrections'] ?? [],
            ];

            // Placeholder: XP would hook in here
            // auth()->user()?->addXp(15);

            // Dispatch browser event to speak the AI reply
            $this->dispatch('speak-reply', text: $result['reply']);
        } else {
            $this->errorMessage = 'AI failed to respond. Please try again.';
        }

        $this->isLoading = false;
        $this->audioFile = null;
    }

    public function newConversation()
    {
        $this->reset(['sessionId', 'scenario', 'messages', 'isLoading', 'errorMessage', 'audioFile']);
        $this->state = 'scenarios';
    }

    public function render()
    {
        return view('livewire.conversation.index')
            ->layout('layouts.app', ['title' => 'AI Conversation']);
    }
}
