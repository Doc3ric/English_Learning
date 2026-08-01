<?php

namespace App\Livewire\Grammar;

use Livewire\Component;
use App\Models\GrammarLesson;
use App\Models\QuizQuestion;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Show extends Component
{
    public $lesson;
    
    // Question Form
    public $showQuestionForm = false;
    public $question = '';
    public $option_a = '';
    public $option_b = '';
    public $option_c = '';
    public $option_d = '';
    public $correct_answer = 'A';
    public $explanation = '';

    public function mount($id)
    {
        $this->lesson = GrammarLesson::findOrFail($id);

        // Security check: cannot access a locked lesson by guessing URL
        $previousLesson = GrammarLesson::where('order_index', '<', $this->lesson->order_index)
                                       ->orderBy('order_index', 'desc')
                                       ->first();
                                       
        if ($previousLesson && !$previousLesson->is_completed) {
            return redirect()->route('grammar')->with('error', 'You must complete previous lessons before accessing this one.');
        }
    }

    public function saveQuestion()
    {
        $this->validate([
            'question' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'correct_answer' => 'required|in:A,B,C,D',
        ]);

        QuizQuestion::create([
            'grammar_lesson_id' => $this->lesson->id,
            'question' => $this->question,
            'option_a' => $this->option_a,
            'option_b' => $this->option_b,
            'option_c' => $this->option_c,
            'option_d' => $this->option_d,
            'correct_answer' => $this->correct_answer,
            'explanation' => $this->explanation,
        ]);

        $this->reset(['question', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer', 'explanation', 'showQuestionForm']);
        $this->lesson->refresh();
        session()->flash('message', 'Question added!');
    }

    public function render()
    {
        return view('livewire.grammar.show');
    }
}
