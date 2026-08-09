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
            "Describe what you did today. Focus on three key activities, how your day went, and one thing you accomplished.",
            "Explain a challenge you recently faced. What was the situation, how did you handle it, and what was the result?",
            "Describe your favorite app or digital tool. What problem does it solve for you, and why do you rely on it?",
            "Write about a country or city you would love to visit. What draws you there, and what would you do first?",
            "Describe a person who has strongly influenced your life or career. Who are they, how did they inspire you, and what key lesson did you learn from them?",
            "Write about a habit you want to build or break. Why is this important to you, and what specific steps are you taking?",
            "Describe your current or ideal morning routine. Walk through your steps from waking up to starting your day.",
            "Write about a skill or topic you learned recently. Why did you choose it, and how do you plan to use this knowledge?",
            "Describe your ideal work or study environment. What kind of setup, lighting, and atmosphere help you stay focused?",
            "Write about a mistake you made in the past. What happened, how did you handle it, and what lesson did you gain?",
            "Describe a goal you're working toward. Why does it matter, and how will you achieve it?",
            "Write about three things you are grateful for today and explain why each one added value to your day.",
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
