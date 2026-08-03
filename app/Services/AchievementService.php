<?php

namespace App\Services;

use App\Models\AchievementUnlock;
use App\Models\Vocabulary;
use App\Models\GrammarLesson;
use App\Models\ReadingAttempt;
use App\Models\JournalEntry;
use App\Models\Mistake;
use App\Models\StudySession;
use Illuminate\Support\Facades\Auth;

class AchievementService
{
    public static function all()
    {
        return [
            // Vocabulary
            'vocab_1' => ['title' => 'First Word', 'desc' => 'Learn your first vocabulary word.', 'icon' => 'sparkles', 'category' => 'vocabulary', 'target' => 1],
            'vocab_50' => ['title' => 'Word Collector', 'desc' => 'Learn 50 vocabulary words.', 'icon' => 'sparkles', 'category' => 'vocabulary', 'target' => 50],
            'vocab_100' => ['title' => 'Lexicon Builder', 'desc' => 'Learn 100 vocabulary words.', 'icon' => 'sparkles', 'category' => 'vocabulary', 'target' => 100],
            'vocab_500' => ['title' => 'Walking Dictionary', 'desc' => 'Learn 500 vocabulary words.', 'icon' => 'sparkles', 'category' => 'vocabulary', 'target' => 500],
            
            // Grammar
            'grammar_1' => ['title' => 'Syntax Starter', 'desc' => 'Complete your first grammar lesson.', 'icon' => 'academic-cap', 'category' => 'grammar', 'target' => 1],
            'grammar_5' => ['title' => 'Rule Follower', 'desc' => 'Complete 5 grammar lessons.', 'icon' => 'academic-cap', 'category' => 'grammar', 'target' => 5],
            'grammar_10' => ['title' => 'Grammar Guru', 'desc' => 'Complete 10 grammar lessons.', 'icon' => 'academic-cap', 'category' => 'grammar', 'target' => 10],
            
            // Reading
            'reading_1' => ['title' => 'First Page', 'desc' => 'Read your first article.', 'icon' => 'book-open', 'category' => 'reading', 'target' => 1],
            'reading_10' => ['title' => 'Bookworm', 'desc' => 'Read 10 articles.', 'icon' => 'book-open', 'category' => 'reading', 'target' => 10],
            'reading_50' => ['title' => 'Speed Reader', 'desc' => 'Read 50 articles.', 'icon' => 'book-open', 'category' => 'reading', 'target' => 50],
            
            // Journal
            'journal_1' => ['title' => 'Dear Diary', 'desc' => 'Write your first journal entry.', 'icon' => 'pencil-alt', 'category' => 'journal', 'target' => 1],
            'journal_7' => ['title' => 'Consistent Writer', 'desc' => 'Write 7 journal entries.', 'icon' => 'pencil-alt', 'category' => 'journal', 'target' => 7],
            'journal_30' => ['title' => 'Scribe', 'desc' => 'Write 30 journal entries.', 'icon' => 'pencil-alt', 'category' => 'journal', 'target' => 30],
            
            // Mistakes
            'mistakes_1' => ['title' => 'Learning from Errors', 'desc' => 'Log your first mistake.', 'icon' => 'exclamation-circle', 'category' => 'mistakes', 'target' => 1],
            'mistakes_20' => ['title' => 'Self-Corrector', 'desc' => 'Log 20 mistakes.', 'icon' => 'exclamation-circle', 'category' => 'mistakes', 'target' => 20],
            
            // Study Time
            'time_10' => ['title' => 'Dedication', 'desc' => 'Study for 10 total hours.', 'icon' => 'clock', 'category' => 'time', 'target' => 10 * 60],
            'time_50' => ['title' => 'Mastery', 'desc' => 'Study for 50 total hours.', 'icon' => 'clock', 'category' => 'time', 'target' => 50 * 60],
            
            // Streaks
            'streak_7' => ['title' => 'One Week Strong', 'desc' => 'Maintain a 7-day streak.', 'icon' => 'fire', 'category' => 'streak', 'target' => 7],
            'streak_30' => ['title' => 'Monthly Habit', 'desc' => 'Maintain a 30-day streak.', 'icon' => 'fire', 'category' => 'streak', 'target' => 30],
            'streak_100' => ['title' => 'Unstoppable', 'desc' => 'Maintain a 100-day streak.', 'icon' => 'fire', 'category' => 'streak', 'target' => 100],
        ];
    }

    public static function check($category, $livewireComponent)
    {
        $user = Auth::user();
        if (!$user) return;

        $achievements = collect(self::all())->where('category', $category);
        
        $currentValue = 0;
        
        switch ($category) {
            case 'vocabulary':
                $currentValue = Vocabulary::whereNotNull('example_sentence')->count();
                break;
            case 'grammar':
                $currentValue = GrammarLesson::where('is_completed', true)->count();
                break;
            case 'reading':
                $currentValue = ReadingAttempt::count();
                break;
            case 'journal':
                $currentValue = JournalEntry::count();
                break;
            case 'mistakes':
                $currentValue = Mistake::count();
                break;
            case 'time':
                $currentValue = round(StudySession::sum('duration_seconds') / 60);
                break;
            case 'streak':
                $currentValue = self::calculateStreak();
                break;
        }

        $unlockedKeys = AchievementUnlock::where('user_id', $user->id)->pluck('achievement_key')->toArray();

        foreach ($achievements as $key => $ach) {
            if (!in_array($key, $unlockedKeys) && $currentValue >= $ach['target']) {
                // Unlock it!
                AchievementUnlock::create([
                    'user_id' => $user->id,
                    'achievement_key' => $key
                ]);

                // Dispatch browser event
                $livewireComponent->dispatch('achievement-unlocked', 
                    title: $ach['title'], 
                    icon: $ach['icon']
                );
            }
        }
    }

    private static function calculateStreak()
    {
        $dates = StudySession::selectRaw('DATE(created_at) as date')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->pluck('date')
            ->toArray();

        if (empty($dates)) return 0;
        
        $streak = 0;
        $mostRecent = \Carbon\Carbon::parse($dates[0]);
        if (!$mostRecent->isToday() && !$mostRecent->isYesterday()) return 0;

        $checkDate = $mostRecent->copy();
        foreach ($dates as $date) {
            if (\Carbon\Carbon::parse($date)->isSameDay($checkDate)) {
                $streak++;
                $checkDate->subDay();
            } else {
                break;
            }
        }
        return $streak;
    }
}
