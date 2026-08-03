<div>
    <h3 class="text-lg font-semibold text-slate-100 mb-6">Today's Words to Learn</h3>

    @if($words->isEmpty())
        <div class="bg-slate-900 border border-slate-800 rounded-lg p-12 text-center">
            <div class="w-16 h-16 rounded-full bg-slate-800 flex items-center justify-center mx-auto mb-4 text-emerald-500 text-2xl font-bold">
                ✓
            </div>
            <h4 class="text-slate-100 font-semibold text-lg">All caught up!</h4>
            <p class="text-slate-400 mt-2">You have no new words to learn today. Great job!</p>
        </div>
    @else
        <div class="grid gap-6">
            @foreach($words as $word)
                <div class="bg-slate-900 border border-slate-800 rounded-lg p-6 flex flex-col md:flex-row gap-6" wire:key="word-{{ $word->id }}">
                    <!-- Word Info -->
                    <div class="md:w-1/3">
                        <div class="flex items-baseline gap-2 mb-2 flex-wrap">
                            <h4 class="text-2xl font-bold text-slate-100">{{ $word->word }}</h4>
                            @if($word->part_of_speech)
                                <span class="text-xs font-medium text-slate-500 italic">{{ $word->part_of_speech }}</span>
                            @endif
                            @if($word->source === 'writing_coach')
                                <span class="text-xs font-semibold px-2 py-0.5 bg-emerald-500/15 text-emerald-400 border border-emerald-500/20 rounded-full">✍ From Writing Coach</span>
                            @endif
                        </div>
                        @if($word->pronunciation)
                            <p class="text-sm text-slate-400 mb-2 font-mono">/{{ $word->pronunciation }}/</p>
                        @endif
                        <p class="text-slate-300">{{ $word->meaning }}</p>
                    </div>
                    
                    <!-- Practice Area -->
                    <div class="md:w-2/3 border-t md:border-t-0 md:border-l border-slate-800 pt-4 md:pt-0 md:pl-6">
                        <label class="block text-sm font-medium text-slate-400 mb-2">Write your own example sentence to mark as learned:</label>
                        <form wire:submit.prevent="saveExample({{ $word->id }}, sentences[{{ $word->id }}] ?? '')" class="flex gap-3">
                            <input wire:model="sentences.{{ $word->id }}" type="text" placeholder="e.g. He is very {{ $word->word }}..." class="flex-1 bg-slate-950 border border-slate-800 rounded-md py-2 px-3 text-slate-200 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" required>
                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-bold py-2 px-4 rounded-md transition-colors whitespace-nowrap">
                                Mark Learned
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
