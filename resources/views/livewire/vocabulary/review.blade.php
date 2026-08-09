<div>
    <style>
        .perspective-1000 { perspective: 1000px; }
        .transform-style-3d { transform-style: preserve-3d; }
        .backface-hidden { backface-visibility: hidden; }
        .rotate-y-180 { transform: rotateY(180deg); }
    </style>

    <div class="flex items-center justify-between mb-6">
        <h3 class="ds-section-title mb-0">Today's Review</h3>
        @if(!$words->isEmpty())
            <span class="text-sm font-semibold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-3 py-1 rounded-full shadow-sm">
                Card {{ max(1, $totalToReview - $words->count() + 1) }} of {{ max(1, $totalToReview) }}
            </span>
        @endif
    </div>

    @if($words->isEmpty())
        <x-ui.empty-state
            icon="🎉"
            title="All caught up!"
            body="You have reviewed all your scheduled vocabulary for today. Great job!"
        />
    @else
        @php $word = $words->first(); @endphp
        
        <div class="max-w-xl mx-auto w-full py-4" wire:key="flashcard-{{ $word->id }}" x-data="{ flipped: false }">
            <!-- Flashcard Container -->
            <div class="relative w-full perspective-1000 cursor-pointer group" style="height: 350px;" @click="flipped = !flipped">
                <div class="w-full h-full relative transition-transform duration-500 transform-style-3d shadow-2xl rounded-2xl" 
                     :class="{ 'rotate-y-180': flipped }">
                     
                    <!-- Front (Word) -->
                    <div class="absolute inset-0 w-full h-full backface-hidden ds-card border-2 border-slate-700/50 flex flex-col items-center justify-center p-8 bg-gradient-to-br from-slate-800 to-slate-900 group-hover:border-emerald-500/30 transition-colors">
                        <span class="ds-muted italic text-sm mb-3 uppercase tracking-widest font-semibold">{{ $word->part_of_speech ?? 'Vocabulary' }}</span>
                        <h4 class="text-5xl font-black text-slate-100 text-center drop-shadow-md break-words w-full px-4">{{ $word->word }}</h4>
                        <div class="absolute bottom-6 text-slate-500 text-sm flex items-center gap-2 font-medium">
                            <svg class="w-4 h-4 animate-bounce text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-3 3m0 0l-3-3m3 3V9m0-6a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Click to reveal
                        </div>
                    </div>

                    <!-- Back (Meaning) -->
                    <div class="absolute inset-0 w-full h-full backface-hidden rotate-y-180 ds-card border-2 border-emerald-500/30 flex flex-col items-center justify-center p-8 bg-gradient-to-br from-slate-900 to-slate-950 text-center overflow-y-auto shadow-emerald-500/10">
                        <h4 class="text-3xl font-bold text-slate-100 mb-2">{{ $word->word }}</h4>
                        @if($word->pronunciation)
                            <p class="text-emerald-400 font-mono mb-4 text-sm bg-emerald-500/10 px-2 py-0.5 rounded">{{ $word->pronunciation }}</p>
                        @endif
                        <p class="text-lg text-slate-300 mb-6 font-medium">{{ $word->meaning }}</p>
                        
                        <div class="w-full bg-slate-800/80 p-4 rounded-xl border border-slate-700/80 shadow-inner">
                            <p class="text-sm text-slate-400 italic">"{{ $word->example_sentence }}"</p>
                        </div>
                        
                        @if($word->synonyms || $word->antonyms)
                            <div class="mt-4 flex gap-4 text-xs text-slate-500 justify-center">
                                @if($word->synonyms)
                                    <div><span class="text-slate-400 font-semibold">Syn:</span> {{ $word->synonyms }}</div>
                                @endif
                                @if($word->antonyms)
                                    <div><span class="text-slate-400 font-semibold">Ant:</span> {{ $word->antonyms }}</div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Grading Buttons (Only visible when flipped) -->
            <div class="mt-8 transition-all duration-300 transform origin-top" 
                 x-show="flipped" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100">
                 
                 <p class="text-center text-sm font-semibold text-slate-400 mb-4 uppercase tracking-wider flex items-center justify-center gap-2">
                    <span class="h-px bg-slate-700 w-12"></span>
                    How well did you know this?
                    <span class="h-px bg-slate-700 w-12"></span>
                 </p>
                 
                <div class="flex gap-3">
                    <button wire:click="gradeWord({{ $word->id }}, 'hard')" class="ds-btn ds-btn-lg ds-btn-secondary flex-1 hover:!bg-red-500/10 hover:!text-red-400 hover:!border-red-500/30 flex-col py-3 h-auto">
                        <span class="text-base font-bold mb-1">Hard</span>
                        <span class="text-[10px] opacity-60 uppercase tracking-widest font-normal">Review Tomorrow</span>
                    </button>
                    <button wire:click="gradeWord({{ $word->id }}, 'medium')" class="ds-btn ds-btn-lg ds-btn-secondary flex-1 hover:!bg-amber-500/10 hover:!text-amber-400 hover:!border-amber-500/30 flex-col py-3 h-auto">
                        <span class="text-base font-bold mb-1">Medium</span>
                        <span class="text-[10px] opacity-60 uppercase tracking-widest font-normal">Stay in Box {{ $word->leitner_box ?? 1 }}</span>
                    </button>
                    <button wire:click="gradeWord({{ $word->id }}, 'easy')" class="ds-btn ds-btn-lg ds-btn-primary flex-1 hover:!bg-emerald-600 hover:scale-[1.02] transition-transform flex-col py-3 h-auto">
                        <span class="text-base font-bold mb-1">Easy</span>
                        <span class="text-[10px] opacity-70 uppercase tracking-widest font-normal text-emerald-100">Box {{ min(5, ($word->leitner_box ?? 1) + 1) }}</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
