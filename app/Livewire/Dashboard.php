<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Vocabulary;
use App\Models\GrammarLesson;
use App\Models\ReadingAttempt;
use App\Models\JournalEntry;
use App\Models\StudySession;
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

        if (empty($dates)) {
            return 0;
        }

        $streak = 0;
        
        $mostRecent = Carbon::parse($dates[0]);
        if (!$mostRecent->isToday() && !$mostRecent->isYesterday()) {
            return 0;
        }

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

    public function render()
    {
        $today = Carbon::today();
        
        // Mission Checklist
        $mission = [
            'vocab' => Vocabulary::whereDate('created_at', $today)->exists(),
            'grammar' => GrammarLesson::where('is_completed', true)->whereDate('updated_at', $today)->exists(),
            'reading' => ReadingAttempt::whereDate('created_at', $today)->exists(),
            'journal' => JournalEntry::whereDate('created_at', $today)->exists(),
            'review' => Vocabulary::where('is_mastered', true)->whereDate('updated_at', $today)->exists(),
        ];
        
        $missionCompletedCount = count(array_filter($mission));
        $missionTotal = count($mission);
        $missionProgress = ($missionCompletedCount / $missionTotal) * 100;

        // Weekly Goals Progress
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        
        $goal = Goal::firstOrCreate([], [
            'target_vocabulary' => 70,
            'target_grammar' => 5,
            'target_reading' => 7,
            'target_writing' => 7,
            'target_study_time' => 480,
        ]);

        $progress = [
            'vocabulary' => Vocabulary::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count(),
            'grammar' => GrammarLesson::where('is_completed', true)->whereBetween('updated_at', [$startOfWeek, $endOfWeek])->count(),
            'reading' => ReadingAttempt::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count(),
            'writing' => JournalEntry::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count(),
            'study_time' => round(StudySession::whereBetween('created_at', [$startOfWeek, $endOfWeek])->sum('duration_seconds') / 60),
        ];

        return view('livewire.dashboard', [
            'user' => Auth::user(),
            'mission' => $mission,
            'missionProgress' => $missionProgress,
            'goal' => $goal,
            'progress' => $progress,
            'streak' => $this->calculateStreak(),
        ]);
    }
}
