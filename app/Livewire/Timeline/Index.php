<?php

namespace App\Livewire\Timeline;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Vocabulary;
use App\Models\GrammarLesson;
use App\Models\JournalEntry;
use App\Models\ReadingAttempt;

#[Layout('layouts.app')]
class Index extends Component
{
    public function getTimelineProperty()
    {
        $activities = collect();

        // Vocab (learned)
        $vocab = Vocabulary::whereNotNull('example_sentence')->get()->map(function($v) {
            return (object)[
                'type' => 'vocabulary',
                'title' => 'Learned new word: ' . $v->word,
                'subtitle' => $v->meaning,
                'date' => $v->updated_at,
                'url' => route('vocabulary')
            ];
        });
        $activities = $activities->concat($vocab);

        // Grammar (completed)
        $grammar = GrammarLesson::where('is_completed', true)->get()->map(function($g) {
            return (object)[
                'type' => 'grammar',
                'title' => 'Completed lesson: ' . $g->title,
                'subtitle' => 'Level: ' . strtoupper($g->level),
                'date' => $g->updated_at,
                'url' => route('grammar.show', $g->id)
            ];
        });
        $activities = $activities->concat($grammar);

        // Journal
        $journals = JournalEntry::all()->map(function($j) {
            return (object)[
                'type' => 'journal',
                'title' => 'Wrote journal entry: ' . $j->title,
                'subtitle' => $j->word_count . ' words',
                'date' => $j->created_at,
                'url' => route('journal')
            ];
        });
        $activities = $activities->concat($journals);

        // Reading
        $readings = ReadingAttempt::with('article')->get()->map(function($r) {
            return (object)[
                'type' => 'reading',
                'title' => 'Read article: ' . $r->article->title,
                'subtitle' => 'Score: ' . $r->score . '% | WPM: ' . $r->wpm,
                'date' => $r->created_at,
                'url' => route('reading')
            ];
        });
        $activities = $activities->concat($readings);

        return $activities->sortByDesc('date')->groupBy(function($item) {
            return $item->date->format('l, F j, Y');
        });
    }

    public function render()
    {
        return view('livewire.timeline.index', [
            'timeline' => $this->timeline
        ]);
    }
}
