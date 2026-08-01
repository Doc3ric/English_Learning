<?php

namespace App\Livewire\Reading;

use Livewire\Component;
use App\Models\ReadingArticle;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Form extends Component
{
    public $title = '';
    public $level = 'Intermediate';
    public $target_band = '';
    public $full_text = '';
    public $source_url = '';
    public $recommended_time_minutes = 15;

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'level' => 'required|string',
            'target_band' => 'nullable|numeric|min:0|max:9',
            'full_text' => 'required|string',
            'source_url' => 'nullable|url',
            'recommended_time_minutes' => 'required|integer|min:1',
        ]);

        $article = ReadingArticle::create([
            'title' => $this->title,
            'level' => $this->level,
            'target_band' => $this->target_band ?: null,
            'full_text' => $this->full_text,
            'source_url' => $this->source_url,
            'recommended_time_minutes' => $this->recommended_time_minutes,
        ]);

        session()->flash('message', 'Article added successfully! Now add questions for this article.');
        return redirect()->route('reading.questions.create', $article->id);
    }

    public function render()
    {
        return view('livewire.reading.form');
    }
}
