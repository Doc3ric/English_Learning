<?php

namespace App\Livewire\Journal;

use Livewire\Component;
use App\Models\JournalEntry;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    public $content = '';
    public $editingId = null;

    public function save()
    {
        $this->validate([
            'content' => 'required|string',
        ]);

        $wordCount = str_word_count(strip_tags($this->content));

        if ($this->editingId) {
            $entry = JournalEntry::find($this->editingId);
            if ($entry) {
                $entry->update([
                    'content' => $this->content,
                    'word_count' => $wordCount,
                ]);
            }
            $this->editingId = null;
        } else {
            JournalEntry::create([
                'content' => $this->content,
                'word_count' => $wordCount,
            ]);
        }

        \App\Services\AchievementService::check('journal', $this);

        $this->content = '';
        session()->flash('message', 'Journal entry saved!');
    }

    public function edit($id)
    {
        $entry = JournalEntry::find($id);
        if ($entry) {
            $this->editingId = $entry->id;
            $this->content = $entry->content;
        }
    }

    public function cancelEdit()
    {
        $this->editingId = null;
        $this->content = '';
    }

    public function delete($id)
    {
        $entry = JournalEntry::find($id);
        if ($entry) {
            $entry->delete();
        }
    }

    public function getEntriesProperty()
    {
        return JournalEntry::orderBy('created_at', 'desc')->get();
    }

    public function render()
    {
        return view('livewire.journal.index', [
            'entries' => $this->entries
        ]);
    }
}
