<?php

namespace App\Livewire\Vocabulary;

use Livewire\Component;
use App\Models\Vocabulary;
use Carbon\Carbon;

class Review extends Component
{
    public $totalToReview = 0;

    public function mount()
    {
        $this->totalToReview = $this->reviewWords->count();
    }

    public function getReviewWordsProperty()
    {
        // Words that need review today or haven't been reviewed yet.
        return Vocabulary::where('leitner_box', '<', 5)
            ->whereNotNull('example_sentence')
            ->where(function ($query) {
                $query->whereNull('next_review_date')
                      ->orWhere('next_review_date', '<=', Carbon::today());
            })
            ->get();
    }

    public function gradeWord($id, $difficulty)
    {
        $word = Vocabulary::find($id);
        if (!$word) return;

        $box = $word->leitner_box ?? 1;

        if ($difficulty === 'hard') {
            $box = 1;
        } elseif ($difficulty === 'easy') {
            $box = min(5, $box + 1);
        }
        // medium stays the same box

        $intervals = [
            1 => 1,
            2 => 3,
            3 => 7,
            4 => 14,
            5 => 30
        ];

        $days = $intervals[$box] ?? 1;

        $word->update([
            'leitner_box' => $box,
            'last_reviewed_at' => now(),
            'next_review_date' => Carbon::today()->addDays($days)->toDateString(),
        ]);

        auth()->user()?->addXp(10);
    }

    public function render()
    {
        return view('livewire.vocabulary.review', [
            'words' => $this->reviewWords
        ]);
    }
}
