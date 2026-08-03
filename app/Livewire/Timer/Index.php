<?php

namespace App\Livewire\Timer;

use Livewire\Component;
use App\Models\StudySession;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    public $activityType = 'General';
    public $notes = '';

    public function getRecentSessionsProperty()
    {
        return StudySession::latest()->take(10)->get();
    }

    public function saveSession($durationSeconds)
    {
        if ($durationSeconds >= 15) {
            StudySession::create([
                'duration_seconds' => (int) $durationSeconds,
                'activity_type' => $this->activityType,
                'notes' => $this->notes ?: null,
            ]);
            
            \App\Services\AchievementService::check('time', $this);
            \App\Services\AchievementService::check('streak', $this);
        }
        
        $this->reset('notes');
        
        session()->flash('message', 'Study session saved successfully! Great job!');
    }

    public function render()
    {
        return view('livewire.timer.index', [
            'recentSessions' => $this->recentSessions,
        ]);
    }
}
