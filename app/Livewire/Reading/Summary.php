<?php

namespace App\Livewire\Reading;

use Livewire\Component;
use App\Models\ReadingSummary;

class Summary extends Component
{
    public $articleId;
    public $summaryText = '';
    public $wordCount = 0;
    public $saved = false;

    public function mount($articleId)
    {
        $this->articleId = $articleId;
    }

    public function updatedSummaryText()
    {
        $this->wordCount = str_word_count(trim($this->summaryText));
        $this->saved = false;
    }

    public function saveSummary()
    {
        $this->validate([
            'summaryText' => 'required|string'
        ]);

        ReadingSummary::create([
            'reading_article_id' => $this->articleId,
            'summary_text' => $this->summaryText,
            'word_count' => $this->wordCount,
        ]);

        $this->saved = true;
    }

    public function render()
    {
        return view('livewire.reading.summary');
    }
}
