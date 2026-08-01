<?php

namespace App\Livewire\Reading;

use Livewire\Component;
use App\Models\ReadingArticle;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Practice extends Component
{
    public $article;
    public $hasStarted = false;
    public $startTime = null;
    public $elapsedSeconds = 0;

    public function mount($id)
    {
        $this->article = ReadingArticle::findOrFail($id);
        
        if (session('reading_article_id') == $this->article->id && session('reading_start_time')) {
            $this->hasStarted = true;
            $this->startTime = session('reading_start_time');
            $this->elapsedSeconds = now()->timestamp - $this->startTime;
        }
    }

    public function startPractice()
    {
        $this->hasStarted = true;
        $this->startTime = now()->timestamp;
        $this->elapsedSeconds = 0;
        
        session(['reading_article_id' => $this->article->id]);
        session(['reading_start_time' => $this->startTime]);
    }

    public function finishReading()
    {
        return redirect()->route('reading.quiz', $this->article->id);
    }

    public function render()
    {
        return view('livewire.reading.practice');
    }
}
