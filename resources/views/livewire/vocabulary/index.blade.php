<div>
    <x-slot:header>
        Vocabulary
    </x-slot>

    <div class="mb-6 border-b border-slate-800">
        <nav class="-mb-px flex space-x-8">
            <button wire:click="$set('activeTab', 'daily')" class="{{ $activeTab === 'daily' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-slate-400 hover:text-slate-300 hover:border-slate-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                Today's Words
            </button>
            <button wire:click="$set('activeTab', 'review')" class="{{ $activeTab === 'review' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-slate-400 hover:text-slate-300 hover:border-slate-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                Review (Last 7 Days)
            </button>
            <button wire:click="$set('activeTab', 'add')" class="{{ $activeTab === 'add' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-slate-400 hover:text-slate-300 hover:border-slate-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                Add Word
            </button>
        </nav>
    </div>

    <div class="mt-4">
        @if ($activeTab === 'daily')
            <livewire:vocabulary.daily />
        @elseif ($activeTab === 'review')
            <livewire:vocabulary.review />
        @elseif ($activeTab === 'add')
            <livewire:vocabulary.add />
        @endif
    </div>
</div>
