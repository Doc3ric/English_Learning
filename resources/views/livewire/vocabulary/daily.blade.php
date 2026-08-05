<div>
    <h3 class="ds-section-title mb-6">Today's Words to Learn</h3>

    @if($words->isEmpty())
        <x-ui.empty-state
            icon="✓"
            title="All caught up!"
            body="You have no new words to learn today. Great job!"
        />
    @else
        <div class="grid gap-6">
            @foreach($words as $word)
                <div class="ds-card p-6 flex flex-col md:flex-row gap-6" wire:key="word-{{ $word->id }}">

                    {{-- Word Info --}}
                    <div class="md:w-1/3">
                        <div class="flex items-baseline gap-2 mb-2 flex-wrap">
                            <h4 class="text-2xl font-bold text-slate-100">{{ $word->word }}</h4>
                            @if($word->part_of_speech)
                                <span class="ds-muted italic">{{ $word->part_of_speech }}</span>
                            @endif
                            @if($word->source === 'writing_coach')
                                <x-ui.badge variant="emerald">✍ From Writing Coach</x-ui.badge>
                            @endif
                        </div>
                        @if($word->pronunciation)
                            <p class="text-sm text-slate-400 mb-2 font-mono">/{{ $word->pronunciation }}/</p>
                        @endif
                        <p class="ds-body">{{ $word->meaning }}</p>
                    </div>

                    {{-- Practice Area --}}
                    <div class="md:w-2/3 border-t md:border-t-0 md:border-l border-slate-800 pt-4 md:pt-0 md:pl-6">
                        <label class="ds-label">Write your own example sentence to mark as learned:</label>
                        <form wire:submit.prevent="saveExample({{ $word->id }}, sentences[{{ $word->id }}] ?? '')" class="flex gap-3 mt-1">
                            <input
                                wire:model="sentences.{{ $word->id }}"
                                type="text"
                                placeholder="e.g. He is very {{ $word->word }}..."
                                class="ds-input flex-1"
                                required
                            >
                            <button type="submit" class="ds-btn ds-btn-md ds-btn-primary whitespace-nowrap">
                                Mark Learned
                            </button>
                        </form>
                    </div>

                </div>
            @endforeach
        </div>
    @endif
</div>
