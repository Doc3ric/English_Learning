<?php

namespace App\Livewire\Grammar;

use Livewire\Component;
use App\Models\GrammarLesson;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    public $title = '';
    public $content = '';
    public $showAddForm = false;

    public function save()
    {
        $this->validate([
            'title' => 'required|string',
            'content' => 'required|string',
        ]);

        $maxOrder = GrammarLesson::max('order_index') ?? 0;

        GrammarLesson::create([
            'title' => $this->title,
            'content' => $this->content,
            'order_index' => $maxOrder + 1,
            'is_completed' => false,
        ]);

        $this->reset(['title', 'content', 'showAddForm']);
        session()->flash('message', 'Lesson added!');
    }

    public function getLessonsProperty()
    {
        return GrammarLesson::orderBy('order_index', 'asc')->get();
    }

    public function render()
    {
        $lessons = $this->lessons;
        $unlockedStatus = [];
        $previousCompleted = true; // The very first lesson is always unlocked

        foreach ($lessons as $lesson) {
            $unlockedStatus[$lesson->id] = $previousCompleted;
            $previousCompleted = $lesson->is_completed;
        }

        return view('livewire.grammar.index', [
            'lessons' => $lessons,
            'unlockedStatus' => $unlockedStatus
        ]);
    }
}
