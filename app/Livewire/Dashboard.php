<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\StudySession;
use App\Models\WritingSession;
use App\Models\ReadingSession;
use App\Models\ActivityLog;
use App\Models\DailyReflection;
use App\Services\RecommendationEngineService;
use App\Services\SkillProfileService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    private function calculateStreak(): int
    {
        $dates = StudySession::selectRaw('DATE(created_at) as date')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->pluck('date')
            ->toArray();

        if (empty($dates)) return 0;

        $mostRecent = Carbon::parse($dates[0]);
        if (!$mostRecent->isToday() && !$mostRecent->isYesterday()) return 0;

        $streak    = 0;
        $checkDate = $mostRecent->copy();

        foreach ($dates as $date) {
            if (Carbon::parse($date)->isSameDay($checkDate)) {
                $streak++;
                $checkDate->subDay();
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * Calculate week-over-week improvement deltas for skill scores.
     * Returns null for a skill if there is insufficient data in either week.
     */
    private function getWeeklyDeltas(array $currentProfile, int $userId): array
    {
        $lastWeekStart = Carbon::now()->subDays(14)->startOfDay();
        $lastWeekEnd   = Carbon::now()->subDays(7)->startOfDay();

        $deltas = [];

        // Grammar delta
        $lastWeekGrammar = WritingSession::where('user_id', $userId)
            ->whereNotNull('grammar_score')
            ->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])
            ->pluck('grammar_score');

        if ($lastWeekGrammar->isNotEmpty() && $currentProfile['Grammar']['score'] !== null) {
            $deltas['Grammar'] = $currentProfile['Grammar']['score'] - round($lastWeekGrammar->average());
        } else {
            $deltas['Grammar'] = null;
        }

        // Writing delta (clarity + naturalness)
        $lastWeekWriting = WritingSession::where('user_id', $userId)
            ->whereNotNull('clarity_score')
            ->whereNotNull('naturalness_score')
            ->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])
            ->get(['clarity_score', 'naturalness_score']);

        if ($lastWeekWriting->isNotEmpty() && $currentProfile['Writing']['score'] !== null) {
            $lastAvg = round($lastWeekWriting->avg(fn($s) => ($s->clarity_score + $s->naturalness_score) / 2));
            $deltas['Writing'] = $currentProfile['Writing']['score'] - $lastAvg;
        } else {
            $deltas['Writing'] = null;
        }

        // Reading delta
        $lastWeekReading = ReadingSession::where('user_id', $userId)
            ->whereNotNull('quiz_score')
            ->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])
            ->pluck('quiz_score');

        if ($lastWeekReading->isNotEmpty() && $currentProfile['Reading']['score'] !== null) {
            $lastAvgPct      = round(($lastWeekReading->average() / 5) * 100);
            $deltas['Reading'] = $currentProfile['Reading']['score'] - $lastAvgPct;
        } else {
            $deltas['Reading'] = null;
        }

        // Speaking delta — composite, we can't meaningfully delta it week-over-week
        // (session count only grows), so we omit it from the delta display
        $deltas['Speaking'] = null;

        return $deltas;
    }

    public function render()
    {
        $user   = Auth::user();
        $userId = $user->id ?? 1;

        // ─── 1. Data-Driven Daily Plan ────────────────────────────────────────
        $dailyPlan              = RecommendationEngineService::getTodayPlan($userId);
        $planItems              = $dailyPlan->items;
        $totalItems             = count($planItems);
        $completedItems         = $planItems->where('status', 'completed')->count();
        $progressPercent        = $totalItems > 0 ? round(($completedItems / $totalItems) * 100) : 0;
        $estimatedMinutesTotal  = $planItems->sum('estimated_minutes');

        // ─── 2. Top 3 Weaknesses ──────────────────────────────────────────────
        $topWeaknesses = RecommendationEngineService::getTopWeaknesses();

        // ─── 3. Calculated Skill Profile ─────────────────────────────────────
        $skillProfile  = SkillProfileService::getProfile($userId);
        $weeklyDeltas  = $this->getWeeklyDeltas($skillProfile, $userId);

        // ─── 4. Weekly Activity Summary ───────────────────────────────────────
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek   = Carbon::now()->endOfWeek();

        $weeklyStats = [
            'study_minutes'       => round(ActivityLog::where('user_id', $userId)->whereBetween('created_at', [$startOfWeek, $endOfWeek])->sum('duration_seconds') / 60),
            'words_reviewed'      => \App\Models\Vocabulary::whereNotNull('last_reviewed_at')->whereBetween('last_reviewed_at', [$startOfWeek, $endOfWeek])->count(),
            'writing_submissions' => WritingSession::where('user_id', $userId)->whereBetween('created_at', [$startOfWeek, $endOfWeek])->count(),
            'speaking_sessions'   => \App\Models\ConversationSession::where('user_id', $userId)->whereBetween('created_at', [$startOfWeek, $endOfWeek])->count(),
        ];

        // ─── 5. Daily Reflection ──────────────────────────────────────────────
        $todayReflection = DailyReflection::where('user_id', $userId)
            ->whereDate('date', Carbon::today()->toDateString())
            ->first();

        $showReflectionCard = (int) date('H') >= 18; // Show after 6 PM

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
            'skillProfile'          => $skillProfile,
            'weeklyDeltas'          => $weeklyDeltas,
            'weeklyStats'           => $weeklyStats,
            'todayReflection'       => $todayReflection,
            'showReflectionCard'    => $showReflectionCard,
        ]);
    }
}
