<?php

namespace App\Livewire\Vocabulary;

use Livewire\Component;
use App\Models\Vocabulary;

class Add extends Component
{
    public $word = '';
    public $meaning = '';
    public $pronunciation = '';
    public $part_of_speech = '';
    public $synonyms = '';
    public $antonyms = '';
    public $personal_note = '';

    public function save()
    {
        $this->validate([
            'word' => 'required|string|max:255',
            'meaning' => 'required|string',
        ]);

        Vocabulary::create([
            'word' => $this->word,
            'meaning' => $this->meaning,
            'pronunciation' => $this->pronunciation,
            'part_of_speech' => $this->part_of_speech,
            'synonyms' => $this->synonyms,
            'antonyms' => $this->antonyms,
            'personal_note' => $this->personal_note,
        ]);

        $this->reset();
        $this->dispatch('word-added');
        session()->flash('message', 'Word added successfully!');
    }

    public function render()
    {
        return view('livewire.vocabulary.add');
    }
}
