<?php

namespace App\Livewire\WritingCoach;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Services\WritingCoachService;
use App\Models\WritingSession;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class Index extends Component
{
    // States: 'writing', 'loading', 'results'
    public string $state = 'writing';

    public string $userResponse = '';
    public int $wordCount = 0;
    public bool $showWordWarning = false;

    // Results
    public ?array $result = null;
    public ?int $sessionId = null;
    public ?string $errorMessage = null;

    // Rewrite
    public bool $showRewriteBox = false;
    public string $rewriteAttempt = '';
    public bool $rewriteSaved = false;

    // Today's rotating prompt
    public string $prompt = '';

    private const PROMPTS = [
        "Describe what you did today.",
        "Explain a challenge you recently faced and how you handled it.",
        "Describe your favorite app or tool and why you use it.",
        "Write about a place you'd like to visit and why.",
        "Describe a person who has influenced you.",
        "Write about a habit you want to build or break.",
        "Describe your morning routine.",
        "Write about something you learned recently.",
        "Describe your ideal work or study environment.",
        "Write about a mistake you made and what you learned from it.",
        "Describe a goal you're working toward.",
        "Write about something you're grateful for today.",
    ];

    public function mount()
    {
        $this->prompt = self::PROMPTS[date('z') % count(self::PROMPTS)];
    }

    public function updatedUserResponse()
    {
        $this->wordCount = $this->countWords($this->userResponse);
        $this->showWordWarning = $this->wordCount < 50 && $this->wordCount > 0;
    }

    private function countWords(string $text): int
    {
        $text = trim(strip_tags($text));
        if (empty($text)) return 0;
        return str_word_count($text);
    }

    public function submit()
    {
        $this->validate([
            'userResponse' => 'required|string|min:20',
        ], [
            'userResponse.required' => 'Please write something before submitting.',
            'userResponse.min' => 'Please write at least a few words.',
        ]);

        $this->wordCount = $this->countWords($this->userResponse);
        $this->state = 'loading';
        $this->errorMessage = null;

        // Call Groq
        $result = WritingCoachService::analyze($this->prompt, $this->userResponse);

        if (!$result) {
            $this->state = 'writing';
            $this->errorMessage = 'The AI analysis failed. Please check your connection and try again.';
            return;
        }

        // Save to DB
        $session = WritingSession::create([
            'user_id' => Auth::id(),
            'prompt_topic' => $this->prompt,
            'user_response' => $this->userResponse,
            'word_count' => $this->wordCount,
            'ai_corrected_version' => $result['corrected_version'],
            'ai_explanation' => $result['explanation'],
            'grammar_score' => $result['grammar_score'],
            'vocabulary_score' => $result['vocabulary_score'],
            'naturalness_score' => $result['naturalness_score'],
            'clarity_score' => $result['clarity_score'],
            'cefr_estimate' => $result['cefr_estimate'],
        ]);

        $this->result = $result;
        $this->sessionId = $session->id;
        $this->state = 'results';
    }

    public function saveRewrite()
    {
        $this->validate([
            'rewriteAttempt' => 'required|string|min:10',
        ]);

        $session = WritingSession::find($this->sessionId);
        if ($session) {
            $session->update(['rewrite_attempt' => $this->rewriteAttempt]);
        }

        $this->rewriteSaved = true;
    }

    public function startNewSession()
    {
        $this->reset(['userResponse', 'wordCount', 'showWordWarning', 'result', 'sessionId', 'errorMessage', 'showRewriteBox', 'rewriteAttempt', 'rewriteSaved']);
        $this->state = 'writing';
    }

    public function render()
    {
        return view('livewire.writing-coach.index');
    }
}
