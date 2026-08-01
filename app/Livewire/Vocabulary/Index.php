<?php

namespace App\Livewire\Vocabulary;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    public $activeTab = 'daily'; // 'daily', 'review', 'add'

    public function render()
    {
        return view('livewire.vocabulary.index');
    }
}
