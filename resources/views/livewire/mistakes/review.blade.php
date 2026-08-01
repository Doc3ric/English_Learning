<div class="mt-8 max-w-2xl mx-auto">
    @if(!$currentMistake)
        <div class="bg-slate-900 border border-slate-800 rounded-lg p-12 text-center text-slate-400">
            You don't have any mistakes to review yet.
        </div>
    @else
        <div class="bg-slate-900 border border-slate-800 rounded-lg overflow-hidden shadow-lg">
            <div class="p-4 bg-slate-950 border-b border-slate-800 flex justify-between items-center">
                <span class="text-sm font-medium text-slate-400 uppercase tracking-wider">{{ $currentMistake->category }}</span>
                <span class="text-xs text-slate-500">Reviewed {{ $currentMistake->times_reviewed }} times</span>
            </div>
            
            <div class="p-8 text-center">
                <p class="text-sm text-slate-400 mb-4 uppercase tracking-wider font-semibold">Correct this mistake:</p>
                <h3 class="text-2xl font-medium text-slate-100 mb-8 leading-relaxed">
                    {{ $currentMistake->wrong_text }}
                </h3>

                @if(!$showAnswer)
                    <button wire:click="reveal" class="bg-slate-800 hover:bg-slate-700 text-slate-200 font-medium py-3 px-8 rounded-full transition-colors border border-slate-700">
                        Show Correction
                    </button>
                @else
                    <div class="bg-emerald-900/20 border border-emerald-500/30 rounded-lg p-6 mb-6 inline-block w-full">
                        <p class="text-emerald-400 font-bold text-xl mb-2">{{ $currentMistake->correct_text }}</p>
                        @if($currentMistake->reason)
                            <p class="text-slate-300 text-sm mt-4 pt-4 border-t border-emerald-500/20 text-left">
                                <strong class="text-emerald-500">Explanation:</strong> {{ $currentMistake->reason }}
                            </p>
                        @endif
                    </div>
                    
                    <button wire:click="loadRandomMistake" class="bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-bold py-3 px-8 rounded-full transition-colors">
                        Next Mistake &rarr;
                    </button>
                @endif
            </div>
        </div>
    @endif
</div>
