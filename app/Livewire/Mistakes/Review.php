<?php

namespace App\Livewire\Mistakes;

use Livewire\Component;
use App\Models\Mistake;

class Review extends Component
{
    public $currentMistake = null;
    public $showAnswer = false;

    public function mount()
    {
        $this->loadRandomMistake();
    }

    public function loadRandomMistake()
    {
        $this->currentMistake = Mistake::inRandomOrder()->first();
        $this->showAnswer = false;
    }

    public function reveal()
    {
        $this->showAnswer = true;
        if ($this->currentMistake) {
            $this->currentMistake->increment('times_reviewed');
        }
    }

    public function render()
    {
        return view('livewire.mistakes.review');
    }
}
