<div class="ds-card-nested">
    <form wire:submit="saveWord" class="space-y-4 mb-6">
        <div>
            <label class="ds-label">New Word</label>
            <input type="text" wire:model="word" class="ds-input font-bold" required>
        </div>
        
        <div>
            <label class="ds-label">Translation / Meaning</label>
            <input type="text" wire:model="translation" class="ds-input" required>
        </div>
        
        <div>
            <label class="ds-label">Part of Speech</label>
            <select wire:model="part_of_speech" class="ds-select" required>
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
            <label class="ds-label">Example from text (Optional)</label>
            <textarea wire:model="example_sentence" rows="2" class="ds-textarea text-sm"></textarea>
        </div>
        
        <button type="submit" class="w-full ds-btn ds-btn-md ds-btn-secondary !text-emerald-400">
            + Add to Vocabulary
        </button>
    </form>
    
    @if(count($recentlyAdded) > 0)
        <div class="border-t border-slate-800 pt-4">
            <h4 class="ds-eyebrow mb-3">Extracted Words</h4>
            <ul class="space-y-2">
                @foreach($recentlyAdded as $word)
                    <li class="ds-card p-2 text-sm flex justify-between items-center">
                        <span class="font-bold text-slate-200">{{ $word->word }}</span>
                        <span class="text-emerald-400 text-xs">&#10003; Added</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
