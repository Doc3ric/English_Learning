<?php

namespace App\Livewire\Vocabulary;

use Livewire\Component;
use App\Models\Vocabulary;
use Carbon\Carbon;
use Livewire\Attributes\On;

class Daily extends Component
{
    public $sentences = [];

    #[On('word-added')]
    public function updateList() {}

    public function getWordsProperty()
    {
        // Get up to 10 words that haven't been mastered and have no example sentence (meaning they haven't been "learned" today)
        return Vocabulary::where('is_mastered', false)
            ->whereNull('example_sentence')
            ->limit(10)
            ->get();
    }

    public function saveExample($id, $sentence)
    {
        $sentence = trim($sentence);
        if (empty($sentence)) {
            return; // Prevent saving empty sentences
        }

        $word = Vocabulary::find($id);
        if ($word) {
            $word->update(['example_sentence' => $sentence]);
            \App\Services\AchievementService::check('vocabulary', $this);
        }
    }

    public function render()
    {
        return view('livewire.vocabulary.daily', [
            'words' => $this->words
        ]);
    }
}
