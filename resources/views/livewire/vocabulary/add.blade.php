<div class="ds-card p-6 max-w-2xl">
    <h3 class="ds-section-title mb-6">Add New Word</h3>

    @if (session()->has('message'))
        <div class="mb-4 p-3 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit="save" class="space-y-4">

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="ds-label">Word *</label>
                <input wire:model="word" type="text" class="ds-input" required>
                @error('word') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="ds-label">Meaning *</label>
                <input wire:model="meaning" type="text" class="ds-input" required>
                @error('meaning') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="ds-label">Pronunciation</label>
                <input wire:model="pronunciation" type="text" class="ds-input">
            </div>
            <div>
                <label class="ds-label">Part of Speech</label>
                <select wire:model="part_of_speech" class="ds-select">
                    <option value="">Select...</option>
                    <option value="noun">Noun</option>
                    <option value="verb">Verb</option>
                    <option value="adjective">Adjective</option>
                    <option value="adverb">Adverb</option>
                    <option value="phrase">Phrase</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="ds-label">Synonyms</label>
                <input wire:model="synonyms" type="text" class="ds-input">
            </div>
            <div>
                <label class="ds-label">Antonyms</label>
                <input wire:model="antonyms" type="text" class="ds-input">
            </div>
        </div>

        <div>
            <label class="ds-label">Personal Note / Context</label>
            <textarea wire:model="personal_note" rows="2" class="ds-textarea"></textarea>
        </div>

        <div class="pt-2 flex justify-end">
            <button type="submit" class="ds-btn ds-btn-md ds-btn-primary">
                Save Word
            </button>
        </div>

    </form>
</div>
