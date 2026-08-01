<div>
    <h3 class="text-lg font-semibold text-slate-100 mb-6">Review (Last 7 Days)</h3>

    @if($words->isEmpty())
        <div class="bg-slate-900 border border-slate-800 rounded-lg p-12 text-center">
            <h4 class="text-slate-100 font-semibold text-lg mb-2">Nothing to review right now.</h4>
            <p class="text-slate-400">Words you learn will appear here for 7 days so you can review them.</p>
        </div>
    @else
        <div class="grid gap-4 md:grid-cols-2">
            @foreach($words as $word)
                <div class="bg-slate-900 border border-slate-800 rounded-lg p-5" wire:key="review-{{ $word->id }}">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h4 class="text-xl font-bold text-slate-100">{{ $word->word }}</h4>
                            <p class="text-sm text-slate-400 mt-1">{{ $word->meaning }}</p>
                        </div>
                        <span class="text-xs text-slate-500 bg-slate-950 px-2 py-1 rounded border border-slate-800">{{ $word->created_at->diffForHumans() }}</span>
                    </div>

                    <div class="bg-slate-950 p-3 rounded text-sm text-slate-300 italic mb-4 border border-slate-800/50">
                        "{{ $word->example_sentence }}"
                    </div>

                    <div class="flex gap-2 mt-4">
                        <button class="flex-1 bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium py-1.5 px-3 rounded-md transition-colors border border-slate-700 text-sm">
                            Needs Review
                        </button>
                        <button wire:click="markMastered({{ $word->id }})" class="flex-1 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 font-medium py-1.5 px-3 rounded-md transition-colors border border-emerald-500/20 text-sm">
                            Still Remember (Master)
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
