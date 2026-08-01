<div class="bg-slate-900 border border-slate-800 rounded-lg p-6">
    <form wire:submit="saveWord" class="space-y-4 mb-6">
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1">New Word</label>
            <input type="text" wire:model="word" class="w-full bg-slate-950 border border-slate-800 rounded-md py-2 px-3 text-slate-200 focus:outline-none focus:border-emerald-500 font-bold" required>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1">Translation / Meaning</label>
            <input type="text" wire:model="translation" class="w-full bg-slate-950 border border-slate-800 rounded-md py-2 px-3 text-slate-200 focus:outline-none focus:border-emerald-500" required>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1">Part of Speech</label>
            <select wire:model="part_of_speech" class="w-full bg-slate-950 border border-slate-800 rounded-md py-2 px-3 text-slate-200 focus:outline-none focus:border-emerald-500" required>
                <option value="Noun">Noun</option>
                <option value="Verb">Verb</option>
                <option value="Adjective">Adjective</option>
                <option value="Adverb">Adverb</option>
                <option value="Pronoun">Pronoun</option>
                <option value="Preposition">Preposition</option>
                <option value="Conjunction">Conjunction</option>
                <option value="Phrase">Phrase</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1">Example from text (Optional)</label>
            <textarea wire:model="example_sentence" rows="2" class="w-full bg-slate-950 border border-slate-800 rounded-md py-2 px-3 text-slate-200 focus:outline-none focus:border-emerald-500 text-sm"></textarea>
        </div>
        
        <button type="submit" class="w-full bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-400 font-bold py-2.5 px-4 rounded-md transition-colors border border-emerald-500/30">
            + Add to Vocabulary
        </button>
    </form>
    
    @if(count($recentlyAdded) > 0)
        <div class="border-t border-slate-800 pt-4">
            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Extracted Words</h4>
            <ul class="space-y-2">
                @foreach($recentlyAdded as $word)
                    <li class="bg-slate-950 rounded border border-slate-800 p-2 text-sm flex justify-between items-center">
                        <span class="font-bold text-slate-200">{{ $word->word }}</span>
                        <span class="text-emerald-400 text-xs">&#10003; Added</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
