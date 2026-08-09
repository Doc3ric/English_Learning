<div>
    <style>
        .perspective-1000 { perspective: 1000px; }
        .transform-style-3d { transform-style: preserve-3d; }
        .backface-hidden { backface-visibility: hidden; }
        .rotate-y-180 { transform: rotateY(180deg); }
    </style>

    <div class="flex items-center justify-between mb-8">
        <div>
            <h3 class="ds-section-title mb-1">Today's Review</h3>
            <p class="ds-muted text-xs">Spaced Repetition System (Leitner Algorithm)</p>
        </div>
        @if(!$words->isEmpty())
            <span class="text-xs font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-3.5 py-1.5 rounded-full shadow-sm flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                Card {{ max(1, $totalToReview - $words->count() + 1) }} of {{ max(1, $totalToReview) }}
            </span>
        @endif
    </div>

    @if($words->isEmpty())
        <x-ui.empty-state
            icon="🎉"
            title="All caught up!"
            body="You have reviewed all your scheduled vocabulary for today. Great job keeping up your streak!"
        />
    @else
        @php $word = $words->first(); @endphp
        
        <div class="max-w-xl mx-auto w-full py-2" wire:key="flashcard-{{ $word->id }}" x-data="{ flipped: false }">
            <!-- 3D Flashcard Container -->
            <div class="relative w-full perspective-1000 cursor-pointer group" style="height: 380px;" @click="flipped = !flipped">
                <div class="w-full relative transition-transform duration-500 transform-style-3d shadow-2xl rounded-3xl" style="height: 380px;"
                     :class="{ 'rotate-y-180': flipped }">
                     
                    <!-- FRONT OF CARD -->
                    <div class="absolute inset-0 backface-hidden ds-card border-2 border-slate-700/60 rounded-3xl p-8 bg-gradient-to-br from-slate-900 via-slate-850 to-slate-950 group-hover:border-emerald-500/40 group-hover:shadow-[0_0_35px_rgba(16,185,129,0.15)] transition-all duration-300 flex flex-col justify-between items-center text-center" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; height: 380px;">
                        
                        <!-- Top: Part of Speech -->
                        <div class="pt-2">
                            <span class="px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-widest bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shadow-sm">
                                {{ $word->part_of_speech ?? 'Vocabulary' }}
                            </span>
                        </div>

                        <!-- Center: Word -->
                        <div class="my-auto px-4 w-full">
                            <h4 class="text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-100 to-slate-300 tracking-tight drop-shadow-md break-words max-w-full">
                                {{ $word->word }}
                            </h4>
                        </div>

                        <!-- Bottom: Flip Prompt -->
                        <div class="pb-2">
                            <div class="flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-800/80 border border-slate-700/60 text-slate-400 text-xs font-medium tracking-wide shadow-inner group-hover:text-emerald-300 group-hover:border-emerald-500/30 transition-all">
                                <svg class="w-4 h-4 animate-bounce text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-3 3m0 0l-3-3m3 3V9m0-6a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Tap anywhere to reveal meaning
                            </div>
                        </div>

                    </div>

                    <!-- BACK OF CARD -->
                    <div class="absolute inset-0 backface-hidden rotate-y-180 ds-card border-2 border-emerald-500/30 rounded-3xl p-8 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-center flex flex-col justify-between overflow-y-auto shadow-emerald-500/10" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; height: 380px;">
                        
                        <div>
                            <!-- Header: Word & Pronunciation -->
                            <div class="flex items-center justify-center gap-3 mb-4">
                                <h4 class="text-3xl font-black text-white tracking-tight">{{ $word->word }}</h4>
                                @if($word->pronunciation)
                                    <span class="text-emerald-400 font-mono text-xs bg-emerald-500/10 border border-emerald-500/20 px-2.5 py-1 rounded-md">{{ $word->pronunciation }}</span>
                                @endif
                            </div>

                            <!-- Meaning Card -->
                            <div class="bg-slate-900/90 border border-slate-800 p-4 rounded-2xl shadow-inner mb-4">
                                <p class="text-base text-slate-200 font-semibold leading-relaxed">{{ $word->meaning }}</p>
                            </div>

                            <!-- Example Sentence -->
                            <div class="bg-slate-800/40 border-l-4 border-emerald-500 p-4 rounded-r-xl text-left">
                                <p class="text-xs uppercase font-bold text-slate-500 tracking-wider mb-1">Example Sentence</p>
                                <p class="text-sm text-slate-300 italic">"{{ $word->example_sentence }}"</p>
                            </div>
                        </div>

                        <!-- Synonyms / Antonyms -->
                        @if($word->synonyms || $word->antonyms)
                            <div class="pt-4 border-t border-slate-800/80 flex flex-wrap gap-4 text-xs text-slate-400 justify-center">
                                @if($word->synonyms)
                                    <div><span class="text-slate-500 font-bold uppercase">Synonyms:</span> {{ $word->synonyms }}</div>
                                @endif
                                @if($word->antonyms)
                                    <div><span class="text-slate-500 font-bold uppercase">Antonyms:</span> {{ $word->antonyms }}</div>
                                @endif
                            </div>
                        @endif

                    </div>

                </div>
            </div>

            <!-- GRADING ACTIONS (Slides in when flipped) -->
            <div class="mt-8 transition-all duration-300 transform origin-top" 
                 x-show="flipped" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100">
                 
                 <div class="text-center text-xs font-bold text-slate-500 mb-4 uppercase tracking-widest flex items-center justify-center gap-3">
                    <span class="h-px bg-slate-800 flex-1"></span>
                    How well did you know this?
                    <span class="h-px bg-slate-800 flex-1"></span>
                 </div>
                 
                <div class="grid grid-cols-3 gap-3">
                    <!-- HARD -->
                    <button wire:click="gradeWord({{ $word->id }}, 'hard')" 
                            class="group p-3 rounded-2xl bg-red-500/10 border border-red-500/20 hover:bg-red-500/20 hover:border-red-500/40 text-red-400 transition-all flex flex-col items-center justify-center text-center">
                        <span class="text-sm font-bold tracking-wide">Hard</span>
                        <span class="text-[10px] text-red-400/70 font-medium mt-0.5">Box 1 (Tomorrow)</span>
                    </button>

                    <!-- MEDIUM -->
                    <button wire:click="gradeWord({{ $word->id }}, 'medium')" 
                            class="group p-3 rounded-2xl bg-amber-500/10 border border-amber-500/20 hover:bg-amber-500/20 hover:border-amber-500/40 text-amber-400 transition-all flex flex-col items-center justify-center text-center">
                        <span class="text-sm font-bold tracking-wide">Medium</span>
                        <span class="text-[10px] text-amber-400/70 font-medium mt-0.5">Box {{ $word->leitner_box ?? 1 }} (Same)</span>
                    </button>

                    <!-- EASY -->
                    <button wire:click="gradeWord({{ $word->id }}, 'easy')" 
                            class="group p-3 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold shadow-lg shadow-emerald-600/25 hover:shadow-emerald-600/40 hover:scale-[1.02] transition-all flex flex-col items-center justify-center text-center">
                        <span class="text-sm font-bold tracking-wide">Easy</span>
                        <span class="text-[10px] text-emerald-100/80 font-medium mt-0.5">Box {{ min(5, ($word->leitner_box ?? 1) + 1) }}</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
