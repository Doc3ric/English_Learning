<?php

namespace App\Livewire\Grammar;

use Livewire\Component;
use App\Models\GrammarLesson;
use App\Models\QuizAttempt;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Quiz extends Component
{
    public $lesson;
    public $answers = []; // Selected answers
    public $submitted = false;
    public $score = 0;
    public $passed = false;

    public function mount($id)
    {
        $this->lesson = GrammarLesson::with('questions')->findOrFail($id);
        
        // Security check: cannot access a locked lesson quiz by guessing URL
        $previousLesson = GrammarLesson::where('order_index', '<', $this->lesson->order_index)
                                       ->orderBy('order_index', 'desc')
                                       ->first();
                                       
        if ($previousLesson && !$previousLesson->is_completed) {
            return redirect()->route('grammar')->with('error', 'You must complete previous lessons before accessing this quiz.');
        }

        if ($this->lesson->questions->count() == 0) {
            return redirect()->route('grammar.show', $this->lesson->id)->with('error', 'No questions available for this quiz.');
        }
    }

    public function submitQuiz()
    {
        $correctCount = 0;
        $totalQuestions = $this->lesson->questions->count();

        foreach ($this->lesson->questions as $question) {
            $userAnswer = $this->answers[$question->id] ?? null;
            if ($userAnswer === $question->correct_answer) {
                $correctCount++;
            }
        }

        $this->score = $correctCount;
        $this->passed = ($correctCount === $totalQuestions); // Must get 100% to pass

        QuizAttempt::create([
            'grammar_lesson_id' => $this->lesson->id,
            'score' => $this->score,
            'passed' => $this->passed,
        ]);

        if ($this->passed) {
            $this->lesson->update(['is_completed' => true]);
        }

        $this->submitted = true;
    }

    public function retry()
    {
        $this->answers = [];
        $this->submitted = false;
        $this->score = 0;
        $this->passed = false;
    }

    public function render()
    {
        return view('livewire.grammar.quiz');
    }
}
