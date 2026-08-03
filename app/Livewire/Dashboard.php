<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Vocabulary;
use App\Models\GrammarLesson;
use App\Models\ReadingAttempt;
use App\Models\JournalEntry;
use App\Models\StudySession;
use App\Models\WritingSession;
use App\Models\Mistake;
use App\Models\Goal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    private function calculateStreak()
    {
        $dates = StudySession::selectRaw('DATE(created_at) as date')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->pluck('date')
            ->toArray();

        if (empty($dates)) return 0;

        $mostRecent = Carbon::parse($dates[0]);
        if (!$mostRecent->isToday() && !$mostRecent->isYesterday()) return 0;

        $streak = 0;
        $checkDate = $mostRecent->copy();

        foreach ($dates as $date) {
            $parsedDate = Carbon::parse($date);
            if ($parsedDate->isSameDay($checkDate)) {
                $streak++;
                $checkDate->subDay();
            } else {
                break;
            }
        }

        return $streak;
    }

    private function getScoreDeltas()
    {
        // Get today's latest session scores
        $today = WritingSession::whereDate('created_at', Carbon::today())
            ->latest()
            ->first();

        // Get yesterday's latest session scores
        $yesterday = WritingSession::whereDate('created_at', Carbon::yesterday())
            ->latest()
            ->first();

        if (!$today) return null;

        $keys = ['grammar_score', 'vocabulary_score', 'naturalness_score', 'clarity_score'];
        $deltas = [];

        foreach ($keys as $key) {
            $todayVal = $today->$key ?? 0;
            $yesterdayVal = $yesterday ? ($yesterday->$key ?? 0) : null;
            $deltas[$key] = [
                'today'     => $todayVal,
                'yesterday' => $yesterdayVal,
                'delta'     => $yesterdayVal !== null ? ($todayVal - $yesterdayVal) : null,
            ];
        }

        return [
            'scores'  => $deltas,
            'cefr'    => $today->cefr_estimate,
            'topic'   => $today->prompt_topic,
        ];
    }

    public function render()
    {
        $today = Carbon::today();
        $user  = Auth::user();

        // --- Writing Coach today? ---
        $todaySession = WritingSession::whereDate('created_at', $today)->latest()->first();
        $writtenToday = (bool) $todaySession;

        // Today's prompt (same logic as WritingCoach)
        $prompts = [
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

        // Weakness-aware prompt (mirrors WritingCoach logic)
        $topWeakness = Mistake::selectRaw('category, count(*) as total')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('category')
            ->orderByDesc('total')
            ->first();

        $basePrompt = $prompts[date('z') % count($prompts)];
        $todayPrompt = ($topWeakness && $topWeakness->total >= 3)
            ? $basePrompt . ' (Focus on using ' . $topWeakness->category . ' correctly.)'
            : $basePrompt;

        // --- Yesterday's Improvement ---
        $scoreDeltas = $this->getScoreDeltas();

        // --- Weakness of the Week ---
        $weekTopWeakness = Mistake::selectRaw('category, count(*) as total')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('category')
            ->orderByDesc('total')
            ->first();

        // --- Weekly Goals ---
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek   = Carbon::now()->endOfWeek();

        $goal = Goal::firstOrCreate([], [
            'target_vocabulary' => 70,
            'target_grammar'    => 5,
            'target_reading'    => 7,
            'target_writing'    => 7,
            'target_study_time' => 480,
        ]);

        $progress = [
            'vocabulary' => Vocabulary::whereNotNull('example_sentence')->whereBetween('updated_at', [$startOfWeek, $endOfWeek])->count(),
            'grammar'    => GrammarLesson::where('is_completed', true)->whereBetween('updated_at', [$startOfWeek, $endOfWeek])->count(),
            'reading'    => ReadingAttempt::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count(),
            'writing'    => JournalEntry::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count(),
            'study_time' => round(StudySession::whereBetween('created_at', [$startOfWeek, $endOfWeek])->sum('duration_seconds') / 60),
        ];

        return view('livewire.dashboard', [
            'user'            => $user,
            'streak'          => $this->calculateStreak(),
            'writtenToday'    => $writtenToday,
            'todayPrompt'     => $todayPrompt,
            'todaySession'    => $todaySession,
            'scoreDeltas'     => $scoreDeltas,
            'weekTopWeakness' => $weekTopWeakness,
            'goal'            => $goal,
            'progress'        => $progress,
        ]);
    }
}
