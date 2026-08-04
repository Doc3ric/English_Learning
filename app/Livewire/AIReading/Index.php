<?php

namespace App\Livewire\AIReading;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Services\AIReadingService;
use App\Models\ReadingSession;
use App\Models\WritingSession;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class Index extends Component
{
    // States: 'idle' | 'loading' | 'reading' | 'summarizing' | 'summary_results'
    public string $state = 'idle';

    // Today's topic and resolved CEFR level
    public string $topic = '';
    public string $cefrLevel = '';

    // Generated article data
    public ?array $article = null;
    public ?int $sessionId = null;
    public ?string $errorMessage = null;

    // 13B — Summary
    public string $summaryResponse = '';
    public int $summaryWordCount = 0;
    public bool $showSummaryWarning = false;
    public ?array $summaryResult = null;
    public ?string $summaryError = null;

    // 13C — Quiz
    public ?array $quizData = null;
    public array $quizAnswers = [];
    public ?int $quizScore = null;
    public ?string $quizError = null;

    /**
     * 14 topics — deliberately different subjects and a +6 day offset
     * from Writing Coach's 12-topic list so they never coincide on the same day.
     */
    private const TOPICS = [
        'The impact of social media on modern communication',
        'How cities around the world are adapting to climate change',
        'The history and global culture of coffee',
        'Artificial intelligence in everyday life',
        'The surprising benefits of learning a second language',
        'Renewable energy and the future of electricity',
        'How sleep affects learning, memory, and performance',
        'The rise of remote work and what it means for cities',
        'Traditional foods and their cultural significance',
        'Space exploration: where humanity is headed next',
        'Mental health awareness in the modern world',
        'The role of music across human cultures and history',
        'How technology is transforming education',
        'Why biodiversity matters and how we can protect it',
    ];

    public function mount()
    {
        $this->topic = self::TOPICS[(date('z') + 6) % count(self::TOPICS)];

        $user         = Auth::user();
        $profileLevel = strtoupper(trim($user->level ?? ''));

        if ($profileLevel) {
            $this->cefrLevel = $profileLevel;
        } else {
            $latest = WritingSession::where('user_id', $user->id)
                ->latest()
                ->value('cefr_estimate');
            $this->cefrLevel = $latest ? strtoupper($latest) : 'B1';
        }
    }

    // ── 13A actions ──────────────────────────────────────────────────────────

    public function generate()
    {
        if ($this->state === 'loading') return;

        $this->state        = 'loading';
        $this->errorMessage = null;

        $result = AIReadingService::generate($this->topic, $this->cefrLevel);

        if (!$result) {
            $this->state        = 'idle';
            $this->errorMessage = 'The article could not be generated. Please check your connection and try again.';
            return;
        }

        $session = ReadingSession::create([
            'user_id'             => Auth::id(),
            'topic'               => $this->topic,
            'cefr_level'          => $this->cefrLevel,
            'estimated_read_time' => $result['estimated_read_time'],
            'article_text'        => $result['article'],
            'article_title'       => $result['title'],
            'article_word_count'  => $result['word_count'],
        ]);

        $this->article   = $result;
        $this->sessionId = $session->id;
        $this->state     = 'reading';
    }

    // ── 13B actions ──────────────────────────────────────────────────────────

    public function startSummary()
    {
        $this->summaryResponse    = '';
        $this->summaryWordCount   = 0;
        $this->showSummaryWarning = false;
        $this->summaryError       = null;
        $this->state              = 'summarizing';
    }

    public function updatedSummaryResponse()
    {
        $text = trim(strip_tags($this->summaryResponse));
        $this->summaryWordCount   = $text ? str_word_count($text) : 0;
        $this->showSummaryWarning = $this->summaryWordCount > 0 && $this->summaryWordCount < 30;
    }

    public function submitSummary()
    {
        $this->validate([
            'summaryResponse' => 'required|string|min:10',
        ], [
            'summaryResponse.required' => 'Please write your summary before submitting.',
            'summaryResponse.min'      => 'Please write a little more.',
        ]);

        $this->summaryError = null;

        if (!$this->article || !$this->sessionId) {
            $this->summaryError = 'Session data missing. Please generate a new article.';
            return;
        }

        $result = AIReadingService::evaluateSummary(
            $this->article['article'],
            $this->summaryResponse,
            $this->cefrLevel
        );

        if (!$result) {
            $this->summaryError = 'The evaluation failed. Please try again.';
            return;
        }

        // Persist back to the 13A session row
        $session = ReadingSession::find($this->sessionId);
        if ($session) {
            $session->update([
                'summary_response'       => $this->summaryResponse,
                'summary_score'          => $result['score'],
                'summary_feedback'       => $result['overall_feedback'],
                'missing_ideas'          => json_encode(array_values($result['missing_ideas'] ?? [])),
                'vocabulary_suggestions' => json_encode(array_values($result['vocabulary_suggestions'] ?? [])),
            ]);
        }

        $this->summaryResult = $result;
        $this->state         = 'summary_results';
    }

    // ── 13C actions ──────────────────────────────────────────────────────────

    public function startQuiz()
    {
        $this->quizError = null;
        $this->state = 'loading_quiz'; // Re-using loading indicator logic

        if (!$this->quizData) {
            $result = AIReadingService::generateQuiz($this->article['article'], $this->cefrLevel);
            if (!$result || empty($result['questions'])) {
                $this->quizError = 'Could not generate quiz. Please try again.';
                $this->state = 'summary_results';
                return;
            }
            $this->quizData = $result;
        }

        $this->state = 'quiz';
    }

    public function submitQuiz()
    {
        $this->quizError = null;

        if (count($this->quizAnswers) < count($this->quizData['questions'])) {
            $this->quizError = 'Please answer all questions before submitting.';
            return;
        }

        $score = 0;
        foreach ($this->quizData['questions'] as $q) {
            $userAns = $this->quizAnswers[$q['id']] ?? null;
            if ($userAns === $q['correct_answer']) {
                $score++;
            }
        }

        $this->quizScore = $score;

        $session = ReadingSession::find($this->sessionId);
        if ($session) {
            $session->update([
                'quiz_data'    => $this->quizData,
                'quiz_score'   => $this->quizScore,
                'quiz_answers' => $this->quizAnswers,
            ]);
        }

        $this->state = 'quiz_results';
    }

    public function startNew()
    {
        $this->reset([
            'article', 'sessionId', 'errorMessage',
            'summaryResponse', 'summaryWordCount', 'showSummaryWarning',
            'summaryResult', 'summaryError',
            'quizData', 'quizAnswers', 'quizScore', 'quizError'
        ]);
        $this->state = 'idle';
    }

    public function render()
    {
        return view('livewire.ai-reading.index');
    }
}
