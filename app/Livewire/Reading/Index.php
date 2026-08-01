<?php

namespace App\Livewire\Reading;

use Livewire\Component;
use App\Models\ReadingArticle;
use App\Models\ReadingAttempt;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    public function getArticlesProperty()
    {
        return ReadingArticle::withCount('attempts')->with('attempts')->latest()->get();
    }

    public function getStatsProperty()
    {
        $attempts = ReadingAttempt::all();
        $totalAttempts = $attempts->count();
        
        $avgScore = 0;
        $avgWpm = 0;
        
        if ($totalAttempts > 0) {
            $totalPercentage = $attempts->sum(function($attempt) {
                return $attempt->total_questions > 0 ? ($attempt->score / $attempt->total_questions) * 100 : 0;
            });
            $avgScore = round($totalPercentage / $totalAttempts);
            
            $attemptsWithWpm = $attempts->whereNotNull('words_per_minute');
            if ($attemptsWithWpm->count() > 0) {
                $avgWpm = round($attemptsWithWpm->average('words_per_minute'));
            }
        }
        
        return [
            'total_attempts' => $totalAttempts,
            'avg_score' => $avgScore,
            'avg_wpm' => $avgWpm,
        ];
    }

    public function render()
    {
        return view('livewire.reading.index', [
            'articles' => $this->articles,
            'stats' => $this->stats,
        ]);
    }
}
