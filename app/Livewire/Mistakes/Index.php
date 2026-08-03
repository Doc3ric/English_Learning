<?php

namespace App\Livewire\Mistakes;

use Livewire\Component;
use App\Models\Mistake;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    public $wrong_text = '';
    public $correct_text = '';
    public $reason = '';
    public $category = '';
    public $editingId = null;

    public $activeTab = 'list'; // list, review

    public function save()
    {
        $this->validate([
            'wrong_text' => 'required|string',
            'correct_text' => 'required|string',
            'category' => 'required|in:grammar,vocabulary,pronunciation',
        ]);

        if ($this->editingId) {
            $mistake = Mistake::find($this->editingId);
            if ($mistake) {
                $mistake->update([
                    'wrong_text' => $this->wrong_text,
                    'correct_text' => $this->correct_text,
                    'reason' => $this->reason,
                    'category' => $this->category,
                ]);
            }
            $this->editingId = null;
        } else {
            Mistake::create([
                'wrong_text' => $this->wrong_text,
                'correct_text' => $this->correct_text,
                'reason' => $this->reason,
                'category' => $this->category,
            ]);

            \App\Services\AchievementService::check('mistakes', $this);
        }

        $this->resetForm();
        session()->flash('message', 'Mistake logged successfully!');
    }

    public function edit($id)
    {
        $mistake = Mistake::find($id);
        if ($mistake) {
            $this->editingId = $mistake->id;
            $this->wrong_text = $mistake->wrong_text;
            $this->correct_text = $mistake->correct_text;
            $this->reason = $mistake->reason;
            $this->category = $mistake->category;
            $this->activeTab = 'list';
        }
    }

    public function cancelEdit()
    {
        $this->editingId = null;
        $this->resetForm();
    }

    public function delete($id)
    {
        $mistake = Mistake::find($id);
        if ($mistake) {
            $mistake->delete();
        }
    }

    public function resetForm()
    {
        $this->wrong_text = '';
        $this->correct_text = '';
        $this->reason = '';
        $this->category = '';
    }

    public function getMistakesProperty()
    {
        return Mistake::orderBy('created_at', 'desc')->get();
    }

    public function render()
    {
        return view('livewire.mistakes.index', [
            'mistakes' => $this->mistakes
        ]);
    }
}
