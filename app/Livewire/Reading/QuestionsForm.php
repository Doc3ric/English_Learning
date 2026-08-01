<?php

namespace App\Livewire\Reading;

use Livewire\Component;
use App\Models\ReadingArticle;
use App\Models\ReadingQuestion;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class QuestionsForm extends Component
{
    public $article;
    public $questions = [];
    
    public $question_text = '';
    public $question_type = 'multiple_choice';
    public $option_a = '';
    public $option_b = '';
    public $option_c = '';
    public $option_d = '';
    public $correct_answer = 'A';
    public $explanation = '';

    public function mount($id)
    {
        $this->article = ReadingArticle::findOrFail($id);
        $this->loadQuestions();
    }

    public function updatedQuestionType()
    {
        if ($this->question_type === 'multiple_choice') {
            $this->correct_answer = 'A';
        } elseif ($this->question_type === 'true_false_not_given') {
            $this->correct_answer = 'True';
        } else {
            $this->correct_answer = '';
        }
    }

    public function loadQuestions()
    {
        $this->questions = $this->article->questions()->get();
    }

    public function saveQuestion()
    {
        $rules = [
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,true_false_not_given,short_answer',
            'correct_answer' => 'required|string',
        ];

        if ($this->question_type === 'multiple_choice') {
            $rules['option_a'] = 'required|string';
            $rules['option_b'] = 'required|string';
            $rules['correct_answer'] = 'required|in:A,B,C,D';
        } elseif ($this->question_type === 'true_false_not_given') {
            $rules['correct_answer'] = 'required|in:True,False,Not Given';
        }

        $this->validate($rules);

        ReadingQuestion::create([
            'reading_article_id' => $this->article->id,
            'question_text' => $this->question_text,
            'question_type' => $this->question_type,
            'option_a' => $this->question_type === 'multiple_choice' ? $this->option_a : null,
            'option_b' => $this->question_type === 'multiple_choice' ? $this->option_b : null,
            'option_c' => $this->question_type === 'multiple_choice' ? $this->option_c : null,
            'option_d' => $this->question_type === 'multiple_choice' ? $this->option_d : null,
            'correct_answer' => $this->correct_answer,
            'explanation' => $this->explanation,
        ]);

        $this->reset(['question_text', 'option_a', 'option_b', 'option_c', 'option_d', 'explanation']);
        $this->updatedQuestionType();
        
        $this->loadQuestions();
        session()->flash('message', 'Question added successfully!');
    }

    public function deleteQuestion($id)
    {
        ReadingQuestion::where('id', $id)->where('reading_article_id', $this->article->id)->delete();
        $this->loadQuestions();
    }

    public function render()
    {
        return view('livewire.reading.questions-form');
    }
}
