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
use App\Models\ActivityLog;
use App\Services\RecommendationEngineService;
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

    public function render()
    {
        $user = Auth::user();
        $userId = $user->id ?? 1;

        // 1. Data-Driven Daily Plan from Recommendation Engine
        $dailyPlan = RecommendationEngineService::getTodayPlan($userId);
        $planItems = $dailyPlan->items;
        $totalItems = count($planItems);
        $completedItems = $planItems->where('status', 'completed')->count();
        $progressPercent = $totalItems > 0 ? round(($completedItems / $totalItems) * 100) : 0;
        $estimatedMinutesTotal = $planItems->sum('estimated_minutes');

        // 2. Top 3 Weaknesses with direct practice links
        $topWeaknesses = RecommendationEngineService::getTopWeaknesses();

        // 3. Weekly Progress Summary
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek   = Carbon::now()->endOfWeek();

        $weeklyStats = [
            'study_minutes' => round(ActivityLog::where('user_id', $userId)->whereBetween('created_at', [$startOfWeek, $endOfWeek])->sum('duration_seconds') / 60),
            'words_reviewed' => Vocabulary::whereNotNull('example_sentence')->whereBetween('updated_at', [$startOfWeek, $endOfWeek])->count(),
            'writing_submissions' => WritingSession::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count(),
            'speaking_sessions' => ActivityLog::where('user_id', $userId)->where('activity_type', 'conversation')->whereBetween('created_at', [$startOfWeek, $endOfWeek])->count(),
        ];

        // 4. English Profile (Estimated skill breakdown)
        $skillProfile = [
            'Grammar'    => 'B1',
            'Vocabulary' => 'B1+',
            'Reading'    => 'B2',
            'Writing'    => 'B1',
            'Speaking'   => 'A2+',
            'Listening'  => 'B1',
        ];

        return view('livewire.dashboard', [
            'user'                  => $user,
            'streak'                => $this->calculateStreak(),
            'dailyPlan'             => $dailyPlan,
            'planItems'             => $planItems,
            'completedItems'        => $completedItems,
            'totalItems'            => $totalItems,
            'progressPercent'       => $progressPercent,
            'estimatedMinutesTotal' => $estimatedMinutesTotal,
            'topWeaknesses'         => $topWeaknesses,
            'weeklyStats'           => $weeklyStats,
            'skillProfile'          => $skillProfile,
        ]);
    }
}
