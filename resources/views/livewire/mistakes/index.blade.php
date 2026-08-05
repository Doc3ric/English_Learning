<div>
    <x-slot:header>
        Mistake Notebook
    </x-slot>

    <x-ui.tab-bar>
        <x-ui.tab value="list"   label="Mistake Log"   :active="$activeTab" wire:click="$set('activeTab', 'list')" />
        <x-ui.tab value="review" label="Review Mode"   :active="$activeTab" wire:click="$set('activeTab', 'review')" />
    </x-ui.tab-bar>

    @if($activeTab === 'list')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-6">
            {{-- Form Column --}}
            <div class="lg:col-span-1">
                <div class="ds-card p-6 sticky top-24">
                    <h3 class="ds-section-title mb-4">
                        {{ $editingId ? 'Edit Mistake' : 'Log Mistake' }}
                    </h3>

                    @if (session()->has('message'))
                        <div class="mb-4 p-3 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">
                            {{ session('message') }}
                        </div>
                    @endif

                    <form wire:submit="save" class="space-y-4">
                        <div>
                            <label class="ds-label">What did you say/write? (Wrong) *</label>
                            <textarea wire:model="wrong_text" rows="2" class="ds-textarea text-red-300" required></textarea>
                            @error('wrong_text') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="ds-label">How should it be? (Correct) *</label>
                            <textarea wire:model="correct_text" rows="2" class="ds-textarea text-emerald-300" required></textarea>
                            @error('correct_text') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="ds-label">Reason / Explanation</label>
                            <textarea wire:model="reason" rows="2" class="ds-textarea"></textarea>
                        </div>

                        <div>
                            <label class="ds-label">Category *</label>
                            <select wire:model="category" class="ds-select" required>
                                <option value="">Select a category...</option>
                                <option value="grammar">Grammar</option>
                                <option value="vocabulary">Vocabulary</option>
                                <option value="pronunciation">Pronunciation</option>
                            </select>
                            @error('category') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex gap-2 pt-2">
                            <button type="submit" class="ds-btn ds-btn-md ds-btn-primary flex-1">
                                {{ $editingId ? 'Update' : 'Log Mistake' }}
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

            {{-- Mistakes List --}}
            <div class="lg:col-span-2 space-y-4">
                <h3 class="ds-section-title mb-4">Past Mistakes</h3>

                @forelse($mistakes as $mistake)
                    <div class="ds-card p-5 group">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex items-center gap-2 flex-wrap">
                                <x-ui.badge class="capitalize">{{ $mistake->category }}</x-ui.badge>
                                @if($mistake->source === 'writing_coach')
                                    <x-ui.badge variant="emerald">✍ Writing Coach</x-ui.badge>
                                @endif
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="ds-muted">Reviewed {{ $mistake->times_reviewed }} times</span>
                                <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button wire:click="edit({{ $mistake->id }})" class="ds-btn-ghost p-1.5 rounded-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button wire:click="delete({{ $mistake->id }})" class="p-1.5 rounded-lg text-slate-500 hover:text-red-400 hover:bg-red-500/10 transition-colors" onclick="confirm('Are you sure you want to delete this mistake?') || event.stopImmediatePropagation()">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3 mt-4">
                            <div class="flex items-start gap-3">
                                <span class="text-red-400 mt-1 font-bold">✗</span>
                                <p class="text-red-300 bg-red-500/10 p-2 rounded-lg w-full line-through">{{ $mistake->wrong_text }}</p>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="text-emerald-400 mt-1 font-bold">✓</span>
                                <p class="text-emerald-300 bg-emerald-500/10 p-2 rounded-lg w-full font-medium">{{ $mistake->correct_text }}</p>
                            </div>
                        </div>

                        @if($mistake->reason)
                            <div class="mt-4 pt-3 border-t border-slate-800">
                                <p class="ds-body text-slate-300"><strong class="text-slate-400 font-semibold">Why:</strong> {{ $mistake->reason }}</p>
                            </div>
                        @endif
                    </div>
                @empty
                    <x-ui.empty-state
                        icon="✏️"
                        title="No mistakes logged yet."
                        body="Learning happens through mistakes! Log one when you're ready."
                    />
                @endforelse
            </div>
        </div>
    @elseif($activeTab === 'review')
        <livewire:mistakes.review />
    @endif
</div>
