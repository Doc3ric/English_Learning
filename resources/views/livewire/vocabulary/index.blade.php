<div>
    <x-slot:header>
        Vocabulary
    </x-slot>

    <x-ui.tab-bar>
        <x-ui.tab value="daily"  label="Today's Words"      :active="$activeTab" wire:click="$set('activeTab', 'daily')" />
        <x-ui.tab value="review" label="Review (Last 7 Days)" :active="$activeTab" wire:click="$set('activeTab', 'review')" />
        <x-ui.tab value="add"    label="Add Word"            :active="$activeTab" wire:click="$set('activeTab', 'add')" />
    </x-ui.tab-bar>

    <div class="mt-6">
        @if ($activeTab === 'daily')
            <livewire:vocabulary.daily />
        @elseif ($activeTab === 'review')
            <livewire:vocabulary.review />
        @elseif ($activeTab === 'add')
            <livewire:vocabulary.add />
        @endif
    </div>
</div>
