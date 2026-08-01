<div class="bg-slate-900 border border-slate-800 rounded-lg p-6 max-w-2xl">
    <h3 class="text-lg font-semibold text-slate-100 mb-6">Add New Word</h3>

    @if (session()->has('message'))
        <div class="mb-4 p-3 rounded bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit="save" class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Word *</label>
                <input wire:model="word" type="text" class="w-full bg-slate-950 border border-slate-800 rounded-md py-2 px-3 text-slate-200 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" required>
                @error('word') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Meaning *</label>
                <input wire:model="meaning" type="text" class="w-full bg-slate-950 border border-slate-800 rounded-md py-2 px-3 text-slate-200 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" required>
                @error('meaning') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Pronunciation</label>
                <input wire:model="pronunciation" type="text" class="w-full bg-slate-950 border border-slate-800 rounded-md py-2 px-3 text-slate-200 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Part of Speech</label>
                <select wire:model="part_of_speech" class="w-full bg-slate-950 border border-slate-800 rounded-md py-2 px-3 text-slate-200 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
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
                <label class="block text-sm font-medium text-slate-300 mb-1">Synonyms</label>
                <input wire:model="synonyms" type="text" class="w-full bg-slate-950 border border-slate-800 rounded-md py-2 px-3 text-slate-200 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Antonyms</label>
                <input wire:model="antonyms" type="text" class="w-full bg-slate-950 border border-slate-800 rounded-md py-2 px-3 text-slate-200 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1">Personal Note / Context</label>
            <textarea wire:model="personal_note" rows="2" class="w-full bg-slate-950 border border-slate-800 rounded-md py-2 px-3 text-slate-200 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"></textarea>
        </div>

        <div class="pt-2 flex justify-end">
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-bold py-2 px-6 rounded-md transition-colors">
                Save Word
            </button>
        </div>
    </form>
</div>
