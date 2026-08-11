<?php

namespace App\Services;

use App\Models\DailyPlan;
use App\Models\DailyPlanItem;
use App\Models\ActivityLog;
use App\Models\Vocabulary;
use App\Models\Mistake;
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

        $plan = DailyPlan::where('user_id', $userId)
            ->where('date', $today)
            ->with('items')
            ->first();

        if ($plan) {
            return $plan;
        }

        // Create new daily plan for today
        $plan = DailyPlan::create([
            'user_id' => $userId,
            'date' => $today,
            'target_minutes' => 45,
            'completed' => false,
        ]);

        self::generatePlanItems($plan);

        return $plan->fresh(['items']);
    }

    /**
     * Generate priority-scored activities based on current system state.
     */
    public static function generatePlanItems(DailyPlan $plan): void
    {
        $userId = $plan->user_id;
        $candidates = [];

        // 1. Vocabulary SRS check
        $dueVocabCount = Vocabulary::where('next_review_date', '<=', Carbon::today()->toDateString())->count();
        $totalVocabCount = Vocabulary::count();
        
        $candidates[] = [
            'activity_type' => 'vocabulary',
            'activity_id' => null,
            'title' => '📚 Vocabulary Review',
            'description' => $dueVocabCount > 0 ? "{$dueVocabCount} words due for SRS review" : "Learn & review daily vocabulary words",
            'route_name' => 'vocabulary',
            'route_params' => null,
            'priority' => 30 + ($dueVocabCount * 2),
            'estimated_minutes' => 10,
        ];

        // 2. Top Weakness Practice check
        $topWeakness = Mistake::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->first();

        if ($topWeakness) {
            $candidates[] = [
                'activity_type' => 'weakness_practice',
                'activity_id' => $topWeakness->category,
                'title' => '⚠️ Weakness Practice',
                'description' => "Focus: {$topWeakness->category} ({$topWeakness->count} errors logged)",
                'route_name' => 'mistakes.practice',
                'route_params' => ['category' => $topWeakness->category],
                'priority' => 40 + ($topWeakness->count * 2),
                'estimated_minutes' => 5,
            ];
        } else {
            $candidates[] = [
                'activity_type' => 'weakness_practice',
                'activity_id' => 'Grammar',
                'title' => '⚠️ Weakness Practice',
                'description' => 'Targeted 5-question fill-in-the-blank practice',
                'route_name' => 'mistakes.practice',
                'route_params' => null,
                'priority' => 35,
                'estimated_minutes' => 5,
            ];
        }

        // 3. Speaking / Conversation check
        $recentSpeaking = ActivityLog::where('user_id', $userId)
            ->where('activity_type', 'conversation')
            ->where('created_at', '>=', Carbon::now()->subDays(2))
            ->exists();

        $candidates[] = [
            'activity_type' => 'conversation',
            'activity_id' => null,
            'title' => '💬 Speaking Practice',
            'description' => 'Interactive voice conversation (Scenario: Job Interview / Casual Chat)',
            'route_name' => 'conversation',
            'route_params' => null,
            'priority' => $recentSpeaking ? 20 : 45,
            'estimated_minutes' => 10,
        ];

        // 4. Writing Coach check
        $recentWriting = ActivityLog::where('user_id', $userId)
            ->where('activity_type', 'writing')
            ->where('created_at', '>=', Carbon::now()->subDays(3))
            ->exists();

        $candidates[] = [
            'activity_type' => 'writing',
            'activity_id' => null,
            'title' => '✍️ Writing Coach',
            'description' => 'Write a short response for instant AI grammar & clarity analysis',
            'route_name' => 'writing-coach',
            'route_params' => null,
            'priority' => $recentWriting ? 15 : 40,
            'estimated_minutes' => 15,
        ];

        // 5. AI Reading check
        $recentReading = ActivityLog::where('user_id', $userId)
            ->where('activity_type', 'reading')
            ->where('created_at', '>=', Carbon::now()->subDays(2))
            ->exists();

        $candidates[] = [
            'activity_type' => 'reading',
            'activity_id' => null,
            'title' => '📖 AI Reading & Quiz',
            'description' => 'Read a CEFR B1 article & test comprehension',
            'route_name' => 'ai-reading',
            'route_params' => null,
            'priority' => $recentReading ? 10 : 35,
            'estimated_minutes' => 10,
        ];

        // Sort candidates by priority descending
        usort($candidates, fn($a, $b) => $b['priority'] <=> $a['priority']);

        // Insert items into daily_plan_items
        foreach ($candidates as $item) {
            DailyPlanItem::create([
                'daily_plan_id' => $plan->id,
                'activity_type' => $item['activity_type'],
                'activity_id' => $item['activity_id'],
                'title' => $item['title'],
                'description' => $item['description'],
                'route_name' => $item['route_name'],
                'route_params' => $item['route_params'],
                'priority' => $item['priority'],
                'estimated_minutes' => $item['estimated_minutes'],
                'status' => 'pending',
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
            'user_id' => $userId,
            'activity_type' => $activityType,
            'activity_id' => $activityId,
            'started_at' => Carbon::now()->subSeconds($durationSeconds),
            'completed_at' => Carbon::now(),
            'duration_seconds' => $durationSeconds,
            'score' => $score,
        ]);

        // 2. Mark item in today's plan as completed
        $today = Carbon::today()->toDateString();
        $plan = DailyPlan::where('user_id', $userId)->where('date', $today)->first();

        if ($plan) {
            $item = DailyPlanItem::where('daily_plan_id', $plan->id)
                ->where('activity_type', $activityType)
                ->where('status', 'pending')
                ->first();

            if ($item) {
                $item->update([
                    'status' => 'completed',
                    'completed_at' => Carbon::now(),
                ]);
            }

            // Check if all items in plan are completed
            $pendingCount = DailyPlanItem::where('daily_plan_id', $plan->id)->where('status', 'pending')->count();
            if ($pendingCount === 0) {
                $plan->update([
                    'completed' => true,
                    'completed_at' => Carbon::now(),
                ]);
            }
        }
    }

    /**
     * Get top 3 mistake categories for priority card.
     */
    public static function getTopWeaknesses(): array
    {
        return Mistake::select('category', DB::raw('count(*) as error_count'))
            ->groupBy('category')
            ->orderBy('error_count', 'desc')
            ->take(3)
            ->get()
            ->map(function ($item) {
                $total = Mistake::where('category', $item->category)->count();
                $reviewed = Mistake::where('category', $item->category)->where('times_reviewed', '>', 0)->count();
                $accuracy = $total > 0 ? round(($reviewed / $total) * 100) : 50;

                return [
                    'category' => $item->category,
                    'count' => $item->error_count,
                    'accuracy' => $accuracy,
                    'severity' => $item->error_count >= 15 ? 'red' : ($item->error_count >= 8 ? 'amber' : 'emerald'),
                ];
            })->toArray();
    }
}
