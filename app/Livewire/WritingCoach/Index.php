<?php

namespace App\Livewire\WritingCoach;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Services\WritingCoachService;
use App\Models\WritingSession;
use App\Models\JournalEntry;
use App\Models\Vocabulary;
use App\Models\Mistake;
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

    // 12B auto-population counts (for results display)
    public int $vocabAdded = 0;
    public int $mistakesAdded = 0;
    public bool $journalSaved = false;

    // Rewrite (self-practice)
    public bool $showRewriteBox = false;
    public string $rewriteAttempt = '';
    public bool $rewriteSaved = false;

    // 12E: Style rewrites
    public ?string $professionalVersion = null;
    public ?string $nativeVersion = null;
    public bool $loadingProfessional = false;
    public bool $loadingNative = false;

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
        $basePrompt = self::PROMPTS[date('z') % count(self::PROMPTS)];

        // 12C: if there's a clear top weakness, occasionally target it
        $topWeakness = \App\Models\Mistake::selectRaw('category, count(*) as total')
            ->where('created_at', '>=', \Carbon\Carbon::now()->subDays(30))
            ->groupBy('category')
            ->orderByDesc('total')
            ->first();

        if ($topWeakness && $topWeakness->total >= 3) {
            // Wrap the base prompt with a weakness-aware instruction
            $this->prompt = $basePrompt . ' (Focus on using ' . $topWeakness->category . ' correctly — this is your current area to improve.)';
        } else {
            $this->prompt = $basePrompt;
        }
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

        // 12F: Build lightweight memory context from last 1-2 sessions
        $memoryContext = WritingCoachService::buildMemoryContext(Auth::id());

        // Call Groq (now with optional memory context)
        $result = WritingCoachService::analyze($this->prompt, $this->userResponse, $memoryContext);

        if (!$result) {
            $this->state = 'writing';
            $this->errorMessage = 'The AI analysis failed. Please check your connection and try again.';
            return;
        }

        // Save to DB
        $session = WritingSession::create([
            'user_id'              => Auth::id(),
            'prompt_topic'         => $this->prompt,
            'user_response'        => $this->userResponse,
            'word_count'           => $this->wordCount,
            'ai_corrected_version' => $result['corrected_version'],
            'ai_explanation'       => $result['explanation'],
            'grammar_score'        => $result['grammar_score'],
            'vocabulary_score'     => $result['vocabulary_score'],
            'naturalness_score'    => $result['naturalness_score'],
            'clarity_score'        => $result['clarity_score'],
            'cefr_estimate'        => $result['cefr_estimate'],
            'memory_context'       => $memoryContext,
        ]);

        $this->result = $result;
        $this->sessionId = $session->id;

        // ---- 12B: Auto-populate existing modules ----

        // 1. Journal — every writing session is a journal entry
        JournalEntry::create([
            'title'      => $this->prompt,
            'content'    => $this->userResponse,
            'word_count' => $this->wordCount,
            'source'     => 'writing_coach',
        ]);
        $this->journalSaved = true;

        // 2. Vocabulary — suggested words from AI
        $this->vocabAdded = 0;
        foreach (($result['suggested_vocabulary'] ?? []) as $v) {
            $word = trim($v['word'] ?? '');
            if (!$word) continue;
            // Avoid exact duplicates
            $exists = Vocabulary::whereRaw('LOWER(word) = ?', [strtolower($word)])->exists();
            if (!$exists) {
                Vocabulary::create([
                    'word'         => $word,
                    'meaning'      => $v['meaning'] ?? '',
                    'part_of_speech' => $v['part_of_speech'] ?? '',
                    'source'       => 'writing_coach',
                ]);
                $this->vocabAdded++;
            }
        }

        // 3. Mistakes — each mistake found by AI
        $this->mistakesAdded = 0;
        $allowedCategories = ['Grammar', 'Vocabulary', 'Writing'];
        foreach (($result['mistakes_found'] ?? []) as $m) {
            $wrongText = trim($m['wrong_text'] ?? '');
            $correctText = trim($m['correct_text'] ?? '');
            if (!$wrongText || !$correctText) continue;
            $cat = in_array($m['category'] ?? '', $allowedCategories) ? $m['category'] : 'Grammar';
            Mistake::create([
                'wrong_text'   => $wrongText,
                'correct_text' => $correctText,
                'reason'       => $m['reason'] ?? '',
                'category'     => $cat,
                'source'       => 'writing_coach',
            ]);
            $this->mistakesAdded++;
        }

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

    // ---- 12E: Style Rewrites ----

    public function makeProfessional()
    {
        if (!$this->result || $this->loadingProfessional) return;
        $this->loadingProfessional = true;

        $text = $this->professionalVersion = WritingCoachService::rewriteInStyle(
            $this->result['corrected_version'], 'professional'
        );

        // Persist to DB
        if ($text) {
            $session = WritingSession::find($this->sessionId);
            $session?->update(['professional_version' => $text]);
        }

        $this->loadingProfessional = false;
    }

    public function makeNative()
    {
        if (!$this->result || $this->loadingNative) return;
        $this->loadingNative = true;

        $text = $this->nativeVersion = WritingCoachService::rewriteInStyle(
            $this->result['corrected_version'], 'native'
        );

        // Persist to DB
        if ($text) {
            $session = WritingSession::find($this->sessionId);
            $session?->update(['native_version' => $text]);
        }

        $this->loadingNative = false;
    }

    public function startNewSession()
    {
        $this->reset([
            'userResponse', 'wordCount', 'showWordWarning', 'result', 'sessionId',
            'errorMessage', 'showRewriteBox', 'rewriteAttempt', 'rewriteSaved',
            'professionalVersion', 'nativeVersion', 'loadingProfessional', 'loadingNative',
        ]);
        $this->state = 'writing';
    }

    public function render()
    {
        return view('livewire.writing-coach.index');
    }
}
