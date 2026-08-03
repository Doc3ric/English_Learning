<?php

namespace App\Livewire\Achievements;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Services\AchievementService;
use App\Models\AchievementUnlock;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class Index extends Component
{
    public function render()
    {
        $allAchievements = collect(AchievementService::all())->map(function($item, $key) {
            $item['key'] = $key;
            return $item;
        });
        
        $unlockedKeys = AchievementUnlock::where('user_id', Auth::id())->pluck('achievement_key')->toArray();

        return view('livewire.achievements.index', [
            'achievements' => $allAchievements->groupBy('category'),
            'unlockedKeys' => $unlockedKeys,
            'totalUnlocked' => count($unlockedKeys),
            'totalAchievements' => $allAchievements->count()
        ]);
    }
}
