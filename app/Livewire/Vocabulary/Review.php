<?php

namespace App\Livewire\Vocabulary;

use Livewire\Component;
use App\Models\Vocabulary;
use Carbon\Carbon;

class Review extends Component
{
    public function getReviewWordsProperty()
    {
        // Words added in the last 7 days, not yet mastered, but have an example sentence.
        // Importantly, only show words from *previous* days (not today's words).
        return Vocabulary::where('is_mastered', false)
            ->whereNotNull('example_sentence')
            ->where('created_at', '<', Carbon::today())
            ->where('created_at', '>=', Carbon::today()->subDays(7))
            ->get();
    }

    public function markMastered($id)
    {
        $word = Vocabulary::find($id);
        if ($word) {
            $word->update(['is_mastered' => true]);
        }
    }

    public function render()
    {
        return view('livewire.vocabulary.review', [
            'words' => $this->reviewWords
        ]);
    }
}
