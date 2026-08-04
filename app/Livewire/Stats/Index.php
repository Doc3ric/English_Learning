<?php

namespace App\Livewire\Stats;

use Livewire\Component;
use App\Models\Goal;
use App\Models\Vocabulary;
use App\Models\GrammarLesson;
use App\Models\ReadingAttempt;
use App\Models\JournalEntry;
use App\Models\StudySession;
use App\Models\Mistake;
use Carbon\Carbon;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    public $goal;

    // Editable goal targets
    public $target_vocabulary;
    public $target_grammar;
    public $target_reading;
    public $target_writing;
    public $target_study_time;
    
    public $isEditingGoals = false;

    public function mount()
    {
        $this->goal = Goal::firstOrCreate([], [
            'target_vocabulary' => 70,
            'target_grammar' => 5,
            'target_reading' => 7,
            'target_writing' => 7,
            'target_study_time' => 480,
        ]);

        $this->target_vocabulary = $this->goal->target_vocabulary;
        $this->target_grammar = $this->goal->target_grammar;
        $this->target_reading = $this->goal->target_reading;
        $this->target_writing = $this->goal->target_writing;
        $this->target_study_time = $this->goal->target_study_time;
    }

    public function saveGoals()
    {
        $this->validate([
            'target_vocabulary' => 'required|integer|min:1',
            'target_grammar' => 'required|integer|min:1',
            'target_reading' => 'required|integer|min:1',
            'target_writing' => 'required|integer|min:1',
            'target_study_time' => 'required|integer|min:1',
        ]);

        $this->goal->update([
            'target_vocabulary' => $this->target_vocabulary,
            'target_grammar' => $this->target_grammar,
            'target_reading' => $this->target_reading,
            'target_writing' => $this->target_writing,
            'target_study_time' => $this->target_study_time,
        ]);

        $this->isEditingGoals = false;
        session()->flash('message', 'Goals updated successfully!');
    }

    private function calculateStreak()
    {
        $dates = StudySession::selectRaw('DATE(created_at) as date')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->pluck('date')
            ->toArray();

        if (empty($dates)) {
            return 0;
        }

        $streak = 0;
        $currentDate = Carbon::today();
        
        // Check if the most recent study session was today or yesterday
        $mostRecent = Carbon::parse($dates[0]);
        if (!$mostRecent->isToday() && !$mostRecent->isYesterday()) {
            return 0; // Streak is broken
        }

        // We start checking from the most recent logged date
        $checkDate = $mostRecent->copy();

        foreach ($dates as $date) {
            $parsedDate = Carbon::parse($date);
            if ($parsedDate->isSameDay($checkDate)) {
                $streak++;
                $checkDate->subDay();
            } else {
                break; // Gap found
            }
        }

        return $streak;
    }

    public function render()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        // Weekly Progress
        $progress = [
            'vocabulary' => Vocabulary::whereNotNull('example_sentence')->whereBetween('updated_at', [$startOfWeek, $endOfWeek])->count(),
            'grammar' => GrammarLesson::where('is_completed', true)->whereBetween('updated_at', [$startOfWeek, $endOfWeek])->count(),
            'reading' => ReadingAttempt::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count(),
            'writing' => JournalEntry::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count(),
            'study_time' => round(StudySession::whereBetween('created_at', [$startOfWeek, $endOfWeek])->sum('duration_seconds') / 60),
        ];

        // Overall Stats
        $overall = [
            'vocabulary' => Vocabulary::count(),
            'grammar' => GrammarLesson::where('is_completed', true)->count(),
            'reading' => ReadingAttempt::count(),
            'writing' => JournalEntry::count(),
            'study_time' => round(StudySession::sum('duration_seconds') / 60),
        ];

        // 12C: Weakness Analysis — mistakes by category, last 30 days
        $weaknesses = Mistake::selectRaw('category, count(*) as total')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $topWeakness = $weaknesses->first();

        // 13F: Reading Analytics
        $avgWpm = round(\App\Models\ReadingAttempt::avg('words_per_minute') ?? 0);

        $readingSessions = \App\Models\ReadingSession::where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->whereNotNull('quiz_score')
            ->latest()
            ->limit(10)
            ->get()
            ->reverse()
            ->values();

        $readingChartLabels = $readingSessions->map(fn($s) => $s->created_at->format('M d'))->toArray();
        $readingQuizScores = $readingSessions->map(fn($s) => ($s->quiz_score / 5) * 100)->toArray();
        $readingSummaryScores = $readingSessions->map(fn($s) => $s->summary_score)->toArray();

        $user = \Illuminate\Support\Facades\Auth::user();
        $readingLevel = $user->reading_cefr_level ?? $user->level ?? 'B1';
        $totalQuizzes = \App\Models\ReadingSession::where('user_id', $user->id)->whereNotNull('quiz_score')->count();
        $sessionsUntilCheck = $totalQuizzes >= 3 ? 1 : max(0, 3 - $totalQuizzes);

        return view('livewire.stats.index', [
            'progress'     => $progress,
            'overall'      => $overall,
            'streak'       => $this->calculateStreak(),
            'startOfWeek'  => $startOfWeek->format('M d'),
            'endOfWeek'    => $endOfWeek->format('M d'),
            'weaknesses'   => $weaknesses,
            'topWeakness'  => $topWeakness,
            'avgWpm'       => $avgWpm,
            'readingChartLabels'   => $readingChartLabels,
            'readingQuizScores'    => $readingQuizScores,
            'readingSummaryScores' => $readingSummaryScores,
            'readingLevel'         => $readingLevel,
            'sessionsUntilCheck'   => $sessionsUntilCheck,
        ]);
    }
}
