<?php

namespace App\Livewire\Reading;

use Livewire\Component;
use App\Models\Vocabulary;

class VocabularyAdd extends Component
{
    public $articleId;
    public $word = '';
    public $translation = '';
    public $example_sentence = '';
    public $part_of_speech = 'Noun';
    public $recentlyAdded = [];

    public function mount($articleId)
    {
        $this->articleId = $articleId;
    }

    public function saveWord()
    {
        $this->validate([
            'word' => 'required|string|max:255',
            'translation' => 'required|string|max:255',
            'example_sentence' => 'nullable|string',
            'part_of_speech' => 'required|string',
        ]);

        $vocab = Vocabulary::create([
            'word' => $this->word,
            'translation' => $this->translation,
            'example_sentence' => $this->example_sentence,
            'part_of_speech' => $this->part_of_speech,
            'source_reading_article_id' => $this->articleId,
        ]);

        $this->recentlyAdded[] = $vocab;

        $this->reset(['word', 'translation', 'example_sentence']);
    }

    public function render()
    {
        return view('livewire.reading.vocabulary-add');
    }
}
