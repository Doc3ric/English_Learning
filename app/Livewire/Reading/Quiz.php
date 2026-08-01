<?php

namespace App\Livewire\Reading;

use Livewire\Component;
use App\Models\ReadingArticle;
use App\Models\ReadingAttempt;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Quiz extends Component
{
    public $article;
    public $questions;
    public $answers = [];
    public $isSubmitted = false;
    public $score = 0;
    public $timeTakenSeconds = 0;
    public $wpm = null;
    public $results = [];
    
    public $elapsedSeconds = 0;

    public function mount($id)
    {
        $this->article = ReadingArticle::findOrFail($id);
        $this->questions = $this->article->questions;
        
        foreach ($this->questions as $q) {
            $this->answers[$q->id] = '';
        }
        
        if (session('reading_article_id') == $this->article->id && session('reading_start_time')) {
            $this->elapsedSeconds = now()->timestamp - session('reading_start_time');
        }
    }

    public function submitQuiz()
    {
        $this->isSubmitted = true;
        $correctCount = 0;
        $this->results = [];
        
        foreach ($this->questions as $q) {
            $userAnswer = trim($this->answers[$q->id] ?? '');
            
            if ($q->question_type === 'short_answer') {
                $isCorrect = strtolower($userAnswer) === strtolower($q->correct_answer);
            } else {
                $isCorrect = $userAnswer === $q->correct_answer;
            }
            
            if ($isCorrect) {
                $correctCount++;
            }
            
            $this->results[$q->id] = [
                'user_answer' => $userAnswer,
                'is_correct' => $isCorrect,
                'correct_answer' => $q->correct_answer,
                'explanation' => $q->explanation
            ];
        }
        
        $this->score = $correctCount;
        
        if (session('reading_start_time')) {
            $this->timeTakenSeconds = now()->timestamp - session('reading_start_time');
            
            $wordCount = str_word_count(strip_tags($this->article->full_text));
            if ($this->timeTakenSeconds > 0) {
                $this->wpm = round($wordCount / ($this->timeTakenSeconds / 60));
            }
            
            session()->forget(['reading_start_time', 'reading_article_id']);
        }
        
        ReadingAttempt::create([
            'reading_article_id' => $this->article->id,
            'score' => $this->score,
            'total_questions' => count($this->questions),
            'time_taken_seconds' => $this->timeTakenSeconds,
            'words_per_minute' => $this->wpm,
        ]);
    }

    public function render()
    {
        return view('livewire.reading.quiz');
    }
}
