<?php

namespace App\Livewire\Timer;

use Livewire\Component;
use App\Models\StudySession;
use Livewire\Attributes\Layout;
use App\Services\RecommendationEngineService;
use App\Services\AchievementService;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class Index extends Component
{
    public string $activityType = 'General';
    public string $notes = '';
    public int $customMinutes = 25;

    public const ACTIVITIES = [
        ['id' => 'General',     'label' => 'General Study',     'icon' => '📖', 'route' => null],
        ['id' => 'Vocabulary',  'label' => 'Vocabulary Review', 'icon' => '📚', 'route' => 'vocabulary'],
        ['id' => 'Grammar',     'label' => 'Grammar Practice',  'icon' => '📘', 'route' => 'grammar'],
        ['id' => 'Reading',     'label' => 'Reading & Quiz',    'icon' => '📖', 'route' => 'ai-reading'],
        ['id' => 'Writing',     'label' => 'Writing Coach',     'icon' => '✍️',  'route' => 'writing-coach'],
        ['id' => 'Speaking',    'label' => 'Speaking Practice', 'icon' => '💬', 'route' => 'conversation'],
    ];

    public function getRecentSessionsProperty()
    {
        return StudySession::latest()->take(10)->get();
    }

    public function saveSession(int $durationSeconds): void
    {
        if ($durationSeconds >= 15) {
            $minutes = max(1, (int) round($durationSeconds / 60));

            StudySession::create([
                'duration_seconds' => $durationSeconds,
                'activity_type'    => $this->activityType,
                'notes'            => $this->notes ? trim($this->notes) : null,
            ]);

            // Sync with ActivityLog and Daily Plan
            $userId = Auth::id() ?? 1;
            $activityMap = [
                'Vocabulary' => 'vocabulary',
                'Grammar'    => 'weakness_practice',
                'Reading'    => 'reading',
                'Writing'    => 'writing',
                'Speaking'   => 'conversation',
            ];
            $logType = $activityMap[$this->activityType] ?? 'general';

            RecommendationEngineService::logAndComplete($userId, $logType, null, $durationSeconds);

            // Check achievements
            AchievementService::check('time', $this);
            AchievementService::check('streak', $this);

            session()->flash('message', "🎉 Session saved! +{$minutes} min of {$this->activityType} added to your study stats.");
        } else {
            session()->flash('error', 'Session was under 15 seconds and was not logged.');
        }

        $this->reset('notes');
    }

    public function render()
    {
        return view('livewire.timer.index', [
            'recentSessions' => $this->recentSessions,
            'activities'     => self::ACTIVITIES,
        ]);
    }
}
