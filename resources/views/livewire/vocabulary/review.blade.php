<div>
    <h3 class="ds-section-title mb-6">Today's Review</h3>

    @if($words->isEmpty())
        <x-ui.empty-state
            icon="📖"
            title="Nothing to review right now."
            body="Words you learn will appear here for 7 days so you can review them."
        />
    @else
        <div class="grid gap-4 md:grid-cols-2">
            @foreach($words as $word)
                <div class="ds-card p-5" wire:key="review-{{ $word->id }}">

                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h4 class="text-xl font-bold text-slate-100">{{ $word->word }}</h4>
                            <p class="ds-muted mt-1">{{ $word->meaning }}</p>
                        </div>
                        <x-ui.badge>{{ $word->created_at->diffForHumans() }}</x-ui.badge>
                    </div>

                    <div class="ds-card-nested p-3 text-sm text-slate-300 italic mb-4">
                        "{{ $word->example_sentence }}"
                    </div>

                    <div class="flex gap-2 mt-4">
                        <button wire:click="gradeWord({{ $word->id }}, 'hard')" class="ds-btn ds-btn-sm ds-btn-secondary flex-1 hover:!bg-red-500/20 hover:!text-red-400 hover:!border-red-500/30">
                            Hard (Box 1)
                        </button>
                        <button wire:click="gradeWord({{ $word->id }}, 'medium')" class="ds-btn ds-btn-sm ds-btn-secondary flex-1 hover:!bg-amber-500/20 hover:!text-amber-400 hover:!border-amber-500/30">
                            Medium
                        </button>
                        <button wire:click="gradeWord({{ $word->id }}, 'easy')" class="ds-btn ds-btn-sm ds-btn-primary flex-1 hover:!bg-emerald-600">
                            Easy
                        </button>
                    </div>

                </div>
            @endforeach
        </div>
    @endif
</div>
