<div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-lg">

    <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-5">
        <div>
            <h2 class="text-base font-bold text-white tracking-tight flex items-center gap-2">
                🌙 DAILY REFLECTION
                <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider bg-slate-800 border border-slate-700 px-2 py-0.5 rounded-full">2 min</span>
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">A quick structured check-in to end your study day</p>
        </div>
        @if($saved && $existingId)
            <button wire:click="edit" class="text-xs text-violet-400 hover:underline font-semibold">
                Edit →
            </button>
        @endif
    </div>

    @if($saved)
        {{-- Saved state --}}
        <div class="text-center py-6">
            <div class="text-3xl mb-2">✅</div>
            <p class="text-sm font-bold text-emerald-400">Reflection saved!</p>
            <p class="text-xs text-slate-400 mt-1">Great job reflecting on your study day. Come back tomorrow.</p>

            @if($newExpression)
                <div class="mt-4 p-3 rounded-xl bg-violet-500/10 border border-violet-500/20 text-left">
                    <p class="text-[10px] text-violet-400 uppercase font-bold tracking-wider mb-1">Today's New Expression</p>
                    <p class="text-sm text-white italic">"{{ $newExpression }}"</p>
                </div>
            @endif
        </div>

    @else
        {{-- Reflection form --}}
        <div class="space-y-5">

            {{-- Checkboxes --}}
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">How did today go?</p>
                <div class="space-y-2.5">
                    @foreach([
                        ['model' => 'didGrammar',    'label' => 'I understood today\'s grammar lesson'],
                        ['model' => 'didVocabulary', 'label' => 'I used at least 3 new vocabulary words'],
                        ['model' => 'didSpeaking',   'label' => 'I spoke English today'],
                        ['model' => 'didWriting',    'label' => 'I wrote in English today'],
                    ] as $item)
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox"
                                   wire:model="{{ $item['model'] }}"
                                   class="w-4 h-4 rounded border-slate-600 bg-slate-800 text-violet-500 focus:ring-violet-500/30 cursor-pointer">
                            <span class="text-sm text-slate-300 group-hover:text-white transition-colors">{{ $item['label'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- What was difficult --}}
            <div>
                <label for="reflection-difficult" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">
                    What was difficult today?
                </label>
                <textarea id="reflection-difficult"
                          wire:model="whatWasDifficult"
                          rows="2"
                          maxlength="500"
                          placeholder="e.g. Past tense was confusing..."
                          class="w-full bg-slate-800/60 border border-slate-700 rounded-xl px-4 py-3 text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-violet-500/60 focus:ring-1 focus:ring-violet-500/30 resize-none transition-colors"></textarea>
            </div>

            {{-- New expression --}}
            <div>
                <label for="reflection-expression" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">
                    New expression I want to remember
                </label>
                <input type="text"
                       id="reflection-expression"
                       wire:model="newExpression"
                       maxlength="300"
                       placeholder="e.g. 'I'm looking forward to...'"
                       class="w-full bg-slate-800/60 border border-slate-700 rounded-xl px-4 py-3 text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-violet-500/60 focus:ring-1 focus:ring-violet-500/30 transition-colors">
            </div>

            {{-- Submit --}}
            <button wire:click="save"
                    wire:loading.attr="disabled"
                    class="w-full ds-btn ds-btn-md bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white font-bold shadow-lg shadow-violet-600/20 transition-all">
                <span wire:loading.remove wire:target="save">Save Reflection</span>
                <span wire:loading wire:target="save">Saving...</span>
            </button>

        </div>
    @endif

</div>
