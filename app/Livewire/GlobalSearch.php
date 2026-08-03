<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Vocabulary;
use App\Models\JournalEntry;
use App\Models\GrammarLesson;
use App\Models\Mistake;
use Illuminate\Support\Str;

class GlobalSearch extends Component
{
    public $query = '';

    public function getResultsProperty()
    {
        if (strlen($this->query) < 2) {
            return [];
        }

        $results = [];

        // Vocabulary
        $vocab = Vocabulary::where('word', 'like', "%{$this->query}%")
            ->orWhere('meaning', 'like', "%{$this->query}%")
            ->limit(5)->get();
        foreach($vocab as $v) {
            $results[] = [
                'type' => 'Vocabulary',
                'title' => $v->word,
                'subtitle' => $v->meaning,
                'url' => route('vocabulary')
            ];
        }

        // Journal
        $journals = JournalEntry::where('title', 'like', "%{$this->query}%")
            ->orWhere('content', 'like', "%{$this->query}%")
            ->limit(3)->get();
        foreach($journals as $j) {
            $results[] = [
                'type' => 'Journal',
                'title' => $j->title,
                'subtitle' => Str::limit($j->content, 50),
                'url' => route('journal')
            ];
        }

        // Grammar
        $grammar = GrammarLesson::where('title', 'like', "%{$this->query}%")
            ->orWhere('topic', 'like', "%{$this->query}%")
            ->limit(3)->get();
        foreach($grammar as $g) {
            $results[] = [
                'type' => 'Grammar',
                'title' => $g->title,
                'subtitle' => $g->topic,
                'url' => route('grammar.show', $g->id)
            ];
        }

        // Mistakes
        $mistakes = Mistake::where('mistake', 'like', "%{$this->query}%")
            ->orWhere('correction', 'like', "%{$this->query}%")
            ->limit(3)->get();
        foreach($mistakes as $m) {
            $results[] = [
                'type' => 'Mistake',
                'title' => $m->mistake,
                'subtitle' => $m->correction,
                'url' => route('mistakes')
            ];
        }

        return collect($results);
    }

    public function render()
    {
        return view('livewire.global-search', [
            'results' => $this->results
        ]);
    }
}
