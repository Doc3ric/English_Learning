<div class="max-w-4xl mx-auto">
    <x-slot:header>
        Add Reading Article
    </x-slot>

    <div class="mb-6">
        <a href="{{ route('reading') }}" class="text-slate-400 hover:text-emerald-400 text-sm flex items-center gap-1 transition-colors">
            &larr; Back to Reading Tracker
        </a>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-lg p-8">
        <form wire:submit="save" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Article Title</label>
                <input type="text" wire:model="title" class="w-full bg-slate-950 border border-slate-800 rounded-md py-2 px-3 text-slate-200 focus:outline-none focus:border-emerald-500" required placeholder="e.g. The History of Space Exploration">
                @error('title') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Level</label>
                    <select wire:model="level" class="w-full bg-slate-950 border border-slate-800 rounded-md py-2 px-3 text-slate-200 focus:outline-none focus:border-emerald-500" required>
                        <option value="Beginner">Beginner</option>
                        <option value="Intermediate">Intermediate</option>
                        <option value="Advanced">Advanced</option>
                        <option value="CEFR A2">CEFR A2</option>
                        <option value="CEFR B1">CEFR B1</option>
                        <option value="CEFR B2">CEFR B2</option>
                        <option value="CEFR C1">CEFR C1</option>
                        <option value="CEFR C2">CEFR C2</option>
                    </select>
                    @error('level') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Target IELTS Band (Optional)</label>
                    <input type="number" step="0.5" min="0" max="9" wire:model="target_band" class="w-full bg-slate-950 border border-slate-800 rounded-md py-2 px-3 text-slate-200 focus:outline-none focus:border-emerald-500" placeholder="e.g. 6.5">
                    @error('target_band') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Recommended Time (minutes)</label>
                    <input type="number" min="1" wire:model="recommended_time_minutes" class="w-full bg-slate-950 border border-slate-800 rounded-md py-2 px-3 text-slate-200 focus:outline-none focus:border-emerald-500" required>
                    @error('recommended_time_minutes') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Source URL (Optional)</label>
                <input type="url" wire:model="source_url" class="w-full bg-slate-950 border border-slate-800 rounded-md py-2 px-3 text-slate-200 focus:outline-none focus:border-emerald-500" placeholder="https://...">
                @error('source_url') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Full Article Text</label>
                <textarea wire:model="full_text" rows="15" class="w-full bg-slate-950 border border-slate-800 rounded-md py-3 px-4 text-slate-200 focus:outline-none focus:border-emerald-500 font-serif leading-relaxed" required placeholder="Paste the full article content here..."></textarea>
                @error('full_text') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="pt-4 border-t border-slate-800 flex justify-end">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-bold py-3 px-8 rounded-md transition-colors shadow">
                    Save Article & Add Questions &rarr;
                </button>
            </div>
        </form>
    </div>
</div>
