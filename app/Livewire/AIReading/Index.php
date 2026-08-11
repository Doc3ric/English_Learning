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

    // 13D — Extraction Counts
    public int $vocabAddedCount = 0;
    public int $mistakesLoggedCount = 0;

    // 13F — Reading Analytics (WPM)
    public ?int $readingStartTime = null;
    public ?int $timeTakenSeconds = null;
    public ?int $wpm = null;

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
        $readingLevel = strtoupper(trim($user->reading_cefr_level ?? ''));
        $profileLevel = strtoupper(trim($user->level ?? ''));

        if ($readingLevel) {
            $this->cefrLevel = $readingLevel;
        } elseif ($profileLevel) {
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

        // Save reading_cefr_level on the user if it was not set
        $user = Auth::user();
        if (empty($user->reading_cefr_level)) {
            $user->update(['reading_cefr_level' => $this->cefrLevel]);
        }

        $session = ReadingSession::create([
            'user_id'             => $user->id,
            'topic'               => $this->topic,
            'cefr_level'          => $this->cefrLevel,
            'estimated_read_time' => $result['estimated_read_time'],
            'article_text'        => $result['article'],
            'article_title'       => $result['title'],
            'article_word_count'  => $result['word_count'],
        ]);

        // 13D - Auto-insert Vocabulary
        $this->vocabAddedCount = 0;
        if (!empty($result['vocabulary']) && is_array($result['vocabulary'])) {
            foreach ($result['vocabulary'] as $v) {
                if (!empty($v['word']) && !empty($v['definition'])) {
                    \App\Models\Vocabulary::create([
                        'word' => $v['word'],
                        'meaning' => $v['definition'],
                        'example_sentence' => $v['example'] ?? null,
                        'source' => 'reading_coach',
                    ]);
                    $this->vocabAddedCount++;
                }
            }
        }

        $this->article   = $result;
        $this->sessionId = $session->id;
        $this->readingStartTime = now()->timestamp;
        $this->state     = 'reading';
    }

    // ── 13B actions ──────────────────────────────────────────────────────────

    public function startSummary()
    {
        if ($this->readingStartTime) {
            $this->timeTakenSeconds = now()->timestamp - $this->readingStartTime;
            $wordCount = $this->article['word_count'] ?? 0;
            
            if ($this->timeTakenSeconds > 0) {
                $this->wpm = (int) round($wordCount / ($this->timeTakenSeconds / 60));
                
                $session = ReadingSession::find($this->sessionId);
                if ($session) {
                    $session->update([
                        'time_taken_seconds' => $this->timeTakenSeconds,
                        'words_per_minute'   => $this->wpm,
                    ]);
                }
            }
            $this->readingStartTime = null; // Clear so it isn't repeatedly calculated
        }

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

        // 13D - Auto-insert Mistakes
        $this->mistakesLoggedCount = 0;
        if (!empty($result['mistakes']) && is_array($result['mistakes'])) {
            $allowedCategories = ['grammar', 'vocabulary', 'pronunciation', 'writing'];
            foreach ($result['mistakes'] as $m) {
                if (!empty($m['wrong_text']) && !empty($m['correct_text'])) {
                    $cat = strtolower($m['category'] ?? 'writing');
                    if (!in_array($cat, $allowedCategories)) {
                        $cat = 'writing';
                    }
                    \App\Models\Mistake::create([
                        'wrong_text' => $m['wrong_text'],
                        'correct_text' => $m['correct_text'],
                        'reason' => $m['reason'] ?? '',
                        'category' => $cat,
                        'source' => 'reading_coach',
                    ]);
                    $this->mistakesLoggedCount++;
                }
            }
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

        auth()->user()?->addXp(50);
        \App\Services\RecommendationEngineService::logAndComplete(auth()->id() ?? 1, 'reading', (string)$this->sessionId, 600, $this->quizScore);

        // 13E - Adaptive Difficulty Logic
        $this->evaluateLevel();

        $this->state = 'quiz_results';
    }

    private function evaluateLevel()
    {
        $user = Auth::user();
        
        $recentSessions = ReadingSession::where('user_id', $user->id)
            ->whereNotNull('quiz_score')
            ->latest()
            ->limit(3)
            ->get();

        if ($recentSessions->count() < 2) return;

        $levels = ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'];
        $currentLevelIdx = array_search($this->cefrLevel, $levels);
        if ($currentLevelIdx === false) return;

        // Promotion: Last 3 sessions, summary >= 80 AND quiz >= 4
        if ($recentSessions->count() >= 3) {
            $promote = true;
            foreach ($recentSessions as $rs) {
                if ($rs->summary_score < 80 || $rs->quiz_score < 4) {
                    $promote = false;
                    break;
                }
            }
            if ($promote && $currentLevelIdx < 4) { // Max C1
                $newLevel = $levels[$currentLevelIdx + 1];
                $user->update(['reading_cefr_level' => $newLevel]);
                $this->cefrLevel = $newLevel;
                return;
            }
        }

        // Demotion: Last 2 sessions, summary < 50 OR quiz < 3
        $lastTwo = $recentSessions->take(2);
        $demote = true;
        foreach ($lastTwo as $rs) {
            if ($rs->summary_score >= 50 && $rs->quiz_score >= 3) {
                $demote = false;
                break;
            }
        }
        if ($demote && $currentLevelIdx > 1) { // Min A2
            $newLevel = $levels[$currentLevelIdx - 1];
            $user->update(['reading_cefr_level' => $newLevel]);
            $this->cefrLevel = $newLevel;
            return;
        }
    }

    public function startNew()
    {
        $this->reset([
            'article', 'sessionId', 'errorMessage',
            'summaryResponse', 'summaryWordCount', 'showSummaryWarning',
            'summaryResult', 'summaryError',
            'quizData', 'quizAnswers', 'quizScore', 'quizError',
            'readingStartTime', 'timeTakenSeconds', 'wpm'
        ]);
        $this->state = 'idle';
    }

    public function render()
    {
        return view('livewire.ai-reading.index');
    }
}
