<div class="mt-8 max-w-2xl mx-auto">
    @if(!$currentMistake)
        <x-ui.empty-state
            icon="🎉"
            title="All caught up!"
            body="You don't have any mistakes to review yet."
        />
    @else
        <div class="ds-card !p-0 overflow-hidden shadow-lg">
            <div class="p-4 border-b border-slate-800 flex justify-between items-center bg-slate-900/50">
                <span class="ds-eyebrow text-slate-400">{{ $currentMistake->category }}</span>
                <span class="ds-muted text-xs">Reviewed {{ $currentMistake->times_reviewed }} times</span>
            </div>

            <div class="p-8 text-center">
                <p class="ds-eyebrow text-slate-400 mb-4">Correct this mistake:</p>
                <h3 class="text-2xl font-medium text-slate-100 mb-8 leading-relaxed">
                    {{ $currentMistake->wrong_text }}
                </h3>

                @if(!$showAnswer)
                    <button wire:click="reveal" class="ds-btn ds-btn-lg ds-btn-secondary">
                        Show Correction
                    </button>
                @else
                    <div class="ds-card-accent-emerald inline-block w-full text-left mb-6">
                        <p class="text-emerald-400 font-bold text-xl mb-2">{{ $currentMistake->correct_text }}</p>
                        @if($currentMistake->reason)
                            <p class="text-slate-300 text-sm mt-4 pt-4 border-t border-emerald-500/20">
                                <strong class="text-emerald-500 font-semibold">Explanation:</strong> {{ $currentMistake->reason }}
                            </p>
                        @endif
                    </div>

                    <button wire:click="loadRandomMistake" class="ds-btn ds-btn-lg ds-btn-primary">
                        Next Mistake &rarr;
                    </button>
                @endif
            </div>
        </div>
    @endif
</div>
