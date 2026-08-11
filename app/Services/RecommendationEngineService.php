<?php

namespace App\Services;

use App\Models\DailyPlan;
use App\Models\DailyPlanItem;
use App\Models\ActivityLog;
use App\Models\Vocabulary;
use App\Models\Mistake;
use App\Models\WritingSession;
use App\Models\ReadingSession;
use App\Models\ConversationSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RecommendationEngineService
{
    /**
     * Get or generate today's data-driven Daily Plan for the user.
     */
    public static function getTodayPlan(int $userId): DailyPlan
    {
        $today = Carbon::today()->toDateString();

        // Use whereDate() for cross-database date comparison compatibility
        $plan = DailyPlan::where('user_id', $userId)
            ->whereDate('date', $today)
            ->with('items')
            ->first();

        if ($plan) {
            return $plan;
        }

        // Create new daily plan for today — pass toDateString() to avoid datetime serialization
        $plan = DailyPlan::create([
            'user_id'        => $userId,
            'date'           => $today,
            'target_minutes' => 45,
            'completed'      => false,
        ]);

        self::generatePlanItems($plan);

        return $plan->fresh(['items']);
    }

    /**
     * Generate priority-scored activities with specific, data-driven reason text.
     */
    public static function generatePlanItems(DailyPlan $plan): void
    {
        $userId     = $plan->user_id;
        $candidates = [];

        // ─── 1. Vocabulary SRS ────────────────────────────────────────────────
        $dueVocabCount  = Vocabulary::where('next_review_date', '<=', Carbon::today()->toDateString())->count();
        $neverReviewed  = Vocabulary::whereNull('next_review_date')->whereNotNull('example_sentence')->count();
        $totalVocab     = Vocabulary::count();

        $vocabPriority  = 30 + ($dueVocabCount * 2);
        if ($dueVocabCount > 0) {
            $vocabDesc   = "{$dueVocabCount} " . ($dueVocabCount === 1 ? 'word' : 'words') . " due for SRS review today";
            $vocabReason = self::buildReason([
                "{$dueVocabCount} " . ($dueVocabCount === 1 ? 'vocabulary word is' : 'vocabulary words are') . " due for spaced-repetition review today",
                $neverReviewed > 0 ? "{$neverReviewed} " . ($neverReviewed === 1 ? 'word has' : 'words have') . " never been reviewed yet" : null,
                "Consistent review now will move words to higher Leitner boxes and reduce future review load",
            ]);
        } else {
            $vocabDesc   = "Learn & review your {$totalVocab} vocabulary words";
            $vocabReason = self::buildReason([
                "No words are overdue — but regular review prevents forgetting",
                "You have {$totalVocab} words in your vocabulary list",
                "Even a short 10-minute session reinforces long-term memory",
            ]);
        }

        $candidates[] = [
            'activity_type'     => 'vocabulary',
            'activity_id'       => null,
            'title'             => '📚 Vocabulary Review',
            'description'       => $vocabDesc,
            'reason'            => $vocabReason,
            'route_name'        => 'vocabulary',
            'route_params'      => null,
            'priority'          => $vocabPriority,
            'estimated_minutes' => 10,
        ];

        // ─── 2. Top Weakness Practice ─────────────────────────────────────────
        // Normalize category case when querying (GROUP BY LOWER)
        $topWeakness = Mistake::selectRaw('LOWER(category) as category_normalized, count(*) as count')
            ->groupBy('category_normalized')
            ->orderBy('count', 'desc')
            ->first();

        if ($topWeakness) {
            $cat      = ucfirst($topWeakness->category_normalized);
            $errCount = $topWeakness->count;

            // Find how many days since the last weakness practice session
            $lastPractice = ActivityLog::where('user_id', $userId)
                ->where('activity_type', 'weakness_practice')
                ->latest()
                ->first();
            $daysSincePractice = $lastPractice
                ? $lastPractice->created_at->diffInDays(now())
                : null;

            $reasonLines = [
                "{$cat} is your most frequent error category with {$errCount} logged " . ($errCount === 1 ? 'mistake' : 'mistakes'),
                $daysSincePractice === null
                    ? "You have not practiced {$cat} yet — this is your highest-impact activity right now"
                    : ($daysSincePractice >= 2 ? "You last practiced weaknesses {$daysSincePractice} days ago" : null),
                "Fixing your top weakness has the highest return on your learning time",
            ];

            $candidates[] = [
                'activity_type'     => 'weakness_practice',
                'activity_id'       => $cat,
                'title'             => '⚠️ Weakness Practice',
                'description'       => "Focus: {$cat} ({$errCount} " . ($errCount === 1 ? 'error' : 'errors') . " logged)",
                'reason'            => self::buildReason($reasonLines),
                'route_name'        => 'mistakes.practice',
                'route_params'      => ['category' => $cat],
                'priority'          => 40 + ($errCount * 2),
                'estimated_minutes' => 5,
            ];
        } else {
            $candidates[] = [
                'activity_type'     => 'weakness_practice',
                'activity_id'       => 'Grammar',
                'title'             => '⚠️ Weakness Practice',
                'description'       => 'Targeted 5-question fill-in-the-blank practice',
                'reason'            => self::buildReason([
                    "No mistakes have been logged yet",
                    "Starting with Grammar practice builds a strong foundation",
                    "As you use other features, your real weaknesses will surface here",
                ]),
                'route_name'        => 'mistakes.practice',
                'route_params'      => null,
                'priority'          => 35,
                'estimated_minutes' => 5,
            ];
        }

        // ─── 3. Speaking / Conversation ───────────────────────────────────────
        $lastSpeaking = ActivityLog::where('user_id', $userId)
            ->where('activity_type', 'conversation')
            ->latest()
            ->first();

        // Also check ConversationSession for users who used it before logging was added
        $lastConvSession = ConversationSession::where('user_id', $userId)
            ->latest()
            ->first();

        $lastSpeakingDate = null;
        if ($lastSpeaking) {
            $lastSpeakingDate = $lastSpeaking->created_at;
        } elseif ($lastConvSession) {
            $lastSpeakingDate = $lastConvSession->created_at;
        }

        $daysSinceSpeaking = $lastSpeakingDate
            ? (int) $lastSpeakingDate->diffInDays(now())
            : null;

        $speakingPriority  = ($daysSinceSpeaking === null || $daysSinceSpeaking >= 2) ? 45 : 20;
        $speakingReasonLines = [
            $daysSinceSpeaking === null
                ? "You have no recorded speaking sessions this week"
                : ($daysSinceSpeaking >= 2
                    ? "You last practiced speaking {$daysSinceSpeaking} days ago — consistency matters for fluency"
                    : "You practiced speaking recently — good habit"),
            "Regular speaking practice builds fluency and reduces hesitation",
            "Speaking is the hardest skill to develop without active practice",
        ];

        $candidates[] = [
            'activity_type'     => 'conversation',
            'activity_id'       => null,
            'title'             => '💬 Speaking Practice',
            'description'       => $daysSinceSpeaking === null
                ? 'Start a voice conversation with your AI partner'
                : ($daysSinceSpeaking >= 2
                    ? "Last practiced {$daysSinceSpeaking} days ago — time to speak again"
                    : 'Continue building your speaking consistency'),
            'reason'            => self::buildReason($speakingReasonLines),
            'route_name'        => 'conversation',
            'route_params'      => null,
            'priority'          => $speakingPriority,
            'estimated_minutes' => 10,
        ];

        // ─── 4. Writing Coach ─────────────────────────────────────────────────
        $lastWriting = WritingSession::where('user_id', $userId)
            ->latest()
            ->first();

        $lastWritingDate   = $lastWriting?->created_at;
        $daysSinceWriting  = $lastWritingDate ? (int) $lastWritingDate->diffInDays(now()) : null;

        $writingPriority = ($daysSinceWriting === null || $daysSinceWriting >= 3) ? 40 : 15;

        if ($lastWriting) {
            $lastScore = $lastWriting->grammar_score;
            $writingDesc = $daysSinceWriting >= 3
                ? "Last submission {$daysSinceWriting} days ago — write again to maintain progress"
                : "Last grammar score: {$lastScore}/100 — write again to track improvement";
        } else {
            $writingDesc = "Submit your first writing for AI grammar & clarity analysis";
        }

        $writingReasonLines = [
            $daysSinceWriting === null
                ? "You have not submitted any writing yet — this is the best way to measure your real grammar level"
                : ($daysSinceWriting >= 3
                    ? "You last wrote {$daysSinceWriting} days ago — writing regularly builds precision"
                    : "You wrote recently — good consistency"),
            "Writing gives the AI Coach real data to calculate your Grammar and Writing skill scores",
            "Each submission adds to your skill profile and helps identify specific grammar errors",
        ];

        $candidates[] = [
            'activity_type'     => 'writing',
            'activity_id'       => null,
            'title'             => '✍️ Writing Coach',
            'description'       => $writingDesc,
            'reason'            => self::buildReason($writingReasonLines),
            'route_name'        => 'writing-coach',
            'route_params'      => null,
            'priority'          => $writingPriority,
            'estimated_minutes' => 15,
        ];

        // ─── 5. AI Reading & Quiz ─────────────────────────────────────────────
        $lastReading = ReadingSession::where('user_id', $userId)
            ->latest()
            ->first();

        $lastReadingDate  = $lastReading?->created_at;
        $daysSinceReading = $lastReadingDate ? (int) $lastReadingDate->diffInDays(now()) : null;
        $readingPriority  = ($daysSinceReading === null || $daysSinceReading >= 2) ? 35 : 10;

        $readingReasonLines = [
            $daysSinceReading === null
                ? "You have not done any AI Reading sessions yet"
                : ($daysSinceReading >= 2
                    ? "You last read {$daysSinceReading} days ago"
                    : "You read recently — keep up the habit"),
            "Reading builds vocabulary in context and improves comprehension speed",
            "Quiz scores from reading sessions are used to calculate your Reading skill score",
        ];

        $candidates[] = [
            'activity_type'     => 'reading',
            'activity_id'       => null,
            'title'             => '📖 AI Reading & Quiz',
            'description'       => $daysSinceReading === null
                ? 'Read a CEFR-matched article and test comprehension'
                : ($daysSinceReading >= 2
                    ? "Last read {$daysSinceReading} days ago — read again to maintain habit"
                    : 'Continue building your reading consistency'),
            'reason'            => self::buildReason($readingReasonLines),
            'route_name'        => 'ai-reading',
            'route_params'      => null,
            'priority'          => $readingPriority,
            'estimated_minutes' => 10,
        ];

        // ─── Sort by priority descending ──────────────────────────────────────
        usort($candidates, fn($a, $b) => $b['priority'] <=> $a['priority']);

        // ─── Insert into daily_plan_items ─────────────────────────────────────
        foreach ($candidates as $item) {
            DailyPlanItem::create([
                'daily_plan_id'     => $plan->id,
                'activity_type'     => $item['activity_type'],
                'activity_id'       => $item['activity_id'],
                'title'             => $item['title'],
                'description'       => $item['description'],
                'reason'            => $item['reason'],
                'route_name'        => $item['route_name'],
                'route_params'      => $item['route_params'],
                'priority'          => $item['priority'],
                'estimated_minutes' => $item['estimated_minutes'],
                'status'            => 'pending',
            ]);
        }
    }

    /**
     * Mark an activity type as completed in today's plan & log activity.
     */
    public static function logAndComplete(int $userId, string $activityType, ?string $activityId = null, int $durationSeconds = 300, ?float $score = null): void
    {
        // 1. Record Activity Log
        ActivityLog::create([
            'user_id'          => $userId,
            'activity_type'    => $activityType,
            'activity_id'      => $activityId,
            'started_at'       => Carbon::now()->subSeconds($durationSeconds),
            'completed_at'     => Carbon::now(),
            'duration_seconds' => $durationSeconds,
            'score'            => $score,
        ]);

        // 2. Mark the matching pending item in today's plan as completed
        $today = Carbon::today()->toDateString();
        $plan  = DailyPlan::where('user_id', $userId)->whereDate('date', $today)->first();

        if ($plan) {
            $item = DailyPlanItem::where('daily_plan_id', $plan->id)
                ->where('activity_type', $activityType)
                ->where('status', 'pending')
                ->first();

            if ($item) {
                $item->update([
                    'status'       => 'completed',
                    'completed_at' => Carbon::now(),
                ]);
            }

            // Check if all items are now completed
            $pendingCount = DailyPlanItem::where('daily_plan_id', $plan->id)
                ->where('status', 'pending')
                ->count();

            if ($pendingCount === 0) {
                $plan->update([
                    'completed'    => true,
                    'completed_at' => Carbon::now(),
                ]);
            }
        }
    }

    /**
     * Get top 3 mistake categories with counts and severity.
     * Normalizes case (Grammar vs grammar) before grouping.
     */
    public static function getTopWeaknesses(): array
    {
        return Mistake::selectRaw('LOWER(category) as category_key, count(*) as error_count')
            ->groupBy('category_key')
            ->orderBy('error_count', 'desc')
            ->take(3)
            ->get()
            ->map(function ($item) {
                $cat     = ucfirst($item->category_key);
                $total   = Mistake::whereRaw('LOWER(category) = ?', [$item->category_key])->count();
                $reviewed = Mistake::whereRaw('LOWER(category) = ?', [$item->category_key])
                    ->where('times_reviewed', '>', 0)
                    ->count();
                $accuracy = $total > 0 ? round(($reviewed / $total) * 100) : 0;

                return [
                    'category' => $cat,
                    'count'    => $item->error_count,
                    'accuracy' => $accuracy,
                    'severity' => $item->error_count >= 15 ? 'red' : ($item->error_count >= 8 ? 'amber' : 'emerald'),
                ];
            })
            ->toArray();
    }

    /**
     * Build a human-readable reason string from an array of bullet point lines.
     * Null lines are filtered out (used for conditional reasons).
     */
    private static function buildReason(array $lines): string
    {
        $filtered = array_filter($lines, fn($l) => $l !== null && $l !== '');
        return implode(' | ', array_values($filtered));
    }
}
