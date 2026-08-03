<div>
    <x-slot:header>
        Mistake Notebook
    </x-slot>

    <div class="mb-6 border-b border-slate-800">
        <nav class="-mb-px flex space-x-8">
            <button wire:click="$set('activeTab', 'list')" class="{{ $activeTab === 'list' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-slate-400 hover:text-slate-300 hover:border-slate-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                Mistake Log
            </button>
            <button wire:click="$set('activeTab', 'review')" class="{{ $activeTab === 'review' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-slate-400 hover:text-slate-300 hover:border-slate-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                Review Mode
            </button>
        </nav>
    </div>

    @if($activeTab === 'list')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-4">
            <!-- Form Column -->
            <div class="lg:col-span-1">
                <div class="bg-slate-900 border border-slate-800 rounded-lg p-6 sticky top-24">
                    <h3 class="text-lg font-semibold text-slate-100 mb-4">
                        {{ $editingId ? 'Edit Mistake' : 'Log Mistake' }}
                    </h3>

                    @if (session()->has('message'))
                        <div class="mb-4 p-3 rounded bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">
                            {{ session('message') }}
                        </div>
                    @endif

                    <form wire:submit="save" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">What did you say/write? (Wrong) *</label>
                            <textarea wire:model="wrong_text" rows="2" class="w-full bg-slate-950 border border-slate-800 rounded-md py-2 px-3 text-red-300 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500" required></textarea>
                            @error('wrong_text') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">How should it be? (Correct) *</label>
                            <textarea wire:model="correct_text" rows="2" class="w-full bg-slate-950 border border-slate-800 rounded-md py-2 px-3 text-emerald-300 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" required></textarea>
                            @error('correct_text') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Reason / Explanation</label>
                            <textarea wire:model="reason" rows="2" class="w-full bg-slate-950 border border-slate-800 rounded-md py-2 px-3 text-slate-200 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Category *</label>
                            <select wire:model="category" class="w-full bg-slate-950 border border-slate-800 rounded-md py-2 px-3 text-slate-200 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" required>
                                <option value="">Select a category...</option>
                                <option value="grammar">Grammar</option>
                                <option value="vocabulary">Vocabulary</option>
                                <option value="pronunciation">Pronunciation</option>
                            </select>
                            @error('category') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex gap-2 pt-2">
                            <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-bold py-2 px-4 rounded-md transition-colors">
                                {{ $editingId ? 'Update' : 'Log Mistake' }}
                            </button>
                            @if($editingId)
                                <button type="button" wire:click="cancelEdit" class="bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium py-2 px-4 rounded-md transition-colors border border-slate-700">
                                    Cancel
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Mistakes List -->
            <div class="lg:col-span-2 space-y-4">
                <h3 class="text-lg font-semibold text-slate-100 mb-4">Past Mistakes</h3>
                
                @forelse($mistakes as $mistake)
                    <div class="bg-slate-900 border border-slate-800 rounded-lg p-5 group">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="inline-block px-2 py-1 bg-slate-950 text-xs font-medium text-slate-400 rounded border border-slate-800 capitalize">
                                    {{ $mistake->category }}
                                </span>
                                @if($mistake->source === 'writing_coach')
                                    <span class="text-xs font-semibold px-2 py-0.5 bg-emerald-500/15 text-emerald-400 border border-emerald-500/20 rounded-full">✍ Writing Coach</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-slate-500">Reviewed {{ $mistake->times_reviewed }} times</span>
                                <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button wire:click="edit({{ $mistake->id }})" class="text-slate-400 hover:text-emerald-400 p-1 rounded hover:bg-slate-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button wire:click="delete({{ $mistake->id }})" class="text-slate-400 hover:text-red-400 p-1 rounded hover:bg-slate-800" onclick="confirm('Are you sure you want to delete this mistake?') || event.stopImmediatePropagation()">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-3 mt-4">
                            <div class="flex items-start gap-3">
                                <span class="text-red-400 mt-1 font-bold">✗</span>
                                <p class="text-red-300 bg-red-900/10 p-2 rounded-md w-full line-through">{{ $mistake->wrong_text }}</p>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="text-emerald-400 mt-1 font-bold">✓</span>
                                <p class="text-emerald-300 bg-emerald-900/10 p-2 rounded-md w-full font-medium">{{ $mistake->correct_text }}</p>
                            </div>
                        </div>

                        @if($mistake->reason)
                            <div class="mt-4 pt-3 border-t border-slate-800/50">
                                <p class="text-sm text-slate-400"><strong class="text-slate-300">Why:</strong> {{ $mistake->reason }}</p>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="bg-slate-900 border border-slate-800 rounded-lg p-12 text-center text-slate-400">
                        No mistakes logged yet. Learning happens through mistakes!
                    </div>
                @endforelse
            </div>
        </div>
    @elseif($activeTab === 'review')
        <livewire:mistakes.review />
    @endif
</div>
