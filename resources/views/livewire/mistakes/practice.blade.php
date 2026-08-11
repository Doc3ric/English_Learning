<div class="max-w-4xl mx-auto space-y-6 py-4">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('mistakes') }}" class="text-xs text-slate-400 hover:text-emerald-400 font-medium flex items-center gap-1 transition-colors">
                    ← Back to Mistakes
                </a>
                <span class="text-slate-600">•</span>
                <span class="text-xs font-bold text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded border border-amber-500/20">Targeted Practice</span>
            </div>
            <h2 class="text-3xl font-black text-white tracking-tight">Weakness Practice Engine</h2>
        </div>
        
        <div class="flex items-center gap-2">
            <button wire:click="setCategory('Grammar')" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $category === 'Grammar' ? 'bg-red-500/20 text-red-400 border border-red-500/30' : 'bg-slate-800 text-slate-400 hover:text-slate-200' }}">
                Grammar
            </button>
            <button wire:click="setCategory('Vocabulary')" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $category === 'Vocabulary' ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : 'bg-slate-800 text-slate-400 hover:text-slate-200' }}">
                Vocabulary
            </button>
            <button wire:click="setCategory('Writing')" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $category === 'Writing' ? 'bg-purple-500/20 text-purple-400 border border-purple-500/30' : 'bg-slate-800 text-slate-400 hover:text-slate-200' }}">
                Writing
            </button>
        </div>
    </div>

    @if($isCompleted)
        {{-- Completion Screen --}}
        <div class="ds-card p-8 text-center max-w-xl mx-auto border-2 border-emerald-500/30 shadow-2xl">
            <div class="w-20 h-20 rounded-full bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center mx-auto mb-4 text-4xl shadow-inner">
                🎉
            </div>
            <h3 class="text-3xl font-black text-white mb-2">Practice Complete!</h3>
            <p class="text-slate-300 text-sm mb-6">You answered <span class="text-emerald-400 font-bold">{{ $score }} of {{ count($questions) }}</span> questions correctly.</p>
            
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-400 font-bold text-sm mb-8 shadow-sm">
                ⭐ +{{ $earnedXp }} XP Awarded to your profile!
            </div>

            <div class="flex gap-4 justify-center">
                <button wire:click="restart" class="ds-btn ds-btn-md ds-btn-secondary">
                    Practice Again
                </button>
                <a href="{{ route('mistakes') }}" class="ds-btn ds-btn-md ds-btn-primary">
                    Return to Mistakes Log
                </a>
            </div>
        </div>
    @else
        @php $q = $questions[$currentIndex]; @endphp
        
        {{-- Question Card --}}
        <div class="ds-card p-8 space-y-6 border border-slate-800 shadow-xl">
            {{-- Progress Header --}}
            <div class="flex justify-between items-center text-xs font-semibold text-slate-400 border-b border-slate-800/80 pb-4">
                <span>Category: <strong class="text-slate-200 uppercase tracking-wider">{{ $category }}</strong></span>
                <span>Question {{ $currentIndex + 1 }} of {{ count($questions) }}</span>
            </div>

            {{-- Question Text --}}
            <div class="bg-slate-950/70 p-6 rounded-2xl border border-slate-800 shadow-inner">
                <p class="text-xl font-bold text-slate-100 leading-relaxed">{{ $q['question'] }}</p>
            </div>

            {{-- Options List --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($q['options'] as $option)
                    @php
                        $isSelected = ($selectedAnswer === $option);
                        $isAnswer = ($q['answer'] === $option);
                        
                        $btnStyle = 'bg-slate-800/60 border-slate-700/80 text-slate-200 hover:border-emerald-500/50 hover:bg-slate-800';
                        if ($isSubmitted) {
                            if ($isAnswer) {
                                $btnStyle = 'bg-emerald-500/20 border-emerald-500/60 text-emerald-300 font-bold shadow-[0_0_15px_rgba(16,185,129,0.2)]';
                            } elseif ($isSelected && !$isCorrect) {
                                $btnStyle = 'bg-red-500/20 border-red-500/60 text-red-300 font-bold';
                            } else {
                                $btnStyle = 'bg-slate-900/40 border-slate-800 text-slate-500 opacity-60';
                            }
                        } elseif ($isSelected) {
                            $btnStyle = 'bg-emerald-500/10 border-emerald-500/50 text-emerald-400 font-semibold shadow-inner';
                        }
                    @endphp

                    <button wire:click="selectAnswer('{{ $option }}')" 
                            class="p-4 rounded-xl border text-left transition-all flex items-center justify-between {{ $btnStyle }}"
                            {{ $isSubmitted ? 'disabled' : '' }}>
                        <span class="text-base">{{ $option }}</span>
                        @if($isSubmitted && $isAnswer)
                            <span class="text-emerald-400 font-bold text-sm">✓ Correct</span>
                        @elseif($isSubmitted && $isSelected && !$isCorrect)
                            <span class="text-red-400 font-bold text-sm">✕ Incorrect</span>
                        @endif
                    </button>
                @endforeach
            </div>

            {{-- Submit / Next Actions --}}
            <div class="pt-4 border-t border-slate-800/80 flex justify-between items-center">
                <div>
                    @if($isSubmitted)
                        <div class="text-sm font-semibold {{ $isCorrect ? 'text-emerald-400' : 'text-red-400' }}">
                            {{ $isCorrect ? 'Great job! Correct answer.' : 'Not quite. Check the explanation below.' }}
                        </div>
                    @endif
                </div>

                @if(!$isSubmitted)
                    <button wire:click="submitAnswer" 
                            class="ds-btn ds-btn-md ds-btn-primary {{ empty($selectedAnswer) ? 'opacity-50 cursor-not-allowed' : '' }}"
                            {{ empty($selectedAnswer) ? 'disabled' : '' }}>
                        Check Answer
                    </button>
                @else
                    <button wire:click="nextQuestion" class="ds-btn ds-btn-md ds-btn-primary flex items-center gap-2">
                        <span>{{ $currentIndex + 1 < count($questions) ? 'Next Question' : 'Finish Session' }}</span>
                        <span>→</span>
                    </button>
                @endif
            </div>

            {{-- Explanation Box --}}
            @if($isSubmitted)
                <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-300 space-y-1">
                    <p class="font-bold text-slate-400 uppercase tracking-wider">Explanation:</p>
                    <p>{{ $q['explanation'] }}</p>
                </div>
            @endif
        </div>
    @endif
</div>
