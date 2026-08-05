<div>
    <x-slot:header>
        Daily Journal
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Editor Column --}}
        <div class="lg:col-span-1">
            <div class="ds-card p-6 sticky top-24">
                <h3 class="ds-section-title mb-4">
                    {{ $editingId ? 'Edit Entry' : 'New Entry' }}
                </h3>

                @if (session()->has('message'))
                    <div class="mb-4 p-3 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">
                        {{ session('message') }}
                    </div>
                @endif

                <form wire:submit="save" class="space-y-4">
                    <div>
                        <textarea
                            wire:model="content"
                            rows="8"
                            placeholder="What did you learn today? What are your goals?"
                            class="ds-textarea"
                        ></textarea>
                        @error('content')
                            <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                        <div class="text-right ds-muted mt-1">
                            {{ str_word_count(strip_tags($content)) }} words
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="ds-btn ds-btn-md ds-btn-primary flex-1">
                            {{ $editingId ? 'Update Entry' : 'Save Entry' }}
                        </button>
                        @if($editingId)
                            <button type="button" wire:click="cancelEdit" class="ds-btn ds-btn-md ds-btn-secondary">
                                Cancel
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- Entries List --}}
        <div class="lg:col-span-2 space-y-4">
            <h3 class="ds-section-title mb-4">Past Entries</h3>

            @forelse($entries as $entry)
                <div class="ds-card p-6 group">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <span class="text-slate-100 font-medium block">{{ $entry->created_at->format('l, F j, Y') }}</span>
                            <span class="ds-muted">{{ $entry->created_at->format('g:i A') }} &middot; {{ $entry->word_count }} words</span>
                        </div>
                        <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button wire:click="edit({{ $entry->id }})" class="ds-btn-ghost p-1.5 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                            <button wire:click="delete({{ $entry->id }})" class="p-1.5 rounded-lg text-slate-500 hover:text-red-400 hover:bg-red-500/10 transition-colors" onclick="confirm('Are you sure you want to delete this entry?') || event.stopImmediatePropagation()">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                    <div class="ds-body text-slate-300 whitespace-pre-wrap leading-relaxed">{{ $entry->content }}</div>
                </div>
            @empty
                <x-ui.empty-state
                    icon="📝"
                    title="No journal entries yet."
                    body="Use the editor on the left to write your first entry."
                />
            @endforelse
        </div>

    </div>
</div>
